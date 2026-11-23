<?php declare(strict_types=1);

namespace Rush\Testing\Conformance;


final class ConformanceViolation
{
    public function __construct(
        public readonly string $rule,
        public readonly string $message,
    ) {
    }


    public function __toString(): string
    {
        return $this->rule .': '. $this->message;
    }
}
