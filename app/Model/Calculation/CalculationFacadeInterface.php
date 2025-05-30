<?php
declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Calculation;

interface CalculationFacadeInterface
{
    public function create(array $data): Calculation;

    public function update(Calculation $calculation, array $data): Calculation;

    public function getList(int $limit, int $offset): array;
}