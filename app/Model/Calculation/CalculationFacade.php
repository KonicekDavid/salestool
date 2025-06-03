<?php

declare(strict_types=1);

namespace App\Model\Calculation;

use Nette\Caching\Cache;
use Nette\Caching\Storage;

/**
 *
 */
class CalculationFacade implements CalculationFacadeInterface
{
    /** @var Cache $cache */
    private Cache $cache;

    /**
     * @param CalculationRepositoryInterface $calculationRepository
     */
    public function __construct(Storage $storage, private CalculationRepositoryInterface $calculationRepository)
    {
        $this->cache = new Cache($storage, 'calculation');
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
            ->setPrice($data->price)
            ->setCurrency($data->currency ?? '')
            ->setStatus(CalculationStatus::NEW->value);

        $calculation = $this->calculationRepository->insert($calculation);
        if ($calculation) {
            $this->cache->clean([Cache::Tags => 'calculation.list']);
            return $calculation;
        }
        return null;
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
        $this->cache->clean([Cache::Tags => 'calculation.list']);
        return $this->calculationRepository->update($calculation);
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array<mixed>
     * @throws \Dibi\Exception
     */
    public function getList(int $page, int $limit): array
    {
        $total = $this->calculationRepository->getTotalCount();
        $pages = (int)ceil($total / $limit);
        $page = $page > $pages ? $pages : $page;
        $offset = ($page - 1) * $limit;

        /** @var Calculation[] $data */
        $data = $this->cache->load('calculation' . $limit . '_' . $page, function () use ($limit, $offset) {
            return $this->calculationRepository->getListOfArrays($limit, $offset);
        }, [Cache::Expire => '20 minutes', Cache::Tags => 'calculation.list']);
        $result = [
            'data' => $data,
            'pagination' => [
                'page'       => $page,
                'limit'      => $limit,
                'totalPages' => $pages,
                'totalItems' => $total
            ]
        ];
        return $result;
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
