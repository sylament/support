<?php

declare(strict_types=1);

namespace Sylament\Support\Concerns;

use Closure;
use Sylament\Support\Data\ClosureParameter;
use Sylament\Support\Services\Invoker;
use Symfony\Contracts\Service\Attribute\Required;

trait EvaluatesClosures
{
    private Invoker $invoker;

    public function getInvoker(): Invoker
    {
        return $this->invoker;
    }

    #[Required]
    public function setInvoker(Invoker $invoker): static
    {
        $this->invoker = $invoker;

        return $this;
    }

    /**
     * @template T
     * @param T|callable(): T|null $value
     * @param array<string, mixed> $parameters
     * @return ($value is null ? null : T)
     */
    public function evaluate(mixed $value, array $parameters = []): mixed
    {
        if (!is_callable($value)) {
            return $value;
        }

        return $this->invoker->call($value, $parameters, $this->resolveDefaultClosureDependencyForEvaluationByName(...));
    }

    /**
     * @template T
     * @param T|callable():T|null $value
     * @return ($value is null ? null : T|Closure():T)
     */
    public function evaluatable(mixed $value): mixed
    {
        if (is_callable($value)) {
            return $value(...);
        }

        return $value;
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): ?ClosureParameter
    {
        return null;
    }
}
