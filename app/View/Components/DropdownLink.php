<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DropdownLink extends Component
{
    public $href;
    public $method;
    public $as;

    public function __construct($href = null, $method = 'GET', $as = 'a')
    {
        $this->href = $href;
        $this->method = $method;
        $this->as = $as;
    }

    public function render()
    {
        return view('components.dropdown-link');
    }
}
