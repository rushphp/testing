<?php declare(strict_types=1);

use Rush\Contract\ExitCode;
use Rush\Contract\Option;
use Rush\Testing\Fixtures\ConfigurableFakeTool;
use Rush\Testing\Fixtures\FakeTool;
use Rush\Testing\Fixtures\ProjectFixture;
use Rush\Testing\Fixtures\RecordingCommand;
use Rush\Testing\Fixtures\RecordingOutput;
use Rush\Testing\Harness\ToolHarness;


test('fake tool exposes metadata, commands and install lines', function (): void {
    $tool = new FakeTool('demo', [], ['created .rush/demo'], '2.0.0');

    expect($tool->key())->toBe('demo')
        ->and($tool->name())->toBe('demo')
        ->and($tool->description())->toBe('Fake tool')
        ->and($tool->version())->toBe('2.0.0')
        ->and($tool->commands('/tmp'))->toBe([])
        ->and($tool->install('/tmp'))->toBe(['created .rush/demo']);
});


test('configurable fake tool exposes configuration', function (): void {
    $tool = new ConfigurableFakeTool('demo', ['greeting' => 'hello']);

    expect($tool->config())->toBe(['greeting' => 'hello']);
});


test('recording output separates stdout and stderr entries', function (): void {
    $output = new RecordingOutput();
    $output->write('a');
    $output->line('b');
    $output->success('c');
    $output->error('boom');

    expect($output->stdout())->toBe("ab\nc\n")
        ->and($output->stderr())->toBe("boom\n")
        ->and($output->byLevel('success'))->toBe(['c'])
        ->and($output->lines())->toBe(['a', 'b', 'c', 'boom']);
});


test('recording command records input, calls and exit code', function (): void {
    $command = new RecordingCommand('demo', [new Option('name', 'Name', true)], ExitCode::CheckFailed->value);

    $result = ToolHarness::runCommand($command, ['--name=Ana']);

    expect($result->code)->toBe(ExitCode::CheckFailed->value)
        ->and($command->calls())->toBe(1)
        ->and($command->lastInput()?->string('name'))->toBe('Ana');
});


test('project fixture writes files and cleans up', function (): void {
    $project = ProjectFixture::create([
        'composer.json' => '{}',
        'nested/file.txt' => 'x',
    ]);

    expect(is_file($project->path('composer.json')))->toBeTrue()
        ->and(file_get_contents($project->path('nested/file.txt')))->toBe('x');

    $root = $project->path();
    $project->cleanup();

    expect(file_exists($root))->toBeFalse();
});
