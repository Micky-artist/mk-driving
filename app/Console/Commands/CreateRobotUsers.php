<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPoint;

class CreateRobotUsers extends Command
{
    protected $signature = 'robots:create-users {--count=5 : Number of robots to create}';
    protected $description = 'Create robot users in the system';

    public function handle(): int
    {
        $count = $this->option('count', 5);
        $this->info("Creating {$count} robot users...");

        try {
            DB::beginTransaction();

            for ($i = 1; $i <= $count; $i++) {
                $profile = $this->robotProfiles[$i - 1] ?? [
                    'first_name' => 'Robot' . $i,
                    'last_name' => 'User' . $i,
                    'email' => "robot{$i}@mkdriving.rw",
                ];

                $user = User::firstOrCreate(
                    ['email' => $profile['email']],
                    [
                        'first_name' => $profile['first_name'],
                        'last_name' => $profile['last_name'],
                        'password' => bcrypt('robot_password'),
                        'role' => 'USER',
                        'is_robot' => true,
                        'email_verified_at' => now(),
                    ]
                );

                // Create UserPoint record for the robot
                UserPoint::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'total_points' => 0,
                        'weekly_points' => 0,
                        'monthly_points' => 0,
                        'last_activity_at' => now(),
                    ]
                );

                $this->line("✓ Created robot: {$profile['first_name']} ({$profile['last_name']})");
            }

            DB::commit();
            $this->info('Successfully created robot users!');
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to create robot users: {$e->getMessage()}");
            return 1;
        }
    }

    private $robotProfiles = [
        [
            'first_name' => 'John',
            'last_name' => 'Sibo',
            'email' => 'john@mkdriving.rw',
        ],
        [
            'first_name' => 'Sarah',
            'last_name' => 'Yarumvise',
            'email' => 'sarah@mkdriving.rw',
        ],
        [
            'first_name' => 'Michael',
            'last_name' => 'Mugyenyi',
            'email' => 'michael@mkdriving.rw',
        ],
        [
            'first_name' => 'Grace',
            'last_name' => 'Mwiza',
            'email' => 'grace@mkdriving.rw',
        ],
        [
            'first_name' => 'David',
            'last_name' => 'Niyonkuru',
            'email' => 'david@mkdriving.rw',
        ],
    ];
}
