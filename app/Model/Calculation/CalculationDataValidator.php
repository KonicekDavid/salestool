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
    public function validate(string $json): CalculationSchema
    {
        $schema = Expect::from(new CalculationSchema());
        $data = \json_decode($json, true);
        $calculationData = (new Processor())->process($schema, $data);
        if ($calculationData instanceof CalculationSchema) {
            return $calculationData;
        }
        throw new \RuntimeException('Invalid calculation schema');
    }
}
