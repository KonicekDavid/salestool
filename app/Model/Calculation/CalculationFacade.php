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
     * @param CalculationSchema $data
     * @return Calculation|null
     */
    public function create(CalculationSchema $data): ?Calculation
    {
        $calculation = (new Calculation())
            ->setCustomerName($data->customer_name ?? '')
            ->setTariffName($data->tariff_name ?? '')
            ->setPrice($data->price ?? 0)
            ->setCurrency($data->currency ?? '')
            ->setStatus(CalculationStatus::NEW->value);

        return $this->calculationRepository->insert($calculation);
    }

    /**
     * @param Calculation $calculation
     * @param CalculationSchema $data
     * @return Calculation|null
     */
    public function update(Calculation $calculation, CalculationSchema $data): ?Calculation
    {
        if (!$data->status) {
            throw new \InvalidArgumentException('Parameter status is required.');
        }

        $this->validateNewStatus($calculation, $data->status);
        $calculation->setStatus($data->status);
        return $this->calculationRepository->update($calculation);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array<mixed>
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