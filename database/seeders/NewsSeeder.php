<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create admin user
        $adminEmail = app()->environment('production') ? 'mkscholars250@gmail.com' : 'remyrwa@gmail.com';
        $admin = User::where('email', $adminEmail)->first();
        
        if (!$admin) {
            $this->command->error("Admin user not found. Please ensure {$adminEmail} exists.");
            return;
        }

        // News Categories
        $categories = ['announcement', 'article', 'update', 'safety', 'promotion'];

        // Default News Items
        $newsItems = [
            // Timeless Announcement
            [
                'title' => [
                    'en' => 'Welcome to MK Driving School!',
                    'rw' => 'Murakaza neza muri MK Driving'
                ],
                'content' => [
                    'en' => "Welcome to MK Driving School. Your journey to safe driving starts here! You can practice any day, enter leaderboards, and be encouraged by others to learn and pass fast.",
                    'rw' => "Murakaza neza ku Ishuri Ryo Gutwara Imodoka muri MK - Urugendo rwawe rwo Gutwara Imodoka rutangirira hano. Igana n'abandi, ugire umarava wo gutsinda vuba."
                ],
                'excerpt' => [
                    'en' => 'Learn along others, enter leadboards, and pass your provisional test fast.',
                    'rw' => 'Igana n\'abanda, rimwe na rimwe mu marushanwa agutera courage, utsinde byihuse.'
                ],
                'category' => 'announcement',
                'type' => 'announcement',
                'status' => 'published',
                'featured' => true,
                'published_at' => now()->subMonths(6),
            ],
        ];

        // Create news items
        foreach ($newsItems as $index => $newsData) {
            $news = News::create([
                'author_id' => $admin->id,
                'title' => $newsData['title'],
                'content' => $newsData['content'],
                'is_published' => $newsData['status'] === 'published',
                'published_at' => $newsData['published_at'],
                'slug' => Str::slug($newsData['title']['en']),
                'views' => rand(100, 1000),
                'likes_count' => rand(20, 200),
                'comments_count' => rand(5, 50),
                'created_at' => $newsData['published_at'],
                'updated_at' => $newsData['published_at'],
            ]);
        }

        $this->command->info('News seeder completed successfully!');
        $this->command->info('Created ' . count($newsItems) . ' news items');
    }
}
