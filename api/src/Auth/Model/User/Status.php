<?php

declare(strict_types=1);

namespace App\Auth\Model\User;

use App\Application\ValueObject\EnumValueObject;

/**
 * @method static wait()
 * @method static active()
 */
final class Status extends EnumValueObject
{
    public const WAIT = 100;
    public const ACTIVE = 200;

    public function isWait(): bool
    {
        return $this->value === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }
}
