<?php

declare(strict_types=1);

namespace App\Model\Calculation;

/**
 * CalculationValidity enum
 */
enum CalculationValidity: string
{
    case VALID = 'valid';
    case EXPIRED = 'expired';
}
