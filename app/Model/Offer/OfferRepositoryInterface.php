<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model;

use Dibi\Result;

interface OfferRepositoryInterface
{

    /**
     * @param int $limit
     * @param int $offset
     * @return Result
     */
    public function getList(int $limit = 10, int $offset = 0): Result;
}