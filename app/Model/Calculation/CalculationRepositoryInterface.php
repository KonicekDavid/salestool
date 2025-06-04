<?php

declare(strict_types=1);

namespace App\Model\Calculation;

/**
 * CalculationRepositoryInterface interface
 */
interface CalculationRepositoryInterface
{
    /**
     * @param Calculation $calculation
     * @return Calculation|null
     */
    public function insert(Calculation $calculation): ?Calculation;

    /**
     * @param Calculation $calculation
     * @return Calculation|null
     */
    public function update(Calculation $calculation): ?Calculation;

    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param int $id
     * @return Calculation|null
     */
    public function getById(int $id): ?Calculation;

    /**
     * @param int $limit
     * @param int $offset
     * @return Calculation[]
     * @throws \Dibi\Exception
     */
    public function getList(int $limit, int $offset): array;
}
