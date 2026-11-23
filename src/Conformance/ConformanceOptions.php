<?php declare(strict_types=1);

namespace Rush\Testing\Conformance;


final class ConformanceOptions
{
    public function __construct(
        public readonly bool $requireCommandPrefix = true,
        public readonly bool $allowReservedNames = false,
        public readonly bool $checkInstall = false,
    ) {
    }
}
