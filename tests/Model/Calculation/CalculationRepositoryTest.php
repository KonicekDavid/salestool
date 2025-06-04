<?php

declare(strict_types=1);

namespace tests\Model\Calculation;

use App\Model\Calculation\Calculation;
use App\Model\Calculation\CalculationRepository;
use App\Model\Calculation\CalculationStatus;
use Dibi\Connection;
use Dibi\Row;
use Mockery;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * CalculationRepositoryTest class
 */
final class CalculationRepositoryTest extends TestCase
{
    /**
     * @var
     */
    private $connection;

    /**
     * @var CalculationRepository
     */
    private CalculationRepository $repository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->connection = Mockery::mock(Connection::class);
        $this->repository = new CalculationRepository($this->connection);
    }

    /**
     * @return void
     * @throws \Dibi\Exception
     */
    public function testInsert(): void
    {
        $calculation = $this->createMockCalculation();

        $this->connection->shouldReceive('query')
            ->once()
            ->with('INSERT INTO calculation', Mockery::type('array'));

        $this->connection->shouldReceive('getInsertId')
            ->once()
            ->andReturn(1);

        $this->connection->shouldReceive('fetch')
            ->once()
            ->with('SELECT * FROM calculation WHERE id = ?', $calculation->getId())
            ->andReturn(new Row($calculation->toArray()));

        $result = $this->repository->insert($calculation);

        Assert::equal($calculation->getCustomerName(), $result->getCustomerName());
    }

    /**
     * @return void
     * @throws \Dibi\Exception
     */
    public function testUpdate(): void
    {
        $calculation = $this->createMockCalculation();
        $calculation->setStatus(CalculationStatus::ACCEPTED->value);

        $this->connection->shouldReceive('query')
            ->once()
            ->with(
                'UPDATE calculation SET',
                ['status' => CalculationStatus::ACCEPTED->value],
                'WHERE id = ?',
                $calculation->getId()
            );

        $this->connection->shouldReceive('getAffectedRows')
            ->once()
            ->andReturn(1);

        $this->connection->shouldReceive('fetch')
            ->once()
            ->with('SELECT * FROM calculation WHERE id = ?', $calculation->getId())
            ->andReturn(new Row($calculation->toArray()));

        $result = $this->repository->update($calculation);

        Assert::equal(CalculationStatus::ACCEPTED->value, $result->getStatus());
    }

    /**
     * @return void
     * @throws \Dibi\Exception
     */
    public function testGetByIdReturnsNull(): void
    {
        $this->connection->shouldReceive('fetch')
            ->once()
            ->with('SELECT * FROM calculation WHERE id = ?', 999)
            ->andReturn(null);

        $result = $this->repository->getById(999);

        Assert::null($result);
    }

    /**
     * @return void
     * @throws \Dibi\Exception
     */
    public function testGetTotalCount(): void
    {
        $this->connection->shouldReceive('fetch')
            ->once()
            ->with('SELECT count(*) as "count" FROM calculation')
            ->andReturn(['count' => 42]);

        $count = $this->repository->getTotalCount();

        Assert::same(42, $count);
    }

    /**
     * @return void
     * @throws \Dibi\Exception
     */
    public function testGetList(): void
    {
        $mockRow = new Row($this->createMockCalculation()->toArray());

        $this->connection->shouldReceive('fetchAll')
            ->once()
            ->andReturn([$mockRow]);

        $results = $this->repository->getList(10, 0);

        Assert::count(1, $results);
        Assert::type(Calculation::class, $results[0]);
    }

    /**
     * @return Calculation
     */
    private function createMockCalculation(): Calculation
    {
        $calc = new Calculation();
        $calc->map([
            'id' => 1,
            'customer_name' => 'Test Customer',
            'tariff_name' => 'Tariff A',
            'price' => 100.00,
            'currency' => 'CZK',
            'status' => 'new',
            'created_at' => '2025-01-01 00:00:00',
            'last_update' => '2025-01-01 00:00:00',
        ]);
        return $calc;
    }
}

//(new CalculationRepositoryTest())->run();