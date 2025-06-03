<?php

declare(strict_types=1);

namespace tests\Model\Calculation;

use App\Model\Calculation\Calculation;
use App\Model\Calculation\CalculationStatus;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

class CalculationTest extends TestCase
{
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
            'id' => 2,
            'customer_name' => $name,
            'tariff_name' => $tariffName,
            'status' => $status,
            'price' => $price,
            'currency' => $currency,
            'created_at' => $date,
            'last_update' => $date
        ];

        $newCalculation = new Calculation();
        $newCalculation->map($data);
        Assert::same( 2, $newCalculation->getId());
        Assert::equal( new \DateTimeImmutable($date), $newCalculation->getCreatedAt());
    }
}

(new CalculationTest())->run();