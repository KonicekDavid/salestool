<?php

declare(strict_types=1);
/**
 * @author David Koníček
 */

namespace App\Model;

/**
 *
 */
class CalculationFacade implements CalculationFacadeInterface
{
    /**
     * @var CalculationRepositoryInterface
     */
    private CalculationRepositoryInterface $calculationRepository;

    /**
     * @param CalculationRepositoryInterface $calculationRepository
     */
    public function __construct(CalculationRepositoryInterface $calculationRepository)
    {
        $this->calculationRepository = $calculationRepository;
    }

    /**
     * @inheritDoc
     */
    public function getList(int $limit = 10, int $offset = 0): array
    {
        $result = $this->calculationRepository->getList($limit, $offset);
        $data = [];
        foreach ($result as $offer) {
            $data[] = [
                'customer' => $offer['customer_name'],
                'tariff' => $offer['tariff_name'],
                'price' => sprintf("%s %s", $offer['price'], $offer['currency']),
                'status' => $offer['status'],
                'createdAt' => (new \DateTime($offer['created_at']))->format('d.m.Y H:i:s'),
            ];
        }
        return $data;
    }
}