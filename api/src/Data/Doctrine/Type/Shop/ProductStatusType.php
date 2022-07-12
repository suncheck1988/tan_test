<?php

declare(strict_types=1);

namespace App\Data\Doctrine\Type\Shop;

use App\Data\Doctrine\Type\EnumType;
use App\Shop\Model\Product\Status;

class ProductStatusType extends EnumType
{
    public const NAME = 'shop_product_status';

    public function getClassName(): string
    {
        return Status::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
