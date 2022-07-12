<?php

declare(strict_types=1);

namespace App\Integration\Command\Sync\Product;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class Command
{
    public function __construct(
        #[NotBlank]
        private string $id,
        #[NotBlank]
        private string $name,
        #[Positive]
        private float $price,
        #[PositiveOrZero]
        private int $balance,
        private bool $isActive
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
