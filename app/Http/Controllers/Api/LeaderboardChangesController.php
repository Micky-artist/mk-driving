<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PointsService;
use Illuminate\Http\JsonResponse;

class LeaderboardChangesController extends Controller
{
    private PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->pointsService = $pointsService;
    }

    public function index(): JsonResponse
    {
        try {
            $leaderboard = $this->pointsService->getLeaderboard(20, 'total');
            $changes = [];
            
            foreach ($leaderboard as $index => $entry) {
                if ($entry['user']['is_robot']) {
                    $changes[] = [
                        'id' => $entry['user']['id'],
                        'user' => $entry['user'],
                        'message' => $entry['user']['first_name'] . ' just earned ' . $entry['points'] . ' points!',
                        'points_change' => '+' . $entry['points'] . ' points',
                        'time_ago' => $entry['last_activity'] ? $entry['last_activity']['time_ago'] : 'Recently',
                        'type' => 'points_earned'
                    ];
                }
            }

            // Sort by most recent
            usort($changes, function ($a, $b) {
                return strtotime($b['time_ago']) - strtotime($a['time_ago']);
            });

            return response()->json([
                'success' => true,
                'changes' => array_slice($changes, 0, 5) // Show last 5 changes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load leaderboard changes'
            ], 500);
        }
    }
}
