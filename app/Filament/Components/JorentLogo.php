<?php

namespace App\Filament\Components;

use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class JorentLogo extends Component
{
    public function __construct(
        public string $size = 'md',
        public bool $animated = false,
        public string $variant = 'full'
    ) {}

    public function render(): View
    {
        return view('components.jorent-logo');
    }

    public static function make(string $size = 'md', bool $animated = false, string $variant = 'full'): static
    {
        return new static($size, $animated, $variant);
    }
}
