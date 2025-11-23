<?php

namespace App\Http\Traits;

trait HasNotifications
{
    /**
     * Flash a success notification.
     *
     * @param string $message
     * @param int $duration
     * @return void
     */
    protected function notifySuccess(string $message, int $duration = 5000): void
    {
        $this->notify('success', $message, $duration);
    }

    /**
     * Flash an error notification.
     *
     * @param string $message
     * @param int $duration
     * @return void
     */
    protected function notifyError(string $message, int $duration = 5000): void
    {
        $this->notify('error', $message, $duration);
    }

    /**
     * Flash a warning notification.
     *
     * @param string $message
     * @param int $duration
     * @return void
     */
    protected function notifyWarning(string $message, int $duration = 5000): void
    {
        $this->notify('warning', $message, $duration);
    }

    /**
     * Flash an info notification.
     *
     * @param string $message
     * @param int $duration
     * @return void
     */
    protected function notifyInfo(string $message, int $duration = 5000): void
    {
        $this->notify('info', $message, $duration);
    }

    /**
     * Flash a notification.
     *
     * @param string $type
     * @param string $message
     * @param int $duration
     * @return void
     */
    protected function notify(string $type, string $message, int $duration = 5000): void
    {
        if (request()->expectsJson()) {
            // For API responses
            $notification = [
                'type' => $type,
                'message' => $message,
                'duration' => $duration
            ];
            
            // Add the notification to the response
            $response = response()->json(
                array_merge(
                    request()->wantsJson() ? (array) (request('response') ?? []) : [],
                    ['notification' => $notification]
                )
            );
            
            // If it's an Inertia.js request, add the notification to the response
            if (class_exists('Inertia\Inertia') && \Inertia\Inertia::getShared('flash')) {
                $shared = \Inertia\Inertia::getShared();
                $flash = $shared['flash'] ?? [];
                
                if (!isset($flash['notification']) || !is_array($flash['notification'])) {
                    $flash['notification'] = [];
                }
                
                $flash['notification'][] = $notification;
                \Inertia\Inertia::share('flash', $flash);
            }
            
            // If this is a redirect response, we'll store the notification in the session
            if (method_exists($this, 'redirectTo') || method_exists($this, 'redirectTo')) {
                session()->flash('notification', $notification);
            }
            
            // If this is a JSON response, we'll return it with the notification
            if (request()->wantsJson() || request()->ajax()) {
                return $response;
            }
        } else {
            // For regular web requests
            session()->flash('notification', [
                'type' => $type,
                'message' => $message,
                'duration' => $duration
            ]);
            
            // If this is an Inertia.js request, we'll also share the notification
            if (class_exists('Inertia\Inertia') && \Inertia\Inertia::getShared('flash')) {
                $shared = \Inertia\Inertia::getShared();
                $flash = $shared['flash'] ?? [];
                
                if (!isset($flash['notification']) || !is_array($flash['notification'])) {
                    $flash['notification'] = [];
                }
                
                $flash['notification'][] = [
                    'type' => $type,
                    'message' => $message,
                    'duration' => $duration
                ];
                
                \Inertia\Inertia::share('flash', $flash);
            }
        }
    }
}
