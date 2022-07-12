<?php

declare(strict_types=1);

namespace App\Application\ValueObject;

use Assert\Assertion;

abstract class StringValueObject extends AbstractValueObject
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        Assertion::maxLength($this->value, 255, 'Max 255 symbols', $this->getPropertyPath());
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqualTo(self $value): bool
    {
        return $this->value === $value->getValue();
    }
}
