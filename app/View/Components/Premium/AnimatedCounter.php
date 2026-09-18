<?php

namespace App\View\Components\Premium;

use Illuminate\View\Component;

class AnimatedCounter extends Component
{
    public int $target;
    public int $duration;
    public string $prefix;
    public string $suffix;
    public int $decimals;

    public function __construct(
        int $target = 0,
        int $duration = 2000,
        string $prefix = '',
        string $suffix = '',
        int $decimals = 0
    ) {
        $this->target = $target;
        $this->duration = $duration;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->decimals = $decimals;
    }

    public function render()
    {
        return view('components.premium.animated-counter');
    }
}
