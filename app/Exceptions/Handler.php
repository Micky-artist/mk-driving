<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Facades\Inertia;
use App\View\Components\ErrorBoundary;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests with JSON response
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
            ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
        }

        // Handle 403 errors with Inertia for admin routes
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 403) {
            if ($request->is('admin*')) {
                return Inertia::render('Errors/403', [
                    'status' => 403,
                    'message' => 'Access Denied. You do not have permission to access this page.'
                ])->toResponse($request)->setStatusCode(403);
            }
        }

        // Use our ErrorBoundary component for all other exceptions
        return ErrorBoundary::renderException($e);
    }
}
