<?php declare(strict_types=1);

namespace Rush\Testing\Conformance;

use RuntimeException;


final class ConformanceFailure extends RuntimeException
{
    /**
     * @param list<ConformanceViolation> $violations
     */
    public function __construct(
        public readonly array $violations,
    ) {
        $lines = array_map(
            static fn (ConformanceViolation $violation): string => (string) $violation,
            $violations,
        );

        parent::__construct("tool conformance failed:\n - ". implode("\n - ", $lines));
    }
}
