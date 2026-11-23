<?php declare(strict_types=1);

namespace Rush\Testing\Fixtures;

use Rush\Contract\CommandInterface;
use Rush\Contract\ProvidesConfigInterface;


final class ConfigurableFakeTool extends FakeTool implements ProvidesConfigInterface
{
    /**
     * @param array<string, mixed> $config
     * @param array<string, CommandInterface> $commands
     * @param list<string> $installLines
     */
    public function __construct(
        string $toolKey,
        private readonly array $config = [],
        array $commands = [],
        array $installLines = [],
        string $toolVersion = '1.0.0',
    ) {
        parent::__construct($toolKey, $commands, $installLines, $toolVersion);
    }


    /** @return array<string, mixed> */
    public function config(): array
    {
        return $this->config;
    }
}
