<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $newsItems = [
            [
                'title' => json_encode([
                    'en' => 'New Traffic Laws Coming into Effect Next Month',
                    'rw' => 'Amategeko Mashya y\'Imihanda Azatangira Gukorwa Ukwezi Gutaha'
                ]),
                'slug' => 'new-traffic-laws-2023',
                'content' => json_encode([
                    'en' => 'New traffic laws will be implemented next month to improve road safety. Learn about the changes and how they might affect you.',
                    'rw' => 'Hazatangira gukorwa amategeko mashya y\'imihanda ukwezi gutaha kugira ngo hazongere umutekano ku muhanda. Menya ibihindutse n\'uko bishobora kukugiraho ingaruka.'
                ]),
                'published_at' => now(),
                'is_published' => true,
                'image_url' => 'news/traffic-laws.jpg',
                'meta_description' => json_encode([
                    'en' => 'Learn about the new traffic laws coming into effect next month.',
                    'rw' => 'Menya ibyerekeye amategeko mashya y\'imihanda azatangira gukora ukwezi gutaha.'
                ]),
                'author_id' => $admin->id,
            ],
            [
                'title' => json_encode([
                    'en' => 'Tips for Safe Winter Driving',
                    'rw' => 'Inama zo Kugendera neza mu gihe cy\'imbeho y\'imbyura'
                ]),
                'slug' => 'winter-driving-tips',
                'content' => json_encode([
                    'en' => 'As winter approaches, it\'s important to prepare your vehicle and adjust your driving habits for safer winter travel.',
                    'rw' => 'Igihe cy\'imbyura cyegereje, ni ngombwa kwitegura imodoka yawe no kuyihindura imyitwarire yawe yo kugendera kugira ngo ugende neza mu gihe cy\'imbyura.'
                ]),
                'published_at' => now()->subDays(5),
                'is_published' => true,
                'image_url' => 'news/winter-driving.jpg',
                'meta_description' => json_encode([
                    'en' => 'Essential tips for staying safe on the road during winter conditions.',
                    'rw' => 'Inama ngenderwaho zo kuguma uri mu mutekano ku muhanda mu gihe cy\'imbyura.'
                ]),
                'author_id' => $admin->id,
            ],
            [
                'title' => json_encode([
                    'en' => 'Electric Vehicles: What You Need to Know',
                    'rw' => 'Imodoka Zikoresha Amashanyarazi: Ibyo Ukeneye Kumenya'
                ]),
                'slug' => 'electric-vehicles-guide',
                'content' => json_encode([
                    'en' => 'With the rise of electric vehicles, here\'s what drivers need to know about making the switch to electric.',
                    'rw' => 'Hagati y\'ibyiyongereye by\'imodoka zikoresha amashanyarazi, dore ibyo abashoferi bakeneye kumenya mbere yo guhindura kuri izi modoka.'
                ]),
                'published_at' => now()->subDays(10),
                'is_published' => true,
                'image_url' => 'news/electric-vehicles.jpg',
                'meta_description' => json_encode([
                    'en' => 'A comprehensive guide to electric vehicles for new and prospective owners.',
                    'rw' => 'Uru ni urugero rw\'uburyo wakoresha amashanyarazi ku bantu bafite imodoka cyangwa bashaka kuzigura.'
                ]),
                'author_id' => $admin->id,
            ],
        ];

        foreach ($newsItems as $news) {
            // Check if a news item with this slug already exists
            if (!News::where('slug', $news['slug'])->exists()) {
                News::create($news);
            }
        }
    }
}
