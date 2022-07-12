<?php

declare(strict_types=1);

namespace App\Data\Doctrine\Type;

use App\Application\ValueObject\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

final class UuidType extends GuidType
{
    public const NAME = 'uuid';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Uuid ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? new Uuid((string)$value) : null;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName(): string
    {
        assert(is_string(self::NAME));
        return self::NAME;
    }
}
