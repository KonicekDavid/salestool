<?php

declare(strict_types=1);

namespace App\Model\Calculation;

interface CalculationRepositoryInterface
{
    public function insert(Calculation $calculation): ?Calculation;

    public function update(Calculation $calculation): ?Calculation;

    public function getById(int $id): ?Calculation;

    /**
     * @param int $limit
     * @param int $offset
     * @return Calculation[]
     * @throws \Dibi\Exception
     */
    public function getList(int $limit, int $offset): array;
}
