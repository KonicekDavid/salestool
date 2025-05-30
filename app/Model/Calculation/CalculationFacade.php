<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model\Calculation;

/**
 *
 */
class CalculationFacade implements CalculationFacadeInterface
{
    /**
     * @param CalculationRepositoryInterface $calculationRepository
     */
    public function __construct(private CalculationRepositoryInterface $calculationRepository)
    {
    }

    /**
     * @param array $data
     * @return Calculation
     */
    public function create(array $data): Calculation
    {
        $this->validateInputData($data);
        $calculation = (new Calculation())
            ->setCustomerName($data['customer_name'])
            ->setTariffName($data['tariff_name'])
            ->setPrice($data['price'])
            ->setCurrency($data['currency'])
            ->setStatus(CalculationStatus::NEW->value);

        return $this->calculationRepository->insert($calculation);
    }

    /**
     * @param Calculation $calculation
     * @param array $data
     * @return Calculation
     * @throws \InvalidArgumentException
     */
    public function update(Calculation $calculation, array $data): Calculation
    {
        if (empty($data['status'])) {
            throw new \InvalidArgumentException('Parameter status is required.');
        }

        $this->validateNewStatus($calculation, $data['status']);
        $calculation->setStatus($data['status']);
        return $this->calculationRepository->update($calculation);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Dibi\Exception
     */
    public function getList(int $limit, int $offset): array
    {
        $data = $this->calculationRepository->getList($limit, $offset);
        $data = array_map(function (Calculation $calculation) {
            return $calculation->toArray();
        }, $data);
        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    private function validateInputData(array $data): void
    {
        $requiredFields = ['customer_name', 'tariff_name', 'price', 'currency'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Parameter {$field} is required.");
            }
        }
    }

    /**
     * @param Calculation $calculation
     * @param string $status
     * @return void
     */
    private function validateNewStatus(Calculation $calculation, string $status): void
    {
        $newStatus = CalculationStatus::tryFrom($status);
        if (!$newStatus) {
            throw new \InvalidArgumentException('Invalid status.');
        }

        $oldStatus = CalculationStatus::from($calculation->getStatus());
        if ($newStatus === CalculationStatus::NEW && $newStatus !== $oldStatus) {
            throw new \InvalidArgumentException('Invalid status.');
        }
    }
}