<?php

namespace App\View\Components\Premium;

use Illuminate\View\Component;

class GlassCard extends Component
{
    public string $title;
    public string $gradient;
    public string $borderColor;

    public function __construct(
        string $title = '',
        string $gradient = 'from-emerald-500/20 to-blue-500/20',
        string $borderColor = 'white/20'
    ) {
        $this->title = $title;
        $this->gradient = $gradient;
        $this->borderColor = $borderColor;
    }

    public function render()
    {
        return view('components.premium.glass-card');
    }
}
