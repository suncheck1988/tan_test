<?php

declare(strict_types=1);

namespace App\Integration\Dto\Sync;

use App\Application\ValueObject\Amount;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;

class ProductDto
{
    public function __construct(
        private Uuid $id,
        private string $name,
        private Amount $price,
        private Quantity $balance,
        private bool $isActive
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Amount
    {
        return $this->price;
    }

    public function getBalance(): Quantity
    {
        return $this->balance;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
