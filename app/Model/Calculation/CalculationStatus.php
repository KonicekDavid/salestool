<?php

declare(strict_types=1);

/**
 * @author David Koníček
 */

namespace App\Model\Calculation;

enum CalculationStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}