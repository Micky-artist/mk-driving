<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case SUCCESSFUL = 'SUCCESSFUL';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';
    case EXPIRED = 'EXPIRED';
    case REJECTED = 'REJECTED';
    case TIMEOUT = 'TIMEOUT';
    case NOT_FOUND = 'NOT_FOUND';
    case ERROR = 'ERROR';
    case REFUNDED = 'REFUNDED';
}