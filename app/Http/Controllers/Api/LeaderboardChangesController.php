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
                        'name' => $entry['user']['first_name'] . ' ' . $entry['user']['last_name'],
                        'message' => __('quiz.companion.justEarnedPoints', ['name' => $entry['user']['first_name'], 'points' => $entry['points']]),
                        'points_change' => '+' . $entry['points'] . ' points',
                        'time_ago' => $entry['last_activity'] ? timeDiffForHumans($entry['last_activity']) : 'Recently',
                        'timestamp' => $entry['last_activity'] ? $entry['last_activity']->timestamp : now()->timestamp,
                        'type' => 'points_earned',
                        'points' => $entry['points'],
                        'leaderboard_score' => $entry['points']
                    ];
                }
            }

            // Always return at least empty structure for frontend
            if (empty($changes)) {
                $changes = [
                    [
                        'id' => null,
                        'name' => null,
                        'message' => 'No recent activity',
                        'points_change' => null,
                        'time_ago' => null,
                        'timestamp' => 0,
                        'type' => 'no_activity',
                        'points' => 0,
                        'leaderboard_score' => 0
                    ]
                ];
            }

            // Sort by most recent (using timestamp)
            usort($changes, function ($a, $b) {
                return ($b['timestamp'] ?? 0) - ($a['timestamp'] ?? 0);
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
