<?php declare(strict_types=1);

use Rush\Contract\Option;
use Rush\Testing\Conformance\ConformanceFailure;
use Rush\Testing\Conformance\ToolConformance;
use Rush\Testing\Fixtures\FakeTool;
use Rush\Testing\Fixtures\RecordingCommand;


test('accepts a conforming tool', function (): void {
    $tool = new FakeTool('demo', ['demo hello' => new RecordingCommand('demo hello')]);

    expect(ToolConformance::violations($tool, sys_get_temp_dir()))->toBe([]);
});


test('reports violations', function (FakeTool $tool, string $rule): void {
    $violations = ToolConformance::violations($tool, sys_get_temp_dir());

    $rules = array_map(static fn ($violation): string => $violation->rule, $violations);

    expect($rules)->toContain($rule);
})->with([
    'invalid key' => [new FakeTool('BadKey'), 'key'],
    'empty metadata' => [new FakeTool('demo', [], [], ''), 'metadata'],
    'unscoped command' => [
        new FakeTool('demo', ['other' => new RecordingCommand('other')]),
        'commands',
    ],
    'reserved name' => [
        new FakeTool('demo', ['list' => new RecordingCommand('list')]),
        'commands',
    ],
    'name mismatch' => [
        new FakeTool('demo', ['demo x' => new RecordingCommand('other')]),
        'commands',
    ],
    'duplicate option' => [
        new FakeTool('demo', [
            'demo x' => new RecordingCommand('demo x', [
                new Option('a', 'A', true),
                new Option('a', 'A', true),
            ]),
        ]),
        'options',
    ],
    'repeatable without value' => [
        new FakeTool('demo', [
            'demo x' => new RecordingCommand('demo x', [
                new Option('a', 'A', false, null, false, 'value', 'a', true),
            ]),
        ]),
        'options',
    ],
]);


test('assert throws an aggregate failure', function (): void {
    $tool = new FakeTool('BadKey');

    expect(fn () => ToolConformance::assert($tool, sys_get_temp_dir()))
        ->toThrow(ConformanceFailure::class);
});
