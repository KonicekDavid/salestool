<?php

declare(strict_types=1);

namespace App\Model\Calculation;

/**
 * CalculationSchema schema
 */
final class CalculationSchema
{
    public int|null $id = null;
    public string|null $customer_name = null;
    public string|null $tariff_name = null;
    public float|null $price = null;
    public string|null $currency = null;
    public string|null $status = null;
}
