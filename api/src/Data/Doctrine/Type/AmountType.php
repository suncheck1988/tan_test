<?php

declare(strict_types=1);

namespace App\Data\Doctrine\Type;

use App\Application\ValueObject\Amount;

class AmountType extends IntegerType
{
    public const NAME = 'amount';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getClassName(): string
    {
        return Amount::class;
    }
}
