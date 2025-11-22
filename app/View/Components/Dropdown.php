<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{
    public $align;

    public function __construct($align = 'right')
    {
        $this->align = $align;
    }

    public function render()
    {
        return view('components.dropdown');
    }
}
