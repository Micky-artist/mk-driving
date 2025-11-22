<?php

namespace App\Policies;

use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizAttemptPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view their own attempts
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only the attempt owner or admin can view the attempt
        return $user->id === $quizAttempt->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a quiz attempt
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only the attempt owner or admin can update the attempt
        return $user->id === $quizAttempt->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only the attempt owner or admin can delete the attempt
        return $user->id === $quizAttempt->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can submit answers for the attempt.
     */
    public function submitAnswers(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only the attempt owner can submit answers
        if ($user->id !== $quizAttempt->user_id) {
            return false;
        }

        // Can only submit answers for in-progress attempts
        return $quizAttempt->status === 'in_progress';
    }

    /**
     * Determine whether the user can view the attempt results.
     */
    public function viewResults(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only the attempt owner or admin can view results
        if ($user->id !== $quizAttempt->user_id && !$user->hasRole('admin')) {
            return false;
        }

        // Can only view results for completed attempts
        return $quizAttempt->status === 'completed';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QuizAttempt $quizAttempt): bool
    {
        // Not implemented
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only admins can force delete attempts
        return $user->hasRole('admin');
    }
}
