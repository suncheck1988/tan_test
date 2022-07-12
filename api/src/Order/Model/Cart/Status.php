<?php

declare(strict_types=1);

namespace App\Order\Model\Cart;

use App\Application\ValueObject\EnumValueObject;

/**
 * @method static active()
 */
final class Status extends EnumValueObject
{
    public const ACTIVE = 100;
}
