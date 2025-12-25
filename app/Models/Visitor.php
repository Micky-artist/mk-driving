<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'ip_address',
        'user_agent',
        'device_type',
        'device_name',
        'browser',
        'platform',
        'country',
        'city',
        'is_registered_user',
        'user_id',
        'first_visit_at',
        'last_visit_at',
        'total_visits',
    ];

    protected $casts = [
        'first_visit_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'is_registered_user' => 'boolean',
    ];

    /**
     * Get the user associated with this visitor (if registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate or retrieve visitor ID from request.
     */
    public static function getOrCreateVisitorId($request): string
    {
        // Generate a unique visitor ID based on IP, User Agent, and other factors
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        
        // Create a fingerprint from available data
        $fingerprint = md5($ip . $userAgent . ($request->header('Accept-Language') ?? ''));
        
        return substr($fingerprint, 0, 32); // First 32 characters for uniqueness
    }

    /**
     * Detect device type from user agent.
     */
    public static function detectDevice($userAgent): array
    {
        $deviceType = 'desktop';
        $deviceName = 'Unknown';
        $browser = 'Unknown';
        $platform = 'Unknown';

        // Simple device detection
        if (preg_match('/(android|iphone|ipad|ipod|mobile|phone)/i', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/(tablet|ipad)/i', $userAgent)) {
            $deviceType = 'tablet';
        }

        // Browser detection
        if (preg_match('/Chrome\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Chrome ' . $matches[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Firefox ' . $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/i', $userAgent, $matches)) {
            $browser = 'Safari ' . $matches[1];
        }

        // Platform detection
        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iOS/i', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'device_name' => $deviceName,
            'browser' => $browser,
            'platform' => $platform,
        ];
    }

    /**
     * Get country from IP address (simplified).
     */
    public static function getCountryFromIP($ip): ?string
    {
        // This is a simplified implementation
        // In production, you'd use a proper IP geolocation service
        $ipData = @file_get_contents("http://ip-api.com/json/{$ip}");
        
        if ($ipData) {
            $data = json_decode($ipData, true);
            return $data['countryCode'] ?? null;
        }

        return null;
    }

    /**
     * Get city from IP address (simplified).
     */
    public static function getCityFromIP($ip): ?string
    {
        $ipData = @file_get_contents("http://ip-api.com/json/{$ip}");
        
        if ($ipData) {
            $data = json_decode($ipData, true);
            return $data['city'] ?? null;
        }

        return null;
    }
}
