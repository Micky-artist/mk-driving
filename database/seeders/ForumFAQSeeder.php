<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ForumFAQSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing admin user
        $adminEmail = app()->environment('production') ? 'mkscholars250@gmail.com' : 'remyrwa@gmail.com';
        $admin = User::where('email', $adminEmail)->first();
        
        if (!$admin) {
            $this->command->error("Admin user not found. Please ensure {$adminEmail} exists.");
            return;
        }

        // FAQ Categories
        $faqs = [
    [
        'title' => [
            'en' => 'What are the requirements for getting a driving license in Rwanda?',
            'rw' => 'Ni ibiki bisabwa ngo mbone uruhushya rwo gutwara imodoka mu Rwanda?'
        ],
        'content' => [
            'en' => 'To get a driving license in Rwanda, you must:\n1. Be at least 18 years old\n2. Have a valid national ID\n3. Pass the theoretical test\n4. Pass the practical driving test.',
            'rw' => 'Kugira uruhushya rwo gutwara imodoka mu Rwanda, ugomba:\n1. Kuba ufite imyaka 18 byibuze\n2. Kuba ufite indangamuntu\n3. Gutsinda ikizami cy\'amategeko y\'umuhanda\n4. Gutsinda ikizamini cyo gutwara imodoka.'
        ],
        'topics' => ['general', 'requirements', 'license'],
        'category' => 'general'
    ],
    
    [
        'title' => [
            'en' => 'How long does it take to complete the theory practice tests?',
            'rw' => 'Bisaba igihe kingana gute ngo ndangize amahugurwa y\'amategeko y\'umuhanda?'
        ],
        'content' => [
            'en' => 'Our standard theory practice takes 4-6 weeks to complete, but it depends on you schedule and how often you practice.',
            'rw' => 'Kwitoza cyangwa Amahugurwa bikunze gufata byibuze ibyumweru 4 kugeza kuri 6. Akenshi biterwa nigihe mufata mwihugura buri munsi.'
        ],
        'topics' => ['general', 'duration', 'schedule'],
        'category' => 'general'
    ],

    [
        'title' => [
            'en' => 'What are the fees for driving courses?',
            'rw' => 'Muca amafaranga angana ate ngo ntangira kwitoza?'
        ],
        'content' => [
            'en' => 'There is a free course you can try. Others are paid per your needs and you can find them in Plans',
            'rw' => 'Hari umwitozo w\'buntu wagerageza Ahabanza. Ushobora no kongera ifatagabuguzi bitewe nuko ushaka kwiga.'
        ],
        'topics' => ['pricing', 'fees', 'payment'],
        'category' => 'pricing'
    ]
];

        // Insert FAQs
        foreach ($faqs as $index => $faq) {
            DB::table('forum_questions')->insert([
                'title' => json_encode($faq['title']),
                'content' => json_encode($faq['content']),
                'user_id' => $admin->id,
                'is_approved' => true,
                'topics' => json_encode($faq['topics']),
                'is_news_discussion' => false,
                'created_at' => Carbon::now()->subDays($index),
                'updated_at' => Carbon::now()->subDays($index),
            ]);
        }

        // Create answers from admin user for all questions
        foreach ($faqs as $index => $faq) {
            // Get the question that was just created
            $question = DB::table('forum_questions')
                ->where('title', json_encode($faq['title']))
                ->first();
            
            if ($question) {
                DB::table('forum_answers')->insert([
                    'content' => json_encode($faq['answer']),
                    'forum_question_id' => $question->id,
                    'user_id' => $admin->id,
                    'is_approved' => true,
                    'created_at' => Carbon::now()->subDays($index)->addHours(2),
                    'updated_at' => Carbon::now()->subDays($index)->addHours(2),
                ]);
            }
        }

        // Create a default user for asking questions
        User::firstOrCreate(
            ['email' => 'student@mk-driving.com'],
            [
                'password' => Hash::make('student123'),
                'first_name' => 'John',
                'last_name' => 'Kagabo',
                'role' => 'USER',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Forum FAQ seeder completed successfully!');
        $this->command->info('Created ' . count($faqs) . ' FAQ questions with answers');
        $this->command->info('Created default student user');
    }
}
