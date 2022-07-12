<?php

declare(strict_types=1);

namespace App\Auth\Model\User;

use App\Application\ValueObject\EnumValueObject;

/**
 * @method static user()
 * @method static admin()
 */
final class Role extends EnumValueObject
{
    public const USER = 100;
    public const ADMIN = 200;

    public function isUser(): bool
    {
        return $this->value === self::USER;
    }

    public function isAdmin(): bool
    {
        return $this->value === self::ADMIN;
    }
}
