<?php

namespace Double;

class FakeFirebaseSnapshot
{
    public function __construct(private mixed $value)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function exists(): bool
    {
        return $this->value !== null;
    }
}
