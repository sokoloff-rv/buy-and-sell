<?php

namespace Double;

class FakeFirebaseReference
{
    public function __construct(private string $path = '')
    {
    }

    public function getChild(string $child): self
    {
        return new self(trim($this->path . '/' . $child, '/'));
    }

    public function getSnapshot(): FakeFirebaseSnapshot
    {
        return new FakeFirebaseSnapshot(FakeFirebaseStore::get($this->path));
    }

    public function getValue(): mixed
    {
        return FakeFirebaseStore::get($this->path);
    }

    public function set(mixed $value): void
    {
        FakeFirebaseStore::set($this->path, $value);
    }

    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            FakeFirebaseStore::set(trim($this->path . '/' . $key, '/'), $value);
        }
    }

    public function remove(): void
    {
        FakeFirebaseStore::remove($this->path);
    }
}
