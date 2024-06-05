<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icons extends Component
{
    public $icon;

    public $width;

    public $height;

    public $viewBox;

    public $fill;

    public $strokeWidth;

    public $id;

    public $class;

    public function __construct(
        $icon = null,
        $width = 24,
        $height = 24,
        $viewBox = '24 24',
        $fill = 'none', // currentColor, none
        $strokeWidth = 2,
        $id = null,
        $class = null
    ) {
        $this->icon = $icon;
        $this->width = $width;
        $this->height = $height;
        $this->viewBox = $viewBox;
        $this->fill = $fill;
        $this->strokeWidth = $strokeWidth;
        $this->id = $id ?? '';
        $this->class = $class ?? '';
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $width }}" height="{{ $height }}"
    viewBox="0 0 {{ $viewBox }}" fill="{{ $fill }}" stroke="currentColor" stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round" stroke-linejoin="round" id="{{ $id }}"
    {{ $attributes->merge(['class' => "icon icon-$icon $class"]) }}>
    @includeFirst(["icons.$icon", "icons.missing-icon"])
</svg>
blade;
    }
}
