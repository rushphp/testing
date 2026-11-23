<?php declare(strict_types=1);

namespace Rush\Testing\Fixtures;

use Closure;
use Rush\Contract\CommandInterface;
use Rush\Contract\InputInterface;
use Rush\Contract\Option;
use Rush\Contract\OutputInterface;


final class RecordingCommand implements CommandInterface
{
    private ?InputInterface $lastInput = null;

    private int $calls = 0;


    /**
     * @param list<Option> $options
     * @param null|Closure(InputInterface, OutputInterface): void $handler
     */
    public function __construct(
        private readonly string $commandName,
        private readonly array $options = [],
        private readonly int $exitCode = 0,
        private readonly ?Closure $handler = null,
    ) {
    }


    public function name(): string
    {
        return $this->commandName;
    }


    public function description(): string
    {
        return 'Recording command';
    }


    public function usage(): string
    {
        return 'rush '. $this->commandName;
    }


    /** @return list<Option> */
    public function options(): array
    {
        return $this->options;
    }


    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->lastInput = $input;
        $this->calls++;

        if ($this->handler !== null) {
            ($this->handler)($input, $output);
        }

        return $this->exitCode;
    }


    public function lastInput(): ?InputInterface
    {
        return $this->lastInput;
    }


    public function calls(): int
    {
        return $this->calls;
    }
}
