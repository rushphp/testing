<?php declare(strict_types=1);

namespace Rush\Testing\Harness;


final class ToolRunResult
{
    public function __construct(
        public readonly int $code,
        public readonly string $stdout,
        public readonly string $stderr,
    ) {
    }


    /** @return list<string> */
    public function lines(): array
    {
        $text = rtrim($this->stdout, "\n");

        return $text === '' ? [] : explode("\n", $text);
    }


    public function output(): string
    {
        return $this->stdout;
    }
}
