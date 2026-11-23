<?php declare(strict_types=1);

use Rush\Contract\ExitCode;
use Rush\Contract\InputInterface;
use Rush\Contract\OutputInterface;
use Rush\Testing\Fixtures\FakeTool;
use Rush\Testing\Fixtures\ProjectFixture;
use Rush\Testing\Fixtures\RecordingCommand;
use Rush\Testing\Harness\ToolHarness;


function fakeEchoTool(int $exitCode = 0): FakeTool
{
    $command = new RecordingCommand(
        'demo hello',
        [],
        $exitCode,
        static function (InputInterface $input, OutputInterface $output): void {
            $output->line('hello');
        },
    );

    return new FakeTool('demo', ['demo hello' => $command]);
}


test('runs a tool through the runtime when the operator is configured', function (): void {
    $result = ToolHarness::run(fakeEchoTool(), ['demo', 'hello']);

    expect($result->code)->toBe(ExitCode::Success->value)
        ->and($result->stdout)->toContain('hello')
        ->and($result->stderr)->toBe('');
});


test('fails when the operator is required and missing', function (): void {
    $result = ToolHarness::run(fakeEchoTool(), ['demo', 'hello'], withOperator: false);

    expect($result->code)->toBe(ExitCode::Failure->value)
        ->and($result->stderr)->toContain('operator not configured');
});


test('propagates the command exit code', function (): void {
    $result = ToolHarness::run(fakeEchoTool(ExitCode::CheckFailed->value), ['demo', 'hello']);

    expect($result->code)->toBe(ExitCode::CheckFailed->value);
});


test('does not clean a project fixture supplied by the caller', function (): void {
    $project = ProjectFixture::create();

    ToolHarness::run(fakeEchoTool(), ['demo', 'hello'], $project);

    expect(is_dir($project->path()))->toBeTrue();

    $project->cleanup();
});
