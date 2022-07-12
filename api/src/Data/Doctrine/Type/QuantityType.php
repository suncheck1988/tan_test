<?php

declare(strict_types=1);

namespace App\Data\Doctrine\Type;

use App\Application\ValueObject\Quantity;

class QuantityType extends IntegerType
{
    public const NAME = 'quantity';

    protected function getClassName(): string
    {
        return Quantity::class;
    }
}
