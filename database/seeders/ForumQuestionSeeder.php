<?php

namespace Database\Seeders;

use App\Models\ForumQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ForumQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(5)->get();
        
        $questions = [
            [
                'title' => json_encode([
                    'en' => 'How to handle roundabouts?',
                    'rw' => 'Ushobora gute kugendera mu nzira y\'umuzenguruka?'
                ]),
                'content' => json_encode([
                    'en' => 'I\'m a new driver and I find roundabouts quite challenging. Any tips on how to navigate them safely?',
                    'rw' => 'Ndi umushoferi mushya kandi ntabwo mbyumva neza mu nzira y\'umuzenguruka. Hari inama zihariye zokuyikoresha neza?'
                ]),
                'topics' => json_encode(['roundabouts', 'new-driver', 'safety']),
                'is_approved' => true,
            ],
            [
                'title' => json_encode([
                    'en' => 'Best way to parallel park?',
                    'rw' => 'Uburyo bwiza bwo gupaka imodoka imwe iri inyuma y\'indi?'
                ]),
                'content' => json_encode([
                    'en' => 'I struggle with parallel parking. What are some techniques that can help me improve?',
                    'rw' => 'Nkomeje kugira ibibazo no gupaka imodoka imwe iri inyuma y\'indi. Hari ubuhe buryo bungana?'
                ]),
                'topics' => json_encode(['parking', 'driving-tips', 'new-driver']),
                'is_approved' => true,
            ],
            [
                'title' => json_encode([
                    'en' => 'Preparing for the driving test',
                    'rw' => 'Kwitegura kugenzura imodoka'
                ]),
                'content' => json_encode([
                    'en' => 'I have my driving test next week. What should I focus on during my final preparations?',
                    'rw' => 'Mfite igenzura rya modoka mu cyumweru gitaha. Ni iki niteguye kugenzura?'
                ]),
                'topics' => json_encode(['driving-test', 'tips', 'preparation']),
                'is_approved' => true,
            ],
        ];

        foreach ($questions as $question) {
            $user = $users->random();
            
            $forumQuestion = ForumQuestion::create(array_merge($question, [
                'user_id' => $user->id,
                'views' => rand(0, 1000),
            ]));
        }
    }
}
