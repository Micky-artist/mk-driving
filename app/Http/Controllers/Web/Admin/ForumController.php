<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumQuestion;
use App\Models\ForumAnswer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    /**
     * Display a listing of forum questions.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $questions = ForumQuestion::with(['user', 'answers'])
            ->withCount('answers')
            ->latest()
            ->paginate(15);

        return view('admin.forum.index', compact('questions'));
    }

    /**
     * Display the forum leaderboard with filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function leaderboard(Request $request): View
    {
        $see = $request->get('see', 'default');
        
        if ($see === 'questions') {
            return $this->showQuestionsLeaderboard($request);
        } elseif ($see === 'answers') {
            return $this->showAnswersLeaderboard($request);
        }
        
        // Default leaderboard view
        // Get top users by questions asked
        $topQuestioners = ForumQuestion::select('user_id')
            ->selectRaw('COUNT(*) as questions_count')
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('questions_count')
            ->limit(10)
            ->get();

        // Get top users by answers given
        $topAnswerers = ForumAnswer::select('user_id')
            ->selectRaw('COUNT(*) as answers_count')
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('answers_count')
            ->limit(10)
            ->get();

        // Get most active users (combined questions + answers)
        $mostActive = DB::table(DB::raw('(
            SELECT user_id, COUNT(*) as contributions_count 
            FROM forum_questions 
            GROUP BY user_id
            UNION ALL 
            SELECT user_id, COUNT(*) as contributions_count 
            FROM forum_answers 
            GROUP BY user_id
        ) as combined'))
        ->select('user_id', DB::raw('SUM(contributions_count) as contributions_count'))
        ->groupBy('user_id')
        ->orderByDesc('contributions_count')
        ->limit(10)
        ->get();

        // Load user relationships
        $userIds = $mostActive->pluck('user_id');
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');
        $mostActive->each(function ($item) use ($users) {
            $item->user = $users->get($item->user_id);
        });

        // Get full leaderboard with pagination
        $fullLeaderboard = DB::table(DB::raw('(
            SELECT user_id, COUNT(*) as contributions_count 
            FROM forum_questions 
            GROUP BY user_id
            UNION ALL 
            SELECT user_id, COUNT(*) as contributions_count 
            FROM forum_answers 
            GROUP BY user_id
        ) as combined'))
        ->select('user_id', DB::raw('SUM(contributions_count) as total_contributions'))
        ->groupBy('user_id')
        ->orderByDesc('total_contributions')
        ->paginate(50);

        // Load user relationships for full leaderboard
        $leaderboardUserIds = $fullLeaderboard->pluck('user_id');
        $leaderboardUsers = \App\Models\User::whereIn('id', $leaderboardUserIds)->get()->keyBy('id');
        $fullLeaderboard->getCollection()->each(function ($item) use ($leaderboardUsers) {
            $item->user = $leaderboardUsers->get($item->user_id);
        });

        return view('admin.forum.leaderboard', compact('topQuestioners', 'topAnswerers', 'mostActive', 'fullLeaderboard'));
    }

    /**
     * Show questions leaderboard with admin management.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    private function showQuestionsLeaderboard(Request $request): View
    {
        $questions = ForumQuestion::with(['user'])
            ->withCount('answers')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.forum.leaderboard-questions', compact('questions'));
    }

    /**
     * Show answers leaderboard with admin management.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    private function showAnswersLeaderboard(Request $request): View
    {
        $answers = ForumAnswer::with(['user', 'question'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.forum.leaderboard-answers', compact('answers'));
    }

    /**
     * Display the specified forum question.
     *
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\View\View
     */
    public function show(ForumQuestion $question): View
    {
        $question->load(['user', 'answers.user']);
        return view('admin.forum.show', compact('question'));
    }

    /**
     * Store a newly created answer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAnswer(Request $request, ForumQuestion $question)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:10',
        ]);

        try {
            $question->answers()->create([
                'user_id' => Auth::id(),
                'content' => $validated['content'],
                'is_approved' => true, // Auto-approve admin answers
            ]);

            return back()->with('success', 'Answer posted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to post answer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question.
     *
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ForumQuestion $question)
    {
        try {
            $question->delete();
            return redirect()->route('admin.forum.index')
                ->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified answer.
     *
     * @param  \App\Models\ForumAnswer  $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAnswer(ForumAnswer $answer)
    {
        try {
            $answer->delete();
            return response()->json([
                'success' => true,
                'message' => 'Answer deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete answer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle approval status of a question.
     *
     * @param  \App\Models\ForumQuestion  $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleApproval(ForumQuestion $question)
    {
        try {
            $question->update(['is_approved' => !$question->is_approved]);
            $status = $question->is_approved ? 'approved' : 'unapproved';
            return back()->with('success', "Question has been {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update approval status: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new announcement.
     *
     * @return \Illuminate\View\View
     */
    public function createAnnouncement(): View
    {
        return view('admin.forum.announcements.create');
    }

    /**
     * Store a newly created announcement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'is_pinned' => 'boolean',
        ]);

        try {
            ForumQuestion::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'content' => $validated['content'],
                'is_announcement' => true,
                'is_pinned' => $validated['is_pinned'] ?? false,
                'is_approved' => true, // Auto-approve announcements
            ]);

            return redirect()->route('admin.forum.index')
                ->with('success', 'Announcement created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create announcement: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified announcement.
     *
     * @param  \App\Models\ForumQuestion  $announcement
     * @return \Illuminate\View\View
     */
    public function editAnnouncement(ForumQuestion $announcement): View
    {
        if (!$announcement->is_announcement) {
            abort(404);
        }

        return view('admin.forum.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ForumQuestion  $announcement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAnnouncement(Request $request, ForumQuestion $announcement)
    {
        if (!$announcement->is_announcement) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'is_pinned' => 'boolean',
        ]);

        try {
            $announcement->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'is_pinned' => $validated['is_pinned'] ?? false,
            ]);

            return redirect()->route('admin.forum.index')
                ->with('success', 'Announcement updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update announcement: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified announcement.
     *
     * @param  \App\Models\ForumQuestion  $announcement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAnnouncement(ForumQuestion $announcement)
    {
        if (!$announcement->is_announcement) {
            abort(404);
        }

        try {
            $announcement->delete();
            return redirect()->route('admin.forum.index')
                ->with('success', 'Announcement deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete announcement: ' . $e->getMessage());
        }
    }

    /**
     * Display the moderation dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function moderationIndex(): View
    {
        $pendingAnswersCount = ForumAnswer::where('is_approved', false)->count();
        
        // For now, we'll simulate reported content count
        // In a real implementation, you'd have a reports table
        $reportedContentCount = 0;

        return view('admin.forum.moderation.index', compact('pendingAnswersCount', 'reportedContentCount'));
    }

    /**
     * Display pending answers for approval.
     *
     * @return \Illuminate\View\View
     */
    public function pendingAnswers(): View
    {
        $pendingAnswers = ForumAnswer::with(['user', 'question.user'])
            ->where('is_approved', false)
            ->latest()
            ->paginate(15);

        return view('admin.forum.moderation.pending-answers', compact('pendingAnswers'));
    }

    /**
     * Approve a pending answer.
     *
     * @param  \App\Models\ForumAnswer  $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveAnswer(ForumAnswer $answer)
    {
        try {
            $answer->update(['is_approved' => true]);
            return back()->with('success', 'Answer approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve answer: ' . $e->getMessage());
        }
    }

    /**
     * Reject and delete a pending answer.
     *
     * @param  \App\Models\ForumAnswer  $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectAnswer(ForumAnswer $answer)
    {
        try {
            $answer->delete();
            return back()->with('success', 'Answer rejected and deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject answer: ' . $e->getMessage());
        }
    }

    /**
     * Display reported content.
     *
     * @return \Illuminate\View\View
     */
    public function reportedContent(): View
    {
        // For now, return empty since we don't have a reports table yet
        // In a real implementation, you'd fetch from a reports table
        $reportedItems = collect();

        return view('admin.forum.moderation.reported-content', compact('reportedItems'));
    }

    /**
     * Resolve a report by taking action on reported content.
     *
     * @param  int  $reportId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolveReport(int $reportId)
    {
        // Placeholder for report resolution logic
        return back()->with('success', 'Report resolved successfully.');
    }

    /**
     * Dismiss a report without taking action.
     *
     * @param  int  $reportId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dismissReport(int $reportId)
    {
        // Placeholder for report dismissal logic
        return back()->with('success', 'Report dismissed successfully.');
    }
}
