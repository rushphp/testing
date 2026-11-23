[English](README.md) | [Português](README.pt.md)

![PHP](https://img.shields.io/badge/PHP-%20^8.3-777BB4)
![PHPStan-Level](https://img.shields.io/badge/PHPStan-Level%209-224488)
![License](https://img.shields.io/badge/License-MIT-777)

# RUSH Testing

**RUSH** — Registry-driven Unified Shell Helper.

Testing kit for tools built for RUSH. It provides fixtures, a runtime harness
and a contract conformance suite so every tool is tested the same way.

> **Pre-1.0:** minor releases may include breaking changes.
> Development-only: never add this package to runtime dependencies.

## ✨ Features

- **Fixtures**: `FakeTool`, `ConfigurableFakeTool`, `RecordingCommand`,
  `RecordingOutput`, `ProjectFixture`, `OperatorFixture`.
- **Harness**: run a tool through the real runtime or a command in isolation.
- **Conformance**: assert that a tool respects `ToolInterface`/`CommandInterface`
  and the registry rules (command scoping, reserved names, option coherence).

All types are framework-agnostic (plain PHP). Pest/PHPUnit integration is left
to the caller.

## 📦 Installation

```bash
composer require --dev rushphp/testing
```

## 🚀 Quick start

```php
use Rush\Testing\Fixtures\ProjectFixture;
use Rush\Testing\Harness\ToolHarness;
use Rush\Testing\Conformance\ToolConformance;

$project = ProjectFixture::create(['composer.json' => '{}'])->operator();
$result = ToolHarness::run(new MyTool(), ['my', 'status'], $project);

expect($result->code)->toBe(0);
expect($result->stdout)->toContain('ok');

ToolConformance::assert(new MyTool(), $project->path());
```

## 🧪 Testing a command in isolation

```php
use Rush\Testing\Harness\ToolHarness;

$result = ToolHarness::runCommand($command, ['--target=staging']);

expect($result->code)->toBe(0);
```

## ✅ Good practices

- Run `ToolConformance::assert()` in its own test for every tool.
- Use `ToolHarness::run()` to test wiring (registry -> Application -> command).
- Use `ToolHarness::runCommand()` to test command logic without the runtime.
- Prefer `RecordingOutput` and structured assertions over exact help layout.
- Test both operator paths (`withOperator: true/false`) when policy varies.
- Keep domain-specific fakes (SSH, database, HTTP) inside the tool; build them
  on top of `ProjectFixture`.
- Always clean up temp projects (`ProjectFixture` does it automatically).

## 🤝 Contributions

Contributions are welcome. Open an issue or submit a pull request.

## 📄 License

MIT. See [LICENSE](LICENSE).
