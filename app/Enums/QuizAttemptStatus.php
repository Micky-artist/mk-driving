<?php

namespace App\Enums;

enum QuizAttemptStatus: string
{
    case IN_PROGRESS = 'IN_PROGRESS';
    case COMPLETED = 'COMPLETED';
}