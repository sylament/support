<?php

declare(strict_types=1);

namespace Sylament\Support\Services;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor as SymfonyPropertyAccessor;

class PropertyAccessor
{
    /**
     * @param array<mixed>|object $target
     */
    public function getValue(array|object $target, ?string $key, mixed $default = null): mixed
    {
        if ($key === null) {
            return $target;
        }

        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $this->getPropertyAccessor()->getValue($target, $segment);
            } else {
                return $default;
            }
        }

        return $target;
    }

    /**
     * @param string|array<string> $key
     */
    public function setValue(mixed &$target, string|array $key, mixed $value): mixed
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        /**
         * @var string $segment
         */
        $segment = array_shift($segments);

        if (is_array($target)) {
            if ($segments !== []) {
                if (!array_key_exists($segment, $target)) {
                    $target[$segment] = [];
                }

                $this->setValue($target[$segment], $segments, $value);
            } else {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments !== []) {
                if (!isset($target->{$segment})) {
                    $this->getPropertyAccessor()->setValue($target, $segment, []);
                }

                $this->setValue($target->{$segment}, $segments, $value);
            } else {
                $this->getPropertyAccessor()->setValue($target, $segment, $value);
            }
        } else {
            $target = [];

            if ($segments !== []) {
                $this->setValue($target[$segment], $segments, $value);
            } else {
                $target[$segment] = $value;
            }
        }

        return $target;
    }

    private function getPropertyAccessor(): SymfonyPropertyAccessor
    {
        return PropertyAccess::createPropertyAccessor();
    }
}
