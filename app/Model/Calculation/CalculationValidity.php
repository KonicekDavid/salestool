<?php
declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Calculation;

enum CalculationValidity: string
{
    case VALID = 'valid';
    case EXPIRED = 'expired';
}