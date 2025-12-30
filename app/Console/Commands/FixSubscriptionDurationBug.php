<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixSubscriptionDurationBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:fix-duration-bug {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix subscriptions affected by the duration_days bug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Fixing Subscription Duration Bug ===');
        
        $startTime = now();
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $fixedCount = 0;
        $errorCount = 0;
        
        try {
            // Find all subscriptions that were likely affected by the bug
            $buggySubscriptions = $this->findBuggySubscriptions();
            
            $this->info("Found {$buggySubscriptions->count()} potentially buggy subscriptions");
            
            if ($buggySubscriptions->isEmpty()) {
                $this->info('No buggy subscriptions found. All good!');
                return;
            }
            
            if ($dryRun) {
                $this->table(
                    ['ID', 'User', 'Plan', 'Current End', 'Correct End', 'Duration'],
                    $buggySubscriptions->map(function ($sub) {
                        $plan = $sub->plan;
                        $correctEnd = $sub->starts_at->copy()->addDays($plan->duration_in_days);
                        return [
                            $sub->id,
                            $sub->user_id,
                            $plan->name['en'] ?? 'Unknown',
                            $sub->ends_at,
                            $correctEnd,
                            $plan->duration_in_days . ' days'
                        ];
                    })
                );
                return;
            }
            
            $this->withProgressBar($buggySubscriptions, function ($subscription) use (&$fixedCount, &$errorCount) {
                try {
                    $this->fixSubscription($subscription);
                    $fixedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->newLine();
                    $this->error("Failed to fix subscription ID {$subscription->id}: {$e->getMessage()}");
                    Log::error('Failed to fix subscription in script', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage()
                    ]);
                }
            });
            
            $this->newLine();
            $this->newLine();
            $this->info('=== Summary ===');
            $this->info("Fixed: {$fixedCount} subscriptions");
            $this->info("Errors: {$errorCount} subscriptions");
            $this->info("Duration: " . $startTime->diffInSeconds(now()) . " seconds");
            $this->info("Completed at: " . now()->toDateTimeString());
            
            if ($fixedCount > 0) {
                $this->newLine();
                $this->warn('⚠️  IMPORTANT: Verify the fixes in the admin panel and deploy the code changes before users access the system.');
            }
            
        } catch (\Exception $e) {
            $this->error("Script failed: " . $e->getMessage());
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
        
        return \App\Models\Subscription::where('status', 'ACTIVE')
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
