<?php

namespace App\Services;

use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeviceTrackingService
{
    /**
     * Enhanced device fingerprinting for better visitor/user differentiation.
     */
    public static function generateDeviceFingerprint(Request $request): array
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $acceptLanguage = $request->header('Accept-Language') ?? '';
        $acceptEncoding = $request->header('Accept-Encoding') ?? '';
        
        // Enhanced fingerprinting with multiple data points
        $fingerprintData = [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'accept_language' => $acceptLanguage,
            'accept_encoding' => $acceptEncoding,
            'screen_resolution' => $request->header('Screen-Resolution') ?? '',
            'timezone' => $request->header('Timezone') ?? '',
        ];
        
        // Generate primary fingerprint
        $primaryFingerprint = md5(serialize($fingerprintData));
        
        // Generate device-specific fingerprint (for device tracking)
        $deviceFingerprint = md5($ip . $userAgent . $acceptLanguage);
        
        return [
            'visitor_id' => substr($primaryFingerprint, 0, 32),
            'device_fingerprint' => substr($deviceFingerprint, 0, 64),
            'session_id' => session()->getId() ?? uniqid('session_', true),
        ];
    }

    /**
     * Determine if device belongs to a registered user.
     */
    public static function determineDeviceOwnership(array $fingerprints): array
    {
        $visitorId = $fingerprints['visitor_id'];
        $deviceFingerprint = $fingerprints['device_fingerprint'];
        
        // Check if this device has been seen before
        $existingVisitors = Visitor::where('device_fingerprint', $deviceFingerprint)
            ->orderBy('last_visit_at', 'desc')
            ->get();
            
        $deviceOwnership = [
            'is_known_device' => false,
            'previous_users' => [],
            'current_user' => null,
        ];
        
        if ($existingVisitors->isNotEmpty()) {
            $deviceOwnership['is_known_device'] = true;
            
            // Get all users who have used this device
            $previousUsers = $existingVisitors
                ->where('is_registered_user', true)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->toArray();
                
            $deviceOwnership['previous_users'] = $previousUsers;
            
            // Check if current user matches previous users
            if (Auth::check()) {
                $currentUser = Auth::id();
                if (in_array($currentUser, $previousUsers)) {
                    $deviceOwnership['current_user'] = $currentUser;
                }
            }
        }
        
        return $deviceOwnership;
    }

    /**
     * Track visitor with enhanced device analysis.
     */
    public static function trackVisitor(Request $request): Visitor
    {
        $fingerprints = self::generateDeviceFingerprint($request);
        $deviceOwnership = self::determineDeviceOwnership($fingerprints);
        
        $user = Auth::check() ? Auth::user() : null;
        $isRegisteredUser = $user !== null;
        
        $visitorData = [
            'visitor_id' => $fingerprints['visitor_id'],
            'device_fingerprint' => $fingerprints['device_fingerprint'],
            'session_id' => $fingerprints['session_id'],
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 500),
            'device_type' => Visitor::detectDevice($request->userAgent())['device_type'],
            'device_name' => Visitor::detectDevice($request->userAgent())['device_name'],
            'browser' => Visitor::detectDevice($request->userAgent())['browser'],
            'platform' => Visitor::detectDevice($request->userAgent())['platform'],
            'country' => Visitor::getCountryFromIP($request->ip()),
            'city' => Visitor::getCityFromIP($request->ip()),
            'is_registered_user' => $isRegisteredUser,
            'user_id' => $isRegisteredUser ? $user->id : null,
            'is_known_device' => $deviceOwnership['is_known_device'],
            'device_first_seen_at' => $deviceOwnership['is_known_device'] ? null : now(),
            'first_visit_at' => now(),
            'last_visit_at' => now(),
            'total_visits' => \DB::raw('COALESCE(total_visits + 1, 1)'),
        ];
        
        return Visitor::updateOrCreate(
            ['visitor_id' => $fingerprints['visitor_id']],
            $visitorData
        );
    }

    /**
     * Get visitor statistics with device ownership analysis.
     */
    public static function getVisitorStats(): array
    {
        return [
            'unregistered_visitors' => Visitor::where('is_registered_user', false)->count(),
            'registered_visitors' => Visitor::where('is_registered_user', true)->count(),
            'total_visitors' => Visitor::count(),
            'unique_visitors' => Visitor::distinct('visitor_id')->count(),
            'unique_devices' => Visitor::distinct('device_fingerprint')->count(),
            'known_devices' => Visitor::where('is_known_device', true)->count(),
            'new_devices_today' => Visitor::where('device_first_seen_at', '>=', now()->startOfDay())->count(),
            'devices_used_by_multiple_users' => Visitor::selectRaw('device_fingerprint, COUNT(DISTINCT user_id) as user_count')
                ->whereNotNull('user_id')
                ->groupBy('device_fingerprint')
                ->having('user_count', '>', 1)
                ->count(),
        ];
    }
}
