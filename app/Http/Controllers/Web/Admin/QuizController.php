<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['subscriptionPlan', 'questions'])
            ->withCount(['questions', 'quizAttempts'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Quizzes/Index', [
            'quizzes' => $quizzes
        ]);
    }

    public function create()
    {
        $subscriptionPlans = SubscriptionPlan::all();
        
        return Inertia::render('Admin/Quizzes/Create', [
            'subscriptionPlans' => $subscriptionPlans
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.rw' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.rw' => 'required|string',
            'topics' => 'required|array',
            'topics.*' => 'string|max:255',
            'time_limit_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_guest_quiz' => 'boolean',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|array',
            'questions.*.text.en' => 'required|string',
            'questions.*.text.rw' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|array',
            'questions.*.options.*.text.en' => 'required|string',
            'questions.*.options.*.text.rw' => 'required|string',
            'questions.*.correct_option_index' => 'required|integer|min:0',
        ]);

        if (empty($validated['subscription_plan_id']) && !$validated['is_guest_quiz']) {
            return back()->withErrors([
                'subscription_plan_id' => 'The quiz must be associated with a subscription plan or marked as a guest quiz.'
            ]);
        }

        $quiz = Quiz::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'topics' => $validated['topics'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'is_active' => $validated['is_active'] ?? true,
            'is_guest_quiz' => $validated['is_guest_quiz'] ?? false,
            'subscription_plan_id' => $validated['subscription_plan_id'],
        ]);

        foreach ($validated['questions'] as $questionData) {
            $question = $quiz->questions()->create([
                'text' => $questionData['text'],
                'correct_option_index' => $questionData['correct_option_index'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                $question->options()->create([
                    'text' => $optionData['text'],
                    'image_url' => $optionData['image_url'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz created successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.options', 'subscriptionPlan']);
        
        return Inertia::render('Admin/Quizzes/Show', [
            'quiz' => $quiz
        ]);
    }

    public function edit(Quiz $quiz)
    {
        $subscriptionPlans = SubscriptionPlan::all();
        $quiz->load(['questions.options']);
        
        return Inertia::render('Admin/Quizzes/Edit', [
            'quiz' => $quiz,
            'subscriptionPlans' => $subscriptionPlans
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.rw' => 'required|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.rw' => 'required|string',
            'topics' => 'required|array',
            'topics.*' => 'string|max:255',
            'time_limit_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_guest_quiz' => 'boolean',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|array',
            'questions.*.text.en' => 'required|string',
            'questions.*.text.rw' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|array',
            'questions.*.options.*.text.en' => 'required|string',
            'questions.*.options.*.text.rw' => 'required|string',
            'questions.*.correct_option_index' => 'required|integer|min:0',
        ]);

        if (empty($validated['subscription_plan_id']) && !$validated['is_guest_quiz']) {
            return back()->withErrors([
                'subscription_plan_id' => 'The quiz must be associated with a subscription plan or marked as a guest quiz.'
            ]);
        }

        $quiz->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'topics' => $validated['topics'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'is_active' => $validated['is_active'] ?? true,
            'is_guest_quiz' => $validated['is_guest_quiz'] ?? false,
            'subscription_plan_id' => $validated['subscription_plan_id'],
        ]);

        $quiz->questions()->delete();

        foreach ($validated['questions'] as $questionData) {
            $question = $quiz->questions()->create([
                'text' => $questionData['text'],
                'correct_option_index' => $questionData['correct_option_index'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                $question->options()->create([
                    'text' => $optionData['text'],
                    'image_url' => $optionData['image_url'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully.');
    }

    public function toggleGuest(Quiz $quiz)
    {
        $quiz->update([
            'is_guest_quiz' => !$quiz->is_guest_quiz,
            'subscription_plan_id' => $quiz->is_guest_quiz ? null : $quiz->subscription_plan_id
        ]);

        return back()->with('success', 'Quiz guest status updated successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // TODO: Implement CSV import logic
        // This would parse the CSV and create/update quizzes

        return back()->with('success', 'Quizzes imported successfully.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="quiz_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'title_en', 'title_rw', 'description_en', 'description_rw', 
                'topics', 'time_limit_minutes', 'is_guest_quiz', 'subscription_plan_id',
                'question_text_en', 'question_text_rw', 'correct_option_index',
                'option_1_text_en', 'option_1_text_rw', 'option_1_image_url',
                'option_2_text_en', 'option_2_text_rw', 'option_2_image_url',
                'option_3_text_en', 'option_3_text_rw', 'option_3_image_url',
                'option_4_text_en', 'option_4_text_rw', 'option_4_image_url',
            ]);

            // Add example row
            fputcsv($file, [
                'Road Signs', 'Ibirango mu nzira', 'Test your knowledge of road signs', 'Gerageza ubumenyi bwawe ku birango mu nzira',
                'traffic-signs,rules', '10', '0', '1',
                'What does this sign mean?', 'Iki kiringo kivuga iki?', '0',
                'Stop', 'Hagarara', 'https://example.com/stop_sign.jpg',
                'Yield', 'Tanga umusanzu', 'https://example.com/yield_sign.jpg',
                'No entry', 'Ntushobora kwinjira', 'https://example.com/no_entry.jpg',
                'Roundabout', 'Zigira ubutatu', 'https://example.com/roundabout.jpg',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
