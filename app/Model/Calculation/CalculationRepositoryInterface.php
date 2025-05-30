<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Calculation;

use Dibi\Result;

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