<?php

namespace Double;

class FakeFirebaseStore
{
    public static array $tree = [];

    public static function reset(): void
    {
        self::$tree = [];
    }

    public static function get(string $path): mixed
    {
        $node = self::$tree;
        foreach (self::segments($path) as $segment) {
            if (!is_array($node) || !array_key_exists($segment, $node)) {
                return null;
            }
            $node = $node[$segment];
        }

        return $node;
    }

    public static function set(string $path, mixed $value): void
    {
        $segments = self::segments($path);
        if ($segments === []) {
            self::$tree = is_array($value) ? $value : [];
            return;
        }

        $node = &self::$tree;
        $last = array_key_last($segments);
        foreach ($segments as $index => $segment) {
            if ($index === $last) {
                $node[$segment] = $value;
                return;
            }
            if (!isset($node[$segment]) || !is_array($node[$segment])) {
                $node[$segment] = [];
            }
            $node = &$node[$segment];
        }
    }

    public static function remove(string $path): void
    {
        $segments = self::segments($path);
        if ($segments === []) {
            self::$tree = [];
            return;
        }

        $node = &self::$tree;
        $last = array_key_last($segments);
        foreach ($segments as $index => $segment) {
            if ($index === $last) {
                unset($node[$segment]);
                return;
            }
            if (!isset($node[$segment]) || !is_array($node[$segment])) {
                return;
            }
            $node = &$node[$segment];
        }
    }

    private static function segments(string $path): array
    {
        $path = trim($path, '/');

        return $path === '' ? [] : explode('/', $path);
    }
}
