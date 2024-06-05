<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Brands extends Component
{
    public $brand;

    public $width;

    public $height;

    public $viewBox;

    public $id;

    public $class;

    public function __construct(
        $brand = null,
        $width = 24,
        $height = 24,
        $viewBox = '24 24',
        $id = null,
        $class = null
    ) {
        $this->brand = $brand;
        $this->width = $width;
        $this->height = $height;
        $this->viewBox = $viewBox;
        $this->id = $id ?? '';
        $this->class = $class ?? '';
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
<svg xmlns="http://www.w3.org/2000/svg" width="{{ $width }}" height="{{ $height }}"
    viewBox="0 0 {{ $viewBox }}" id="{{ $id }}"
    {{ $attributes->merge(['class' => "icon icon-$brand $class"]) }}>
    @includeFirst(["icons.brands.$brand", "icons.missing-icon"])
</svg>
blade;
    }
}
