<?php

namespace App\Traits;

use App\Enums\ClanInviteStatus;
use App\Models\Clan;
use App\Models\ClanUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasClan
{
    public function clans(): BelongsToMany
    {
        return $this->belongsToMany(Clan::class)
            ->using(ClanUser::class)
            ->withPivot(ClanUser::FIELDS)
            ->withTimestamps();
    }

    public function ownedClan(): HasOne
    {
        return $this->hasOne(Clan::class, 'owner_id');
    }

    public function myClan(): ?Clan
    {
        if ($this->hasClan()) {
            return $this->clans()->where('status', ClanInviteStatus::ACCEPTED)->first();
        }

        return null;
    }

    public function hasClan(): bool
    {
        return $this->clans()->where('status', ClanInviteStatus::ACCEPTED)->exists();
    }

    public function isClanOwner(): bool
    {
        return $this->ownedClan()->exists();
    }

    public function clanInvites(): Clan
    {
        return $this->clans()->where('status', ClanInviteStatus::PENDING)->first();
    }

    public function canCreateClan(): bool
    {
        return ! $this->hasClan();
    }

    public function joinClan(Clan $clan): void
    {
        if ($this->hasClanInviteToClan($clan)) {
            $this->rejectAllClanInvites($clan);

            $this->clans()->attach($clan, ['status' => ClanInviteStatus::ACCEPTED]);
        }
    }

    public function leaveClan(Clan $clan): int
    {
        if ($this->isInClan($clan) && ! $this->ownsClan($clan)) {
            return $this->clans()->update(
                $clan->id,
                ['status' => ClanInviteStatus::LEFT]
            );
        }
    }

    public function acceptClanInvite(Clan $clan): void
    {
        if ($this->canAcceptClanInvite($clan)) {
            $this->joinClan($clan);
        }
    }

    public function canAcceptClanInvite(Clan $clan): bool
    {
        return ! $this->hasClan() && $this->hasClanInviteToClan($clan);
    }

    public function hasClanInviteToClan(Clan $clan): bool
    {
        return $this->clans()
            ->where('id', $clan->id)
            ->where('status', ClanInviteStatus::PENDING)->exists();
    }

    public function canBeInvitedToClan(Clan $clan): bool
    {
        return ! $this->hasClan() && ! $this->clanIsBlocked($clan);
    }

    public function rejectClanInvite(Clan $clan): int
    {
        if ($this->hasClanInviteToClan($clan)) {
            return $this->clans()->updateExistingPivot(
                $clan->id,
                ['status' => ClanInviteStatus::REJECTED]
            );
        }
    }

    public function rejectAllClanInvites(?Clan $ignoreClan = null): bool
    {
        if ($ignoreClan) {
            return $this->clans()->where('status', ClanInviteStatus::PENDING)
                ->where('id', '!=', $ignoreClan->id)
                ->update(['status' => ClanInviteStatus::REJECTED]);
        }

        return $this->clans()->where('status', ClanInviteStatus::PENDING)
            ->update(['status' => ClanInviteStatus::REJECTED]);
    }

    public function createClan(string $name, string $tag, string $description): ?Clan
    {
        if ($this->canCreateClan()) {
            $this->rejectAllClanInvites();

            $clan = Clan::create([
                'name' => $name,
                'tag' => $tag,
                'description' => $description,
                'owner_id' => $this->id,
            ]);

            $this->clans()->attach($clan->id, ['status' => ClanInviteStatus::ACCEPTED]);

            return $clan;
        }

        return null;
    }

    public function ownsClan(Clan $clan): bool
    {
        return $this->isClanOwner() && $this->ownedClan()->id == $clan->id;
    }

    public function isInClan(Clan $clan): bool
    {
        return $this->clans()->where('id', $clan->id)->exists();
    }

    public function canTransferClan(Clan $clan): bool
    {
        return $this->ownsClan($clan);
    }

    public function transferClan(Clan $clan, User $user): int
    {
        if ($this->canTransferClan($clan) && $user->isInClan($clan)) {
            return $this->clans()->update(
                $clan->id,
                [
                    'owner_id' => $user->id,
                ]
            );
        }
    }

    public function updateClan(Clan $clan, string $name, string $tag, string $description): int
    {
        if ($this->ownsClan($clan)) {
            return $this->clans()->update(
                $clan->id,
                [
                    'name' => $name,
                    'tag' => $tag,
                    'description' => $description,
                ]
            );
        }
    }

    public function deleteClan(Clan $clan): int
    {
        if ($this->ownsClan($clan)) {
            return $this->clans()->delete($clan);
        }
    }

    public function cancelClanInvite(Clan $clan, User $user): int
    {
        if ($this->ownsClan($clan) && $user->hasClanInviteToClan($clan)) {
            return $user->clans()->updateExistingPivot(
                $clan->id,
                ['status' => ClanInviteStatus::CANCELLED]
            );
        }
    }

    public function clanIsBlocked(Clan $clan): bool
    {
        return $this->clans()
            ->where('id', $clan->id)
            ->where('status', ClanInviteStatus::BLOCKED)
            ->exists();
    }

    public function blockClanInviteFromClan(Clan $clan): ?int
    {
        if ($this->hasClanInviteToClan($clan)) {
            return $this->clans()->updateExistingPivot(
                $clan->id,
                ['status' => ClanInviteStatus::BLOCKED]
            );
        } else {
            return $this->clans()->attach($clan, ['status' => ClanInviteStatus::BLOCKED]);
        }
    }

    public function unblockClanInviteFromClan(Clan $clan): int
    {
        if ($this->clanIsBlocked($clan)) {
            return $this->clans()->detach($clan);
        }
    }
}
