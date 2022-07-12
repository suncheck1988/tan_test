<?php

declare(strict_types=1);

namespace App\Application\ValueObject;

use InvalidArgumentException;

final class Amount extends IntegerValueObject
{
    public static function fromRub(float $value): self
    {
        $value = $value * 100;
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('Invalid amount ' . $value);
        }

        return new self((int)$value);
    }

    public function toRub(): float
    {
        $value = $this->value / 100;

        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            $value = $value + .0;
        }

        return $value;
    }
}
