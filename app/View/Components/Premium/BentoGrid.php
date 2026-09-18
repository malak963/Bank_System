<?php

namespace App\View\Components\Premium;

use Illuminate\View\Component;

class BentoGrid extends Component
{
    public array $items;
    public bool $parallax;

    public function __construct(array $items = [], bool $parallax = true)
    {
        $this->items = $items;
        $this->parallax = $parallax;
    }

    public function render()
    {
        return view('components.premium.bento-grid');
    }
}
