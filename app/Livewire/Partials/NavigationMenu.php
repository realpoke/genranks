<?php

namespace App\Livewire\Partials;

use App\Contracts\Auth\LogoutUserContract;
use App\Contracts\GetsLatestGenLinkDownloadLinkContract;
use App\Traits\WithLimits;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class NavigationMenu extends Component
{
    use WithLimits;

    public ?string $name;

    public ?string $email;

    #[On('update-navigation-name')]
    public function updateName($name)
    {
        $this->name = $name;
    }

    #[On('update-navigation-email')]
    public function updateEmail($email)
    {
        $this->email = $email;
    }

    public function logout(LogoutUserContract $logouter)
    {
        $loggedOut = $logouter();

        $this->redirectRoute('home', navigate: true);
    }

    public function downloadGenLink(GetsLatestGenLinkDownloadLinkContract $downloader)
    {
        $this->limitTo(1, 'download.genlink', 'download');

        Cache::add('genlink-download-link', $downloader(), 30);
        $link = Cache::get('genlink-download-link');

        if ($link == null) {
            return;
        }

        return Response::streamDownload(fn () => $link, 'GenLink.exe');
    }

    public function mount()
    {
        $this->name = $this->user()?->name;
        $this->email = $this->user()?->email;
    }

    #[Computed]
    protected function user()
    {
        return Auth::user();
    }
}
