<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'nickname' => $this->nickname,
            'rank' => $this->rank,
            'elo' => $this->elo,
            'monthly_rank' => $this->monthly_rank,
            'monthly_elo' => $this->monthly_elo,
        ];
    }
}
