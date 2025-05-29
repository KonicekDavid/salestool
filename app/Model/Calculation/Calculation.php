<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model;

class Calculation
{
    public function __construct(
        public int $id,
        public string $customerName,
        public string $tariffName,
        public float $price,
        public string $currency,
    ) {
    }
}