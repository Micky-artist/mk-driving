<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ErrorBoundary extends Component
{
    /**
     * The error message to display.
     *
     * @var string
     */
    public $message;

    /**
     * Create a new component instance.
     *
     * @param string|null $message
     * @return void
     */
    public function __construct($message = null)
    {
        $this->message = $message ?? 'Something went wrong. Please try again later.';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('components.error-boundary');
    }

    /**
     * Handle the exception and return the error view.
     *
     * @param \Throwable $e
     * @return \Illuminate\View\View
     */
    public static function renderException(\Throwable $e): View
    {
        return view('components.error-boundary', [
            'message' => $e->getMessage(),
            'exception' => $e
        ]);
    }
}
