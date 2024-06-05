<?php

namespace App\Livewire;

use App\Contracts\GetsMarkdownFileContract;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Markdown extends Component
{
    public $markdown;

    public function mount(string $markdown, GetsMarkdownFileContract $compiler)
    {
        $this->markdown = $compiler($markdown);
    }
}
