<?php
declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model;

interface OfferFacadeInterface
{

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getList(int $limit = 10, int $offset = 0): array;
}