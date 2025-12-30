<?php

/**
 * One-time script to fix subscriptions affected by the duration_days bug
 * 
 * This script identifies and fixes subscriptions that were approved with incorrect
 * end dates due to the duration_days vs duration_in_days field mismatch.
 * 
 * Run: php artisan script:fix_subscription_duration_bug
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class FixSubscriptionDurationBug
{
    public function handle()
    {
        echo "=== Fixing Subscription Duration Bug ===\n";
        
        $startTime = now();
        $fixedCount = 0;
        $errorCount = 0;
        
        try {
            // Find all subscriptions that were likely affected by the bug
            $buggySubscriptions = $this->findBuggySubscriptions();
            
            echo "Found {$buggySubscriptions->count()} potentially buggy subscriptions\n\n";
            
            if ($buggySubscriptions->isEmpty()) {
                echo "No buggy subscriptions found. All good!\n";
                return;
            }
            
            foreach ($buggySubscriptions as $subscription) {
                try {
                    $this->fixSubscription($subscription);
                    $fixedCount++;
                    echo "✓ Fixed subscription ID {$subscription->id} for user {$subscription->user_id}\n";
                } catch (\Exception $e) {
                    $errorCount++;
                    echo "✗ Failed to fix subscription ID {$subscription->id}: {$e->getMessage()}\n";
                    Log::error('Failed to fix subscription in script', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $duration = $startTime->diffInSeconds(now());
            
            echo "\n=== Summary ===\n";
            echo "Fixed: {$fixedCount} subscriptions\n";
            echo "Errors: {$errorCount} subscriptions\n";
            echo "Duration: {$duration} seconds\n";
            echo "Completed at: " . now()->toDateTimeString() . "\n";
            
            if ($fixedCount > 0) {
                echo "\n⚠️  IMPORTANT: Verify the fixes in the admin panel and deploy the code changes before users access the system.\n";
            }
            
        } catch (\Exception $e) {
            echo "Script failed: " . $e->getMessage() . "\n";
            Log::error('Subscription bug fix script failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Find subscriptions that were likely affected by the duration_days bug
     */
    private function findBuggySubscriptions()
    {
        $now = now();
        
        return Subscription::where('status', 'ACTIVE')
            ->where('payment_status', 'COMPLETED')
            ->where(function($query) use ($now) {
                // Case 1: End date is very close to start date (within 1 minute) - clear bug symptom
                $query->whereRaw('TIMESTAMPDIFF(SECOND, starts_at, ends_at) <= 60')
                // Case 2: End date is in past but subscription was approved recently (within last 30 days)
                ->orWhere(function($subQuery) use ($now) {
                    $subQuery->where('ends_at', '<=', $now)
                            ->where('starts_at', '>=', $now->subDays(30));
                });
            })
            ->with(['user', 'plan'])
            ->get();
    }
    
    /**
     * Fix a single subscription by recalculating the correct end date
     */
    private function fixSubscription($subscription)
    {
        $plan = $subscription->plan;
        
        if (!$plan) {
            throw new \Exception("Missing plan for subscription {$subscription->id}");
        }
        
        if (!$plan->duration_in_days) {
            throw new \Exception("Plan {$plan->id} has no duration_in_days set");
        }
        
        // Calculate the correct end date based on when it should have been set
        $correctEndDate = $subscription->starts_at->copy()->addDays($plan->duration_in_days);
        
        // Only fix if the correct end date is actually in the future
        if ($correctEndDate <= now()) {
            throw new \Exception("Corrected end date ({$correctEndDate}) is not in the future");
        }
        
        $oldEndDate = $subscription->ends_at;
        
        // Update the subscription with the correct end date
        $subscription->update([
            'ends_at' => $correctEndDate,
            'metadata' => array_merge($subscription->metadata ?? [], [
                'duration_bug_fixed' => true,
                'fixed_at' => now()->toDateTimeString(),
                'original_end_date' => $oldEndDate,
                'corrected_end_date' => $correctEndDate,
                'fix_reason' => 'duration_days_vs_duration_in_days_bug'
            ])
        ]);
        
        // Log the fix for audit purposes
        Log::info('Subscription duration bug fixed', [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->subscription_plan_id,
            'plan_name' => $plan->name['en'] ?? 'Unknown',
            'old_end_date' => $oldEndDate,
            'new_end_date' => $correctEndDate,
            'plan_duration_days' => $plan->duration_in_days,
            'fixed_at' => now()
        ]);
    }
}

// Run the script
(new FixSubscriptionDurationBug)->handle();
