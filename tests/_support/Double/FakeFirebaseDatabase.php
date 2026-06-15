<?php

namespace Double;

class FakeFirebaseDatabase
{
    public function getReference(string $path = ''): FakeFirebaseReference
    {
        return new FakeFirebaseReference($path);
    }
}
