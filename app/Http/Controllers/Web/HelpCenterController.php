<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpCenterController extends Controller
{
    /**
     * Display the help center page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = [
            [
                'title' => 'Getting Started',
                'description' => 'Learn the basics of your account, dashboard, and how to navigate the platform.',
                'icon' => 'book-open',
            ],
            [
                'title' => 'Quizzes & Attempts',
                'description' => 'Find answers on how to start, resume, and review your quiz attempts.',
                'icon' => 'life-buoy',
            ],
            [
                'title' => 'Account Management',
                'description' => 'Manage your profile, update your password, and view your personal details.',
                'icon' => 'user',
            ],
            [
                'title' => 'Forum & Community',
                'description' => 'Understand how to ask questions and interact with the community forum.',
                'icon' => 'message-square',
            ],
            [
                'title' => 'Technical Support',
                'description' => 'Get help with login issues, bugs, or other technical problems.',
                'icon' => 'shield-check',
            ],
        ];

        $faqItems = [
            [
                'value' => 'item-1',
                'question' => 'How do I start a quiz?',
                'answer' => "Navigate to the 'Quizzes' page from your dashboard. You will see a list of all available quizzes. Simply click on the quiz card you wish to take, and your attempt will begin immediately.",
            ],
            [
                'value' => 'item-2',
                'question' => 'Can I retake a quiz I have already completed?',
                'answer' => 'Yes, you can retake any quiz as many times as you like. Your previous scores will be saved, and taking a quiz again will start a new attempt. This is a great way to practice and improve your score.',
            ],
            [
                'value' => 'item-3',
                'question' => 'Where can I see my previous quiz scores?',
                'answer' => "On the 'Quizzes' page, any quiz you have completed will be marked with a 'Completed' status and will display your most recent score directly on the card for quick reference.",
            ],
            [
                'value' => 'item-4',
                'question' => 'How do I ask a question in the forum?',
                'answer' => "Go to the 'Forum' page. At the top of the page, you'll find a form where you can enter a title and a detailed description for your question. Once you submit it, it will be visible to the community and instructors.",
            ],
            [
                'value' => 'item-5',
                'question' => 'How do I change my password?',
                'answer' => 'Currently, password changes are handled by administrators. Please contact our support team using the email below, and we will assist you with resetting your password securely.',
            ],
        ];

        return view('dashboard.help-center.index', compact('categories', 'faqItems'));
    }
}
