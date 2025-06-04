<?php

declare(strict_types=1);

namespace App\Model\Calculation;

/**
 * CalculationStatus enum
 */
enum CalculationStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
