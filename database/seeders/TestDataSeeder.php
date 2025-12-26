<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\UserPointsHistory;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data - check if tables exist first
        if (Schema::hasTable('user_points')) {
            DB::table('user_points')->truncate();
        }
        if (Schema::hasTable('user_points_history')) {
            DB::table('user_points_history')->truncate();
        }
        
        // Clear existing test users
        User::whereIn('email', [
            'john.doe@example.com',
            'jane.smith@example.com', 
            'mike.johnson@example.com',
            'sarah.williams@example.com',
            'david.brown@example.com',
            'emily.davis@example.com',
            'robert.miller@example.com',
            'lisa.wilson@example.com'
        ])->delete();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create test users with different point levels
        $users = $this->createTestUsers();
        
        // Create forum questions and answers
        $this->createForumActivity($users);
        
        // Create quiz attempts and results
        $this->createQuizActivity($users);
        
        // Award points and create history
        $this->awardPoints($users);
    }

    private function createTestUsers(): array
    {
        $userData = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'role' => 'USER',
                'points_target' => 1250, // Top performer
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'role' => 'USER',
                'points_target' => 980, // High performer
            ],
            [
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson@example.com',
                'role' => 'USER',
                'points_target' => 750, // Good performer
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@example.com',
                'role' => 'USER',
                'points_target' => 450, // Average performer
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@example.com',
                'role' => 'USER',
                'points_target' => 200, // Beginner
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@example.com',
                'role' => 'USER',
                'points_target' => 100, // New user
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Miller',
                'email' => 'robert.miller@example.com',
                'role' => 'INSTRUCTOR',
                'points_target' => 1500, // Instructor with high points
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Wilson',
                'email' => 'lisa.wilson@example.com',
                'role' => 'USER',
                'points_target' => 620, // Active user
            ],
        ];

        $users = [];
        foreach ($userData as $data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => $data['role'],
                'email_verified_at' => now(),
            ]);

            // Initialize user points
            UserPoint::create([
                'user_id' => $user->id,
                'total_points' => 0,
                'weekly_points' => 0,
                'monthly_points' => 0,
                'last_activity_at' => now(),
            ]);

            $users[$user->id] = [
                'user' => $user,
                'points_target' => $data['points_target']
            ];
        }

        return $users;
    }

    private function createForumActivity(array $users): void
    {
        $forumQuestions = [
            [
                'title' => ['en' => 'How to parallel park in tight spaces?', 'rw' => 'Niba y\'akwihangira imodoka mu bice bike?'],
                'content' => ['en' => 'I\'m struggling with parallel parking especially when there\'s not much space. Any tips?', 'rw' => 'Ntabwo ndushobora kwihangira imodoka byimazeyo cyane iyo nta hantu ihagije. Hari ubufasha?'],
                'author_id' => array_keys($users)[1], // Jane Smith
                'views' => 45,
                'votes' => 8,
            ],
            [
                'title' => ['en' => 'Best practices for night driving?', 'rw' => 'Uburyo bwiza bwo gutwara nijoro?'],
                'content' => ['en' => 'What are the most important things to remember when driving at night?', 'rw' => 'Ibya ngombwa kwibuka mu gutwenga nijoro ni ibiki?'],
                'author_id' => array_keys($users)[3], // Sarah Williams
                'views' => 32,
                'votes' => 5,
            ],
            [
                'title' => ['en' => 'Understanding roundabout rules in Rwanda', 'rw' => 'Kumenya amategeko y\'umuhanda wuzungirayeho mu Rwanda'],
                'content' => ['en' => 'Can someone explain the proper way to navigate roundabouts here?', 'rw' => 'Washobora kumugaragaro iburyo byo kuzengera imihanda yuzungirayeho?'],
                'author_id' => array_keys($users)[0], // John Doe
                'views' => 67,
                'votes' => 12,
            ],
            [
                'title' => ['en' => 'Car maintenance tips for new drivers', 'rw' => 'Ubuhanzi bwo kurinda imodoka ku bakoranya bato'],
                'content' => ['en' => 'What basic maintenance should every new driver know?', 'rw' => 'Ibyo kurinda bya ngombwa buri wikoranya uza kumenya?'],
                'author_id' => array_keys($users)[4], // David Brown
                'views' => 28,
                'votes' => 3,
            ],
            [
                'title' => ['en' => 'Dealing with aggressive drivers', 'rw' => 'Gukora n\'abatwenga bihangage'],
                'content' => ['en' => 'How do you handle situations with aggressive drivers on the road?', 'rw' => 'Ugira uwihanga inyungu z\'abatwenga ku muhanda?'],
                'author_id' => array_keys($users)[2], // Mike Johnson
                'views' => 51,
                'votes' => 7,
            ],
        ];

        $createdQuestions = [];
        foreach ($forumQuestions as $questionData) {
            $question = ForumQuestion::create([
                'user_id' => $questionData['author_id'],
                'title' => $questionData['title'],
                'content' => $questionData['content'],
                'views' => $questionData['views'],
                'votes' => $questionData['votes'],
                'is_closed' => false,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
            $createdQuestions[] = $question;
        }

        // Create answers for questions
        $answers = [
            [
                'question_id' => $createdQuestions[0]->id,
                'content' => ['en' => 'Practice in an empty parking lot first. Use reference points and take it slow!', 'rw' => 'Rangiza mu parking idafite imodoka. Koresha amabwiriza wihangire neza!'],
                'author_id' => array_keys($users)[6], // Robert Miller (Instructor)
                'votes' => 15,
                'is_accepted' => true,
            ],
            [
                'question_id' => $createdQuestions[0]->id,
                'content' => ['en' => 'I found that turning the wheel slightly after the back wheels are in helps a lot.', 'rw' => 'Nabonye ko guhindukiza umwanya hanyuma after guciranye byongera kubaha.'],
                'author_id' => array_keys($users)[7], // Lisa Wilson
                'votes' => 8,
                'is_accepted' => false,
            ],
            [
                'question_id' => $createdQuestions[1]->id,
                'content' => ['en' => 'Always use high beams when appropriate, but dim them for oncoming traffic.', 'rw' => 'Koresha imisumire yose ariko wihagure ku batari bwo.'],
                'author_id' => array_keys($users)[6], // Robert Miller (Instructor)
                'votes' => 12,
                'is_accepted' => true,
            ],
            [
                'question_id' => $createdQuestions[2]->id,
                'content' => ['en' => 'Yield to traffic already in the roundabout, signal your exit, and stay in your lane.', 'rw' => 'Tanga umuhanda ku bari muri roundabout, emere uwo uvamo, ugasigayo mu murima wawe.'],
                'author_id' => array_keys($users)[1], // Jane Smith
                'votes' => 18,
                'is_accepted' => true,
            ],
            [
                'question_id' => $createdQuestions[3]->id,
                'content' => ['en' => 'Check oil, tire pressure, and coolant regularly. Learn to change a tire!', 'rw' => 'Reba amavuta, pressure ya taye, na coolant buri gihe. Umenye guhindura taye!'],
                'author_id' => array_keys($users)[0], // John Doe
                'votes' => 9,
                'is_accepted' => true,
            ],
            [
                'question_id' => $createdQuestions[4]->id,
                'content' => ['en' => 'Stay calm, don\'t engage, and if necessary, pull over and let them pass.', 'rw' => 'Tegura, utangira, iyo ari ngombva, voma uware abasohoka.'],
                'author_id' => array_keys($users)[3], // Sarah Williams
                'votes' => 11,
                'is_accepted' => true,
            ],
        ];

        foreach ($answers as $answerData) {
            ForumAnswer::create([
                'question_id' => $answerData['question_id'],
                'user_id' => $answerData['author_id'],
                'content' => $answerData['content'],
                'votes' => $answerData['votes'],
                'is_accepted' => $answerData['is_accepted'],
                'is_approved' => true,
                'created_at' => now()->subDays(rand(0, 25)),
                'updated_at' => now()->subDays(rand(0, 3)),
            ]);
        }
    }

    private function createQuizActivity(array $users): void
    {
        // Get existing quizzes or create sample ones
        $quizzes = Quiz::where('is_active', true)->limit(3)->get();
        
        if ($quizzes->count() < 3) {
            // Create sample quizzes if none exist
            $quizData = [
                [
                    'title' => ['en' => 'Basic Driving Rules', 'rw' => 'Amategeko Y\'Ingenzi'],
                    'description' => ['en' => 'Test your knowledge of basic driving regulations', 'rw' => 'Menya byinshi ku mategeko yo gutwenga'],
                    'time_limit_minutes' => 30,
                ],
                [
                    'title' => ['en' => 'Road Signs Recognition', 'rw' => 'Kumenya Amapamu Y\'Umuhanda'],
                    'description' => ['en' => 'Identify various road signs and their meanings', 'rw' => 'Menya amapamu y\'umuhanda n\'ibyo bivuze'],
                    'time_limit_minutes' => 25,
                ],
                [
                    'title' => ['en' => 'Defensive Driving Techniques', 'rw' => 'Kwirinda Impanuka'],
                    'description' => ['en' => 'Learn safe driving practices and accident prevention', 'rw' => 'Menya uburyo bwo kwirinda impanuka.'],
                    'time_limit_minutes' => 35,
                ],
            ];

            foreach ($quizData as $data) {
                $quiz = Quiz::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'time_limit_minutes' => $data['time_limit_minutes'],
                    'is_active' => true,
                    'creator_id' => array_keys($users)[6], // Robert Miller (Instructor)
                ]);
                $quizzes->push($quiz);
            }
        }

        // Create quiz attempts for users
        $userIds = array_keys($users);
        $quizArray = $quizzes->toArray();

        foreach ($userIds as $index => $userId) {
            $numAttempts = rand(2, 5);
            
            for ($i = 0; $i < $numAttempts; $i++) {
                $quiz = $quizzes[$i % $quizzes->count()];
                $score = rand(45, 95);
                
                $attempt = QuizAttempt::create([
                    'user_id' => $userId,
                    'quiz_id' => $quiz->id,
                    'status' => 'COMPLETED',
                    'score' => $score,
                    'time_spent_seconds' => rand(900, 1800),
                    'started_at' => now()->subDays(rand(1, 20))->subHours(rand(1, 12)),
                    'completed_at' => now()->subDays(rand(1, 20)),
                ]);

                // Create some answers for the attempt
                $this->createQuizAttemptAnswers($attempt, $score);
            }
        }
    }

    private function createQuizAttemptAnswers(QuizAttempt $attempt, int $score): void
    {
        // Get questions for the quiz
        $questions = Question::where('quiz_id', $attempt->quiz_id)->limit(10)->get();
        
        if ($questions->isEmpty()) {
            return;
        }

        $correctAnswers = round(($score / 100) * $questions->count());
        $answeredCorrectly = 0;

        foreach ($questions as $question) {
            $options = Option::where('question_id', $question->id)->get();
            
            if ($options->isEmpty()) {
                continue;
            }

            $isCorrect = $answeredCorrectly < $correctAnswers && rand(0, 1);
            
            if ($isCorrect) {
                $correctOption = $options->where('is_correct', true)->first();
                $option = $correctOption ?: $options->random();
                $answeredCorrectly++;
            } else {
                $option = $options->where('is_correct', false)->first() ?: $options->random();
            }

            QuizAttemptAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $option->id,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]);
        }
    }

    private function awardPoints(array $users): void
    {
        $pointRules = [
            'quiz_completed' => 10,
            'quiz_high_score' => 20,
            'forum_question' => 5,
            'forum_answer' => 8,
            'helpful_answer' => 15,
            'accepted_answer' => 25,
            'daily_login' => 2,
        ];

        foreach ($users as $userId => $userData) {
            $user = $userData['user'];
            $targetPoints = $userData['points_target'];
            $currentPoints = 0;

            // Award points for quiz attempts
            $attempts = QuizAttempt::where('user_id', $userId)->get();
            foreach ($attempts as $attempt) {
                $points = $pointRules['quiz_completed'];
                if ($attempt->score >= 80) {
                    $points += $pointRules['quiz_high_score'];
                }
                
                $currentPoints += $points;
                
                // Only create history if table exists
                if (Schema::hasTable('user_points_history')) {
                    DB::table('user_points_history')->insert([
                        'user_id' => $userId,
                        'points_change' => $points,
                        'reason' => 'quiz_completed',
                        'metadata' => json_encode(['quiz_id' => $attempt->quiz_id, 'score' => $attempt->score]),
                        'created_at' => $attempt->completed_at,
                        'updated_at' => $attempt->completed_at,
                    ]);
                }
            }

            // Award points for forum activity
            $questions = ForumQuestion::where('user_id', $userId)->get();
            foreach ($questions as $question) {
                $points = $pointRules['forum_question'];
                $currentPoints += $points;
                
                if (Schema::hasTable('user_points_history')) {
                    DB::table('user_points_history')->insert([
                        'user_id' => $userId,
                        'points_change' => $points,
                        'reason' => 'forum_question',
                        'metadata' => json_encode(['question_id' => $question->id]),
                        'created_at' => $question->created_at,
                        'updated_at' => $question->created_at,
                    ]);
                }
            }

            $answers = ForumAnswer::where('user_id', $userId)->get();
            foreach ($answers as $answer) {
                $points = $pointRules['forum_answer'];
                
                if ($answer->votes >= 5) {
                    $points += $pointRules['helpful_answer'];
                }
                
                if ($answer->is_accepted) {
                    $points += $pointRules['accepted_answer'];
                }
                
                $currentPoints += $points;
                
                if (Schema::hasTable('user_points_history')) {
                    DB::table('user_points_history')->insert([
                        'user_id' => $userId,
                        'points_change' => $points,
                        'reason' => 'forum_answer',
                        'metadata' => json_encode(['answer_id' => $answer->id, 'votes' => $answer->votes, 'accepted' => $answer->is_accepted]),
                        'created_at' => $answer->created_at,
                        'updated_at' => $answer->created_at,
                    ]);
                }
            }

            // Add daily login points to reach target
            if ($currentPoints < $targetPoints) {
                $remainingPoints = $targetPoints - $currentPoints;
                $daysOfLogin = ceil($remainingPoints / $pointRules['daily_login']);
                
                for ($i = 0; $i < $daysOfLogin; $i++) {
                    if (Schema::hasTable('user_points_history')) {
                        DB::table('user_points_history')->insert([
                            'user_id' => $userId,
                            'points_change' => $pointRules['daily_login'],
                            'reason' => 'daily_login',
                            'metadata' => json_encode(['login_date' => now()->subDays($i)->toDateString()]),
                            'created_at' => now()->subDays($i),
                            'updated_at' => now()->subDays($i),
                        ]);
                    }
                }
            }

            // Update user points total
            $userPoint = UserPoint::where('user_id', $userId)->first();
            if ($userPoint) {
                $userPoint->update([
                    'total_points' => $targetPoints,
                    'weekly_points' => rand(50, 200),
                    'monthly_points' => rand(200, 600),
                    'last_activity_at' => now()->subHours(rand(1, 48)),
                ]);
            }
        }
    }
}
