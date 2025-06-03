<?php

declare(strict_types=1);

namespace App\Model\Calculation;

use Nette\Schema\Expect;
use Nette\Schema\Processor;

final class CalculationDataValidator
{
    /**
     * @param string $json
     * @return CalculationSchema
     */
    public function validateForCreate(string $json): CalculationSchema
    {
        try {
            $schema = Expect::from(new CalculationSchema(), [
                'customer_name' => Expect::string()->min(2)->max(100)->required(),
                'tariff_name' => Expect::string()->min(2)->max(150)->required(),
                'price' => Expect::float()->min(0.00)->required(),
                'currency' => Expect::string()->min(1)->max(10)->required(),
            ]);
            $data = \json_decode($json, true);
            $calculationData = (new Processor())->process($schema, $data);
            if ($calculationData instanceof CalculationSchema) {
                return $calculationData;
            }
            throw new \RuntimeException('Internal error.');
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }

    /**
     * @param string $json
     * @return CalculationSchema
     */
    public function validateForUpdate(string $json): CalculationSchema
    {
        try {
            $schema = Expect::from(new CalculationSchema(), [
                'status' => Expect::anyOf(
                    CalculationStatus::ACCEPTED->value,
                    CalculationStatus::PENDING->value,
                    CalculationStatus::REJECTED->value
                )->required()
            ]);
            $data = \json_decode($json, true);
            $calculationData = (new Processor())->process($schema, $data);
            if ($calculationData instanceof CalculationSchema) {
                return $calculationData;
            }
            throw new \RuntimeException('Internal error.');
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }
    }
}
