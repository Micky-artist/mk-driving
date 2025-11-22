<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AuthSessionStatus extends Component
{
    public $status;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function render()
    {
        return view('components.auth-session-status');
    }
}
