<?php
declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Calculation;

class CalculationSchema
{
    public int|null $id = null;
    public string|null $customer_name = null;
    public string|null $tariff_name = null;
    public float|null $price = null;
    public string|null $currency = null;
    public string|null $status = null;
    public string|null $created_at = null;
    public string|null $last_update = null;
}