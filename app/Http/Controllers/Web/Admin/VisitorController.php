<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    /**
     * Display visitor analytics dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_visitors' => Visitor::count(),
            'unique_visitors' => Visitor::distinct('visitor_id')->count(),
            'registered_visitors' => Visitor::where('is_registered_user', true)->count(),
            'anonymous_visitors' => Visitor::where('is_registered_user', false)->count(),
            'mobile_visitors' => Visitor::where('device_type', 'mobile')->count(),
            'desktop_visitors' => Visitor::where('device_type', 'desktop')->count(),
            'tablet_visitors' => Visitor::where('device_type', 'tablet')->count(),
            'visits_today' => Visitor::whereDate('last_visit_at', today())->count(),
            'visits_this_month' => Visitor::whereMonth('last_visit_at', now()->month)->count(),
        ];

        $recentVisitors = Visitor::with('user')
            ->orderBy('last_visit_at', 'desc')
            ->take(50)
            ->get();

        $topCountries = Visitor::whereNotNull('country')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $topDevices = Visitor::selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        return view('admin.visitors.index', compact('stats', 'recentVisitors', 'topCountries', 'topDevices'));
    }

    /**
     * Track visitor information.
     */
    public function track(Request $request): JsonResponse
    {
        $visitorId = Visitor::getOrCreateVisitorId($request);
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Detect device information
        $deviceInfo = Visitor::detectDevice($userAgent);
        
        // Get geolocation data
        $country = Visitor::getCountryFromIP($ip);
        $city = Visitor::getCityFromIP($ip);

        // Check if this is a registered user
        $user = auth()->check() ? auth()->user() : null;
        $isRegisteredUser = $user !== null;

        // Create or update visitor record
        $visitor = Visitor::updateOrCreate(
            ['visitor_id' => $visitorId],
            [
                'ip_address' => $ip,
                'user_agent' => substr($userAgent, 0, 500),
                'device_type' => $deviceInfo['device_type'],
                'device_name' => $deviceInfo['device_name'],
                'browser' => $deviceInfo['browser'],
                'platform' => $deviceInfo['platform'],
                'country' => $country,
                'city' => $city,
                'is_registered_user' => $isRegisteredUser,
                'user_id' => $isRegisteredUser ? $user->id : null,
                'first_visit_at' => now(),
                'last_visit_at' => now(),
                'total_visits' => DB::raw('COALESCE(total_visits + 1, 1)'),
            ]
        );

        return response()->json([
            'success' => true,
            'visitor_id' => $visitorId,
            'message' => 'Visitor tracked successfully'
        ]);
    }

    /**
     * Get visitor analytics data.
     */
    public function analytics(Request $request): JsonResponse
    {
        $period = $request->get('period', '7'); // Default to 7 days
        
        $startDate = now()->subDays($period);
        
        $data = Visitor::where('last_visit_at', '>=', $startDate)
            ->selectRaw('DATE(last_visit_at) as date, COUNT(*) as visits')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => $data,
            'period' => $period,
            'start_date' => $startDate->toDateString(),
        ]);
    }

    /**
     * Export visitor data.
     */
    public function export(): JsonResponse
    {
        $visitors = Visitor::with('user')
            ->orderBy('last_visit_at', 'desc')
            ->get();

        $csvData = [];
        foreach ($visitors as $visitor) {
            $csvData[] = [
                'Visitor ID' => $visitor->visitor_id,
                'IP Address' => $visitor->ip_address,
                'Device Type' => $visitor->device_type,
                'Device Name' => $visitor->device_name,
                'Browser' => $visitor->browser,
                'Platform' => $visitor->platform,
                'Country' => $visitor->country,
                'City' => $visitor->city,
                'Is Registered User' => $visitor->is_registered_user ? 'Yes' : 'No',
                'User ID' => $visitor->user_id,
                'First Visit' => $visitor->first_visit_at,
                'Last Visit' => $visitor->last_visit_at,
                'Total Visits' => $visitor->total_visits,
            ];
        }

        return response()->json([
            'data' => $csvData,
            'filename' => 'visitors_export_' . now()->format('Y-m-d_H-i-s') . '.csv'
        ]);
    }
}
