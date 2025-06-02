<?php

declare(strict_types=1);

namespace App\Model\Calculation;

use Dibi\Connection;
use Dibi\Row;

class CalculationRepository implements CalculationRepositoryInterface
{
    public const LIMIT = 10;

    public const MAX_LIMIT = 100;

    public const OFFSET = 0;

    /**
     * @param Connection $connection
     */
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @param Calculation $calculation
     * @return Calculation
     * @throws \Dibi\Exception
     */
    public function insert(Calculation $calculation): ?Calculation
    {
        $this->connection->query("INSERT INTO calculation", [
            'customer_name' => $calculation->getCustomerName(),
            'tariff_name'   => $calculation->getTariffName(),
            'price'         => $calculation->getPrice(),
            'currency'      => $calculation->getCurrency(),
            'status'        => $calculation->getStatus()
        ]);
        $id = $this->connection->getInsertId();
        return $this->getById($id);
    }

    /**
     * @param Calculation $calculation
     * @return Calculation
     * @throws \Dibi\Exception
     * @throws \RuntimeException
     */
    public function update(Calculation $calculation): ?Calculation
    {
        $this->connection->query(
            "UPDATE calculation SET",
            ['status' => $calculation->getStatus()],
            'WHERE id = ?',
            $calculation->getId()
        );
        if ($this->connection->getAffectedRows()) {
            return $this->getById($calculation->getId());
        }
        throw new \RuntimeException('Unable to update calculation.');
    }

    /**
     * @param int $id
     * @return Calculation|null
     * @throws \Dibi\Exception
     */
    public function getById(int $id): ?Calculation
    {
        $row = $this->connection->fetch("SELECT * FROM calculation WHERE id = ?", $id);
        if (!$row) {
            return null;
        }
        $calculation = new Calculation();
        $calculation->map($row->toArray());
        return $calculation;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Calculation[]
     * @throws \Dibi\Exception
     */
    public function getList(int $limit, int $offset): array
    {
        if ($limit > self::MAX_LIMIT) {
            $limit = self::LIMIT;
        }

        if ($limit < 1) {
            $limit = self::LIMIT;
        }

        if ($offset < 0) {
            $offset = self::OFFSET;
        }
        $sql = <<<SQL
SELECT *
FROM calculation 
ORDER BY created_at DESC 
LIMIT ?
OFFSET ?
SQL;
        $rows = $this->connection->fetchAll($sql, $limit, $offset);
        $results = [];
        /** @var Row $row */
        foreach ($rows as $row) {
            $calculation = new Calculation();
            $calculation->map($row->toArray());
            $results[] = $calculation;
        }
        return $results;
    }
}
