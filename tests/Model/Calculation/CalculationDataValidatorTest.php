<?php

declare(strict_types=1);

namespace tests\Model\Calculation;

use App\Model\Calculation\CalculationDataValidator;
use App\Model\Calculation\CalculationSchema;
use App\Model\Calculation\CalculationStatus;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * CalculationDataValidatorTest class
 */
final class CalculationDataValidatorTest extends TestCase
{
    /**
     * @return void
     */
    public function testValidationForCreate()
    {
        $customerName = 'Miroslav Novák';
        $tariffName = 'Na míru pro Míru';
        $price = 300.50;
        $currency = 'CZK';

        $patternSchema = new CalculationSchema();
        $patternSchema->customer_name = $customerName;
        $patternSchema->tariff_name = $tariffName;
        $patternSchema->price = $price;
        $patternSchema->currency = $currency;

        $validator = new CalculationDataValidator();
        $data = [
            'customer_name' => $customerName,
            'tariff_name'   => $tariffName,
            'price'         => $price,
            'currency'      => $currency
        ];
        $json = json_encode($data);
        $schema = $validator->validateForCreate($json);

        Assert::equal($patternSchema, $schema);

        $data = [
            'customer_name' => $customerName,
            'test'          => 'test',
            'price'         => $price,
            'currency'      => $currency
        ];
        $json = json_encode($data);
        Assert::exception(
            fn() => $validator->validateForCreate($json),
            \InvalidArgumentException::class
        );
    }

    /**
     * @return void
     */
    public function testValidationForUpdate()
    {
        $status = CalculationStatus::REJECTED->value;

        $patternSchema = new CalculationSchema();
        $patternSchema->status = $status;

        $validator = new CalculationDataValidator();
        $data = ['status' => $status];
        $json = json_encode($data);
        $schema = $validator->validateForUpdate($json);

        Assert::equal($patternSchema, $schema);

        $data = ['status' => CalculationStatus::NEW->value];
        $json = json_encode($data);
        Assert::exception(
            fn() => $validator->validateForUpdate($json),
            \InvalidArgumentException::class
        );
    }
}

(new CalculationDataValidatorTest())->run();
