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
class CalculationRepository implements CalculationRepositoryInterface
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
    public function getList(int $limit = 10, int $offset = 0): array
    {
        if ($limit > 100) {
            $limit = 100;
        }
        $sql = 'SELECT * FROM calculation ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $rows = $this->connection->query($sql, $limit, $offset);
        return array_map(fn($row) => $this->mapRowToOffer($row), $rows);
    }

    private function mapRowToOffer(array|\Dibi\Row $row): Calculation
    {
        return new Calculation(
            (int)$row['id'],
            $row['customer_name'],
            $row['tariff_name'],
            (float)$row['price'],
            $row['currency']
        );
    }
}