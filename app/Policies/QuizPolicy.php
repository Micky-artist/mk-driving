<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view the list of quizzes
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Allow viewing if quiz is active or user is admin/creator
        return $quiz->is_active || $user->id === $quiz->creator_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins and content creators can create quizzes
        return $user->hasAnyRole(['admin', 'content_creator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // Only the creator or admin can update the quiz
        return $user->id === $quiz->creator_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        // Only the creator or admin can delete the quiz
        return $user->id === $quiz->creator_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view quiz statistics.
     */
    public function viewStats(User $user, Quiz $quiz): bool
    {
        // Only the creator or admin can view quiz statistics
        return $user->id === $quiz->creator_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can start a quiz attempt.
     */
    public function attempt(User $user, Quiz $quiz): bool
    {
        // Check if the quiz is active or the user is an admin
        if (!$quiz->is_active && !$user->hasRole('admin')) {
            return false;
        }

        // Check if the user has an active subscription if the quiz is premium
        if ($quiz->subscription_plan_slug && !$user->activeSubscriptions()->whereHas('plan', function($q) use ($quiz) {
            $q->where('slug', $quiz->subscription_plan_slug);
        })->exists()) {
            return false;
        }

        // Check if user has reached their quiz limit
        if ($user->hasReachedQuizLimit()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        return false;
    }
}
