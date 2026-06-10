<?php

declare(strict_types=1);

namespace Sylament\Support\Concerns;

use Sylament\Support\Services\PropertyAccessor;
use Symfony\Contracts\Service\Attribute\Required;

trait AccessesProperties
{
    private PropertyAccessor $propertyAccessor;

    public function getPropertyAccessor(): PropertyAccessor
    {
        return $this->propertyAccessor;
    }

    #[Required]
    public function setPropertyAccessor(PropertyAccessor $propertyAccessor): static
    {
        $this->propertyAccessor = $propertyAccessor;

        return $this;
    }

    /**
     * @param array<mixed>|object $target
     */
    public function getValue(array|object $target, ?string $key, mixed $default = null): mixed
    {
        return $this->propertyAccessor->getValue($target, $key, $default);
    }

    public function setValue(mixed &$target, string $key, mixed $value): mixed
    {
        return $this->propertyAccessor->setValue($target, $key, $value);
    }
}
