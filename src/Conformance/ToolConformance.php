<?php declare(strict_types=1);

namespace Rush\Testing\Conformance;

use Rush\Contract\ProvidesConfigInterface;
use Rush\Contract\ToolInterface;
use Throwable;


final class ToolConformance
{
    /** @var list<string> */
    private const RESERVED = ['help', 'version', 'install', 'list', 'operator'];

    /** @var list<string> */
    private const METADATA = ['name', 'description', 'version'];


    public static function assert(
        ToolInterface $tool,
        string $projectRoot,
        ?ConformanceOptions $options = null,
    ): void {
        $violations = self::violations($tool, $projectRoot, $options);

        if ($violations !== []) {
            throw new ConformanceFailure($violations);
        }
    }


    /**
     * @return list<ConformanceViolation>
     */
    public static function violations(
        ToolInterface $tool,
        string $projectRoot,
        ?ConformanceOptions $options = null,
    ): array {
        $options ??= new ConformanceOptions();
        $violations = [];
        $key = $tool->key();

        if (preg_match('/\A[a-z][a-z0-9_-]*\z/', $key) !== 1) {
            $violations[] = new ConformanceViolation('key', "key '{$key}' must match [a-z][a-z0-9_-]*");
        }

        foreach (self::METADATA as $method) {
            if (trim((string) $tool->{$method}()) === '') {
                $violations[] = new ConformanceViolation('metadata', "{$method}() must be non-empty");
            }
        }

        try {
            $commands = $tool->commands($projectRoot);
        } catch (Throwable $error) {
            $violations[] = new ConformanceViolation('commands', 'commands() failed: '. $error->getMessage());

            return $violations;
        }

        foreach ($commands as $name => $command) {
            if ($name === '') {
                $violations[] = new ConformanceViolation('commands', 'command names must be non-empty');

                continue;
            }

            if ($options->requireCommandPrefix && $name !== $key && ! str_starts_with($name, $key .' ')) {
                $violations[] = new ConformanceViolation(
                    'commands',
                    "command '{$name}' must be scoped to tool '{$key}'",
                );
            }

            if ( ! $options->allowReservedNames && in_array($name, self::RESERVED, true)) {
                $violations[] = new ConformanceViolation('commands', "command '{$name}' uses a reserved name");
            }

            if ($command->name() !== $name) {
                $violations[] = new ConformanceViolation(
                    'commands',
                    "command '{$name}' must return its own name from name()",
                );
            }

            if (trim($command->usage()) === '') {
                $violations[] = new ConformanceViolation('usage', "command '{$name}' must define usage()");
            }

            $seen = [];

            foreach ($command->options() as $option) {
                if (isset($seen[$option->name])) {
                    $violations[] = new ConformanceViolation(
                        'options',
                        "command '{$name}' declares option '{$option->name}' more than once",
                    );
                }

                $seen[$option->name] = true;

                if (trim($option->valueName) === '') {
                    $violations[] = new ConformanceViolation(
                        'options',
                        "option '{$option->name}' must define a value name",
                    );
                }

                if ($option->repeatable && ! $option->takesValue) {
                    $violations[] = new ConformanceViolation(
                        'options',
                        "option '{$option->name}' cannot be repeatable without taking a value",
                    );
                }
            }
        }

        if ($options->checkInstall) {
            try {
                $tool->install($projectRoot);
            } catch (Throwable $error) {
                $violations[] = new ConformanceViolation('install', 'install() failed: '. $error->getMessage());
            }
        }

        if ($tool instanceof ProvidesConfigInterface) {
            try {
                $tool->config();
            } catch (Throwable $error) {
                $violations[] = new ConformanceViolation('config', 'config() failed: '. $error->getMessage());
            }
        }

        return $violations;
    }
}
