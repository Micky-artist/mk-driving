<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputError extends Component
{
    /**
     * The error message.
     *
     * @var string
     */
    public $message;

    /**
     * Create a new component instance.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = null)
    {
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.input-error');
    }
}
