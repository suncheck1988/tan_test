<?php

declare(strict_types=1);

namespace App\Data\Doctrine\Type\Order;

use App\Data\Doctrine\Type\EnumType;
use App\Order\Model\Cart\Status;

class CartStatusType extends EnumType
{
    public const NAME = 'order_cart_status';

    public function getClassName(): string
    {
        return Status::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
