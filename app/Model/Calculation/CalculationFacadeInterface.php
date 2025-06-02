<?php

declare(strict_types=1);

namespace App\Model\Calculation;

interface CalculationFacadeInterface
{
    /**
     * @param CalculationSchema $data
     * @return Calculation|null
     */
    public function create(CalculationSchema $data): ?Calculation;

    /**
     * @param Calculation $calculation
     * @param CalculationSchema $data
     * @return Calculation|null
     */
    public function update(Calculation $calculation, CalculationSchema $data): ?Calculation;

    /**
     * @param int $limit
     * @param int $offset
     * @return array<mixed>
     */
    public function getList(int $limit, int $offset): array;
}
