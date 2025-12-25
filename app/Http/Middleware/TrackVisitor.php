<?php

namespace App\Http\Middleware;

use App\Services\DeviceTrackingService;
use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Only track GET requests to avoid tracking API calls, form submissions, etc.
        if ($request->isMethod('GET') && !$this->shouldSkipTracking($request)) {
            $this->trackVisitor($request);
        }

        return $next($request);
    }

    /**
     * Determine if tracking should be skipped for this request.
     */
    private function shouldSkipTracking(Request $request): bool
    {
        // Skip tracking for admin routes, API routes, assets, etc.
        $skipPatterns = [
            'admin/*',
            'api/*',
            'sanctum/*',
            '_debugbar/*',
            'telescope/*',
            'horizon/*',
            'nova/*',
            'livewire/*',
        ];

        $path = $request->path();
        
        foreach ($skipPatterns as $pattern) {
            if (str($path)->is($pattern)) {
                return true;
            }
        }

        // Skip if user agent indicates a bot/crawler
        $userAgent = $request->userAgent();
        $botPatterns = [
            'bot',
            'crawler',
            'spider',
            'scraper',
            'curl',
            'wget',
            'python',
            'java',
            'node',
            'ruby',
            'perl',
            'php',
            'go-http-client',
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Track visitor information with enhanced device analysis.
     */
    private function trackVisitor(Request $request): void
    {
        try {
            // Use enhanced device tracking service
            DeviceTrackingService::trackVisitor($request);
        } catch (\Exception $e) {
            // Log error but don't break request
            \Log::error('Visitor tracking failed: ' . $e->getMessage());
        }
    }
}
