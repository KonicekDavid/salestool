<?php

declare(strict_types=1);

namespace App\Model\Calculation;

/**
 * Calculation class
 */
final class Calculation
{
    /**
     * Default currency
     */
    public const DEFAULT_CURRENCY = 'CZK';

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $customerName;

    /**
     * @var string
     */
    private string $tariffName;

    /**
     * @var float
     */
    private float $price;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $lastUpdate;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTariffName(): string
    {
        return $this->tariffName;
    }

    /**
     * @param string $tariffName
     * @return $this
     */
    public function setTariffName(string $tariffName): self
    {
        $this->tariffName = $tariffName;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return $this
     */
    public function setPrice(mixed $price): self
    {
        $price = $this->validatePrice($price);
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCurrency(string $currency): self
    {
        if ($currency !== self::DEFAULT_CURRENCY) {
            throw new \InvalidArgumentException("Invalid currency, must be " . self::DEFAULT_CURRENCY . ".");
        }
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $newStatus = CalculationStatus::tryFrom($status);
        if (!$newStatus) {
            throw new \InvalidArgumentException("Invalid status.");
        }
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getLastUpdate(): \DateTimeImmutable
    {
        return $this->lastUpdate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return CalculationValidity
     */
    public function getValidity(): CalculationValidity
    {
        $now = new \DateTimeImmutable();
        $diff = $this->getCreatedAt()->diff($now);
        return $diff->days > 14 ? CalculationValidity::EXPIRED : CalculationValidity::VALID;
    }

    /**
     * @param int $id
     * @return $this
     */
    private function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     * @return $this
     */
    private function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $lastUpdate
     * @return $this
     */
    private function setLastUpdate(\DateTimeImmutable $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    /**
     * @param mixed $price
     * @return float
     */
    private function validatePrice(mixed $price): float
    {
        if (\is_numeric($price)) {
            if ($price < 0) {
                throw new \InvalidArgumentException('Price must be a positive number.');
            }
            return round((float)$price, 2);
        }
        throw new \InvalidArgumentException('Invalid price format, must be a number.');
    }

    /**
     * @param array<mixed> $data
     * @return void
     */
    public function map(array $data): void
    {
        $id = isset($data['id']) && \is_numeric($data['id']) ? (int)$data['id'] : null;
        $customerName = isset($data['customer_name']) && is_string(
            $data['customer_name']
        ) ? $data['customer_name'] : null;
        $tariffName = isset($data['tariff_name']) && is_string($data['tariff_name']) ? $data['tariff_name'] : null;
        $price = isset($data['price']) && \is_numeric($data['price']) ? (float)$data['price'] : null;
        $currency = isset($data['currency']) && is_string($data['currency']) ? $data['currency'] : null;
        $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : null;
        $createdAt = isset($data['created_at']) && is_string($data['created_at']) ? $data['created_at'] : null;
        $lastUpdate = isset($data['last_update']) && is_string($data['last_update']) ? $data['last_update'] : null;

        if ($id === null) {
            throw new \InvalidArgumentException('Id cannot be null.');
        }

        if ($customerName === null) {
            throw new \InvalidArgumentException('Customer name cannot be null.');
        }

        if ($tariffName === null) {
            throw new \InvalidArgumentException('Tariff name cannot be null.');
        }

        if ($price === null) {
            throw new \InvalidArgumentException('Price cannot be null.');
        }

        if ($currency === null) {
            throw new \InvalidArgumentException('Currency cannot be null.');
        }

        if ($status === null) {
            throw new \InvalidArgumentException('Status cannot be null.');
        }

        if ($createdAt === null) {
            throw new \InvalidArgumentException('CreatedAt cannot be null.');
        }

        if ($lastUpdate === null) {
            throw new \InvalidArgumentException('LastUpdate cannot be null.');
        }

        $this->setId($id);
        $this->setCustomerName($customerName);
        $this->setTariffName($tariffName);
        $this->setPrice($price);
        $this->setCurrency($currency);
        $this->setStatus($status);
        $this->setCreatedAt(new \DateTimeImmutable($createdAt));
        $this->setLastUpdate(new \DateTimeImmutable($lastUpdate));
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->getId(),
            'customerName' => $this->getCustomerName(),
            'tariffName'   => $this->getTariffName(),
            'price'        => $this->getPrice(),
            'currency'     => $this->getCurrency(),
            'status'       => $this->getStatus(),
            'createdAt'    => $this->getCreatedAt()->format('d.m.Y H:i:s'),
            'lastUpdate'   => $this->getLastUpdate()->format('d.m.Y H:i:s'),
            'validity'     => $this->getValidity()->value
        ];
    }
}
