<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Welcome extends Component
{
    public $activosCount;
    public $ubicacionesCount;

    public function __construct($activosCount)
    {
        $this->activosCount = $activosCount;
        $this->ubicacionesCount = $ubicacionesCount;
        $this->serviciosCount = $serviciosCount;
    }

    public function render()
    {
        return view('components.welcome');
    }
}
