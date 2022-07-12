<?php

declare(strict_types=1);

namespace App\Order\Command\Cart\ChangeQuantityBatch;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ItemDto
{
    public function __construct(
        #[NotBlank]
        private string $productId,
        #[PositiveOrZero]
        private int $quantity
    ) {
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
