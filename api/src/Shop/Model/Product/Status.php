<?php

declare(strict_types=1);

namespace App\Shop\Model\Product;

use App\Application\ValueObject\EnumValueObject;

/**
 * @method static active()
 * @method static inactive()
 */
class Status extends EnumValueObject
{
    public const ACTIVE = 100;
    public const INACTIVE = 200;

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->value === self::INACTIVE;
    }
}
