<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $blogPosts = [
            [
                'title' => json_encode([
                    'en' => '10 Essential Tips for New Drivers',
                    'rw' => 'Inama 10 ngenderwaho ku bashya mu kugendera'
                ]),
                'slug' => '10-essential-tips-for-new-drivers',
                'content' => json_encode([
                    'en' => 'This blog post provides 10 essential tips for new drivers to stay safe on the road.',
                    'rw' => 'Iyi ngingo y\'urubuga ifite inama 10 ngenderwaho ku bantu bashya mu kugendera kugira ngo bagume mu mutekano ku muhanda.'
                ]),
                'excerpt' => json_encode([
                    'en' => 'Essential tips every new driver should know before hitting the road.',
                    'rw' => 'Inama ngenderwaho uwo ari we wese ushaka kugendera agomba kumenya mbere yo kugendera.'
                ]),
                'published_at' => now(),
                'is_published' => true,
                'featured_image' => 'blog/new-drivers-tips.jpg',
                'meta_description' => json_encode([
                    'en' => 'Essential tips for new drivers to ensure safety and confidence on the road.',
                    'rw' => 'Inama ngenderwaho ku bantu bashya mu kugendera kugira ngo bagume mu mutekano kandi babe bafite icyizere mu kugendera.'
                ]),
            ],
            [
                'title' => json_encode([
                    'en' => 'Understanding Road Signs: A Complete Guide',
                    'rw' => 'Gusobanukirwa Ibimenyetso by\'Umuhanda: Itangiriro Yuzuye'
                ]),
                'slug' => 'understanding-road-signs-guide',
                'content' => json_encode([
                    'en' => 'A comprehensive guide to understanding various road signs and their meanings.',
                    'rw' => 'Uru ni urugero rw\'uburyo wakoresha amashanyarazi ku bantu bafite imodoka cyangwa bashaka kuzigura.'
                ]),
                'excerpt' => json_encode([
                    'en' => 'Learn how to read and understand all types of road signs.',
                    'rw' => 'Menya uko wasoma kandi usobanukirwa ibimenyetso byose by\'umuhanda.'
                ]),
                'published_at' => now()->subDays(3),
                'is_published' => true,
                'featured_image' => 'blog/road-signs-guide.jpg',
                'meta_description' => json_encode([
                    'en' => 'A complete guide to understanding and interpreting road signs for safer driving.',
                    'rw' => 'Uru ni urugero rw\'uburyo wakoresha amashanyarazi ku bantu bafite imodoka cyangwa bashaka kuzigura.'
                ]),
            ],
            [
                'title' => json_encode([
                    'en' => 'The Importance of Defensive Driving',
                    'rw' => 'Ingirakamaro y\'Ukwigendera mu buryo bw\'umutekano'
                ]),
                'slug' => 'importance-defensive-driving',
                'content' => json_encode([
                    'en' => 'Learn why defensive driving is crucial for your safety and how to practice it.',
                    'rw' => 'Menya impamvu yo kwigendera mu buryo bw\'umutekano ari ngombwa kandi ufate uko wabikora.'
                ]),
                'excerpt' => json_encode([
                    'en' => 'Discover the key principles of defensive driving and how they can save lives.',
                    'rw' => 'Menya ingingo nyamukuru zo kwigendera mu buryo bw\'umutekano no uko zishobora kurokora ubuzima.'
                ]),
                'published_at' => now()->subDays(7),
                'is_published' => true,
                'featured_image' => 'blog/defensive-driving.jpg',
                'meta_description' => json_encode([
                    'en' => 'Understanding the importance of defensive driving techniques for road safety.',
                    'rw' => 'Gusobanukirwa ingirakamaro y\'imikorere yo kwigendera mu buryo bw\'umutekano ku muhanda.'
                ]),
            ],
        ];

        foreach ($blogPosts as $post) {
            // Check if a blog post with this slug already exists
            if (!\App\Models\Blog::where('slug', $post['slug'])->exists()) {
                Blog::create(array_merge($post, [
                    'author_id' => $admin->id,
                ]));
            }
        }
    }
}
