<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model;

use Dibi\Connection;
use Dibi\Result;
use Dibi\Row;

/**
 *
 */
class OfferRepository implements OfferRepositoryInterface
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Result
     * @throws \Dibi\Exception
     */
    public function getList(int $limit = 10, int $offset = 0): Result
    {
        if ($limit > 100) {
            $limit = 100;
        }
        $sql = 'SELECT * FROM offer ORDER BY created_at DESC LIMIT ? OFFSET ?';
        return $this->connection->query($sql, $limit, $offset);
    }
}