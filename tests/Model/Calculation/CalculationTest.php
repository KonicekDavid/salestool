<?php

declare(strict_types=1);

namespace tests\Model\Calculation;

use App\Model\Calculation\Calculation;
use App\Model\Calculation\CalculationStatus;
use App\Model\Calculation\CalculationValidity;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

class CalculationTest extends TestCase
{
    public function testMapAndToArray(): void
    {
        $calculation = new Calculation();

        $data = [
            'id'            => 1,
            'customer_name' => 'Miroslav Laciný',
            'tariff_name'   => 'Na míru pro Míru',
            'price'         => 8765.4321,
            'currency'      => Calculation::DEFAULT_CURRENCY,
            'status'        => CalculationStatus::NEW->value,
            'created_at'    => (new \DateTimeImmutable('-1 day'))->format('Y-m-d H:i:s'),
            'last_update'   => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $calculation->map($data);

        Assert::same(1, $calculation->getId());
        Assert::same('Miroslav Laciný', $calculation->getCustomerName());
        Assert::same('Na míru pro Míru', $calculation->getTariffName());
        Assert::same(8765.43, $calculation->getPrice());
        Assert::same('CZK', $calculation->getCurrency());
        Assert::same(CalculationStatus::NEW->value, $calculation->getStatus());
        Assert::type(\DateTimeImmutable::class, $calculation->getCreatedAt());
        Assert::type(\DateTimeImmutable::class, $calculation->getLastUpdate());
        Assert::same(CalculationValidity::VALID, $calculation->getValidity());

        $array = $calculation->toArray();
        Assert::same(1, $array['id']);
        Assert::same('Miroslav Laciný', $array['customerName']);
        Assert::same('Na míru pro Míru', $array['tariffName']);
        Assert::same(8765.43, $array['price']);
        Assert::same('CZK', $array['currency']);
        Assert::same(CalculationStatus::NEW->value, $array['status']);
        Assert::same(CalculationValidity::VALID->value, $array['validity']);
    }

    public function testInvalidCurrencyThrowsException(): void
    {
        $calc = new Calculation();
        Assert::exception(fn() => $calc->setCurrency('USD'),
            \InvalidArgumentException::class,
            "Invalid currency, must be " . Calculation::DEFAULT_CURRENCY . ".");
    }

    public function testInvalidStatusThrowsException(): void
    {
        $calc = new Calculation();
        Assert::exception(fn() => $calc->setStatus('INVALID_STATUS'),
            \InvalidArgumentException::class,
            "Invalid status.");
    }

    public function testPriceThrowsException(): void
    {
        $calc = new Calculation();
        Assert::exception(fn() => $calc->setPrice(-999), \InvalidArgumentException::class, 'Price must be a positive number.');

        $calc = new Calculation();
        Assert::exception(fn() => $calc->setPrice('test'), \InvalidArgumentException::class, 'Invalid price format, must be a number.');
    }

    public function testCreate()
    {
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $name = 'Miroslav Novák';
        $tariffName = 'Na míru pro Míru';
        $status = CalculationStatus::NEW->value;
        $price = 300.00;
        $currency = 'CZK';
        $calculation = (new Calculation())
            ->setCustomerName($name)
            ->setTariffName($tariffName)
            ->setStatus($status)
            ->setPrice($price)
            ->setCurrency($currency);
        Assert::same($name, $calculation->getCustomerName());
        Assert::same($tariffName, $calculation->getTariffName());
        Assert::same($status, $calculation->getStatus());
        Assert::same($price, $calculation->getPrice());
        Assert::same($currency, $calculation->getCurrency());

        $data = [
            'id'            => 2,
            'customer_name' => $name,
            'tariff_name'   => $tariffName,
            'status'        => $status,
            'price'         => $price,
            'currency'      => $currency,
            'created_at'    => $date,
            'last_update'   => $date
        ];

        $newCalculation = new Calculation();
        $newCalculation->map($data);
        Assert::same(2, $newCalculation->getId());
        Assert::equal(new \DateTimeImmutable($date), $newCalculation->getCreatedAt());
    }
}

(new CalculationTest())->run();