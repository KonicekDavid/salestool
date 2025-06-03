<?php

declare(strict_types=1);

use App\Model\Calculation\Calculation;
use App\Model\Calculation\CalculationFacade;
use App\Model\Calculation\CalculationRepositoryInterface;
use App\Model\Calculation\CalculationSchema;
use App\Model\Calculation\CalculationStatus;
use Nette\Caching\Storages\DevNullStorage;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

final class CalculationFacadeTest extends TestCase
{
    private CalculationRepositoryInterface $repository;
    private DevNullStorage $storage;
    private CalculationFacade $facade;

    protected function setUp(): void
    {
        $this->repository = Mockery::mock(CalculationRepositoryInterface::class);
        $this->storage = new DevNullStorage();
        $this->facade = new CalculationFacade($this->storage, $this->repository);
    }

    public function testCreate(): void
    {
        $expected = (new Calculation())
            ->setCustomerName('Míra Novák')
            ->setTariffName('Na míru pro Míru')
            ->setPrice(123.45)
            ->setCurrency('CZK')
            ->setStatus(CalculationStatus::NEW->value);

        $this->repository
            ->shouldReceive('insert')
            ->once()
            ->with(Mockery::type(Calculation::class))
            ->andReturn($expected);

        $schema = new CalculationSchema();
        $schema->customer_name = 'Míra Novák';
        $schema->tariff_name = 'Na míru pro Míru';
        $schema->price = 123.45;
        $schema->currency = 'CZK';

        $actual = $this->facade->create($schema);

        Assert::type(Calculation::class, $actual);
        Assert::same('Míra Novák', $actual->getCustomerName());
    }

    public function testUpdateValid(): void
    {
        $calc = (new Calculation())->setStatus(CalculationStatus::REJECTED->value);
        $schema = new CalculationSchema();
        $schema->status = CalculationStatus::REJECTED->value;

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with($calc)
            ->andReturn($calc);

        $updated = $this->facade->update($calc, $schema);
        Assert::same($calc, $updated);
    }

    public function testUpdateInvalid(): void
    {
        $calc = (new Calculation())->setStatus(CalculationStatus::NEW->value);
        $schema = new CalculationSchema();
        $schema->status = 'INVALID';

        Assert::exception(function () use ($calc, $schema) {
            $this->facade->update($calc, $schema);
        }, \InvalidArgumentException::class);
    }

    public function testGetList(): void
    {
        $calc = (new Calculation())
            ->setCustomerName('Míra Novák')
            ->setTariffName('Na míru pro Míru')
            ->setPrice(123.45)
            ->setCurrency('CZK')
            ->setStatus(CalculationStatus::NEW->value);
        $list = [$calc];

        $this->repository
            ->shouldReceive('getTotalCount')
            ->once()
            ->andReturn(1);

        $this->repository
            ->shouldReceive('getListOfArrays')
            ->once()
            ->with(10, 0)
            ->andReturn($list);

        $result = $this->facade->getList(1, 10);

        Assert::type('array', $result);
        Assert::count(1, $result['data']);
        Assert::same(1, $result['pagination']['totalItems']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}

(new CalculationFacadeTest())->run();