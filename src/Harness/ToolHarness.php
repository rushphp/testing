<?php declare(strict_types=1);

namespace Rush\Testing\Harness;

use RuntimeException;
use Rush\Contract\CommandInterface;
use Rush\Contract\ToolInterface;
use Rush\Manager\Application;
use Rush\Manager\Input;
use Rush\Testing\Fixtures\ProjectFixture;
use Rush\Testing\Fixtures\RecordingOutput;


final class ToolHarness
{
    /**
     * Run a tool through the RUSH runtime.
     *
     * @param list<string> $argv
     */
    public static function run(
        ToolInterface $tool,
        array $argv,
        ?ProjectFixture $project = null,
        bool $withOperator = true,
        string $version = '0.0.0',
    ): ToolRunResult {
        $owned = $project === null;
        $project ??= ProjectFixture::create();

        if ($withOperator) {
            $project->operator();
        }

        $stdout = fopen('php://memory', 'w+');
        $stderr = fopen('php://memory', 'w+');

        if ($stdout === false || $stderr === false) {
            throw new RuntimeException('could not open harness streams');
        }

        try {
            $application = new Application($project->path(), $version, $stdout, $stderr);
            $application->attach($tool);

            $code = $application->run($argv);

            rewind($stdout);
            rewind($stderr);

            return new ToolRunResult(
                $code,
                (string) stream_get_contents($stdout),
                (string) stream_get_contents($stderr),
            );
        } finally {
            fclose($stdout);
            fclose($stderr);

            if ($owned) {
                $project->cleanup();
            }
        }
    }


    /**
     * Run a single command in isolation.
     *
     * @param list<string> $argv
     */
    public static function runCommand(
        CommandInterface $command,
        array $argv,
        ?RecordingOutput $output = null,
    ): ToolRunResult {
        $output ??= new RecordingOutput();
        $input = Input::parse($argv, $command->options());

        $code = $command->run($input, $output);

        return new ToolRunResult($code, $output->stdout(), $output->stderr());
    }
}
