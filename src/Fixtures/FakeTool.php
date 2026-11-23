<?php declare(strict_types=1);

namespace Rush\Testing\Fixtures;

use Rush\Contract\CommandInterface;
use Rush\Contract\ToolInterface;


class FakeTool implements ToolInterface
{
    /**
     * @param array<string, CommandInterface> $commands
     * @param list<string> $installLines
     */
    public function __construct(
        private readonly string $toolKey,
        private readonly array $commands = [],
        private readonly array $installLines = [],
        private readonly string $toolVersion = '1.0.0',
    ) {
    }


    public function key(): string
    {
        return $this->toolKey;
    }


    public function name(): string
    {
        return $this->toolKey;
    }


    public function description(): string
    {
        return 'Fake tool';
    }


    public function version(): string
    {
        return $this->toolVersion;
    }


    /** @return array<string, CommandInterface> */
    public function commands(string $projectRoot): array
    {
        return $this->commands;
    }


    /** @return list<string> */
    public function install(string $projectRoot): array
    {
        return $this->installLines;
    }
}
