<?php

declare(strict_types=1);

namespace App\Order\Command\Cart\ChangeQuantityBatch;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

final class Command
{
    public function __construct(
        #[NotBlank]
        private readonly string $id,
        #[NotBlank]
        #[Valid]
        private readonly array $items
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return ItemDto[]
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
