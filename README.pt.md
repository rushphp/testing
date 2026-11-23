[English](README.md) | [Português](README.pt.md)

![PHP](https://img.shields.io/badge/PHP-%20^8.3-777BB4)
![PHPStan-Level](https://img.shields.io/badge/PHPStan-Level%209-224488)
![License](https://img.shields.io/badge/License-MIT-777)

# RUSH Testing

**RUSH** — Registry-driven Unified Shell Helper.

Kit de testes para tools construídas para o RUSH. Oferece fixtures, um harness
do runtime e uma suíte de conformidade de contrato para que toda tool seja
testada do mesmo jeito.

> **Pré-1.0:** releases minor podem incluir mudanças incompatíveis.
> Somente desenvolvimento: nunca adicione este pacote em dependências de runtime.

## ✨ Recursos

- **Fixtures**: `FakeTool`, `ConfigurableFakeTool`, `RecordingCommand`,
  `RecordingOutput`, `ProjectFixture`, `OperatorFixture`.
- **Harness**: executa uma tool pelo runtime real ou um comando isolado.
- **Conformidade**: garante que a tool respeita `ToolInterface`/`CommandInterface`
  e as regras do registry (escopo de comando, nomes reservados, coerência de opções).

Todos os tipos são independentes de framework (PHP puro). A integração com
Pest/PHPUnit fica a cargo do consumidor.

## 📦 Instalação

```bash
composer require --dev rushphp/testing
```

## 🚀 Início rápido

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

## 🧪 Testando um comando isolado

```php
use Rush\Testing\Harness\ToolHarness;

$result = ToolHarness::runCommand($command, ['--target=staging']);

expect($result->code)->toBe(0);
```

## ✅ Boas práticas

- Rode `ToolConformance::assert()` em um teste dedicado para cada tool.
- Use `ToolHarness::run()` para testar a ligação (registry -> Application -> comando).
- Use `ToolHarness::runCommand()` para testar a lógica sem o runtime.
- Prefira `RecordingOutput` e asserções estruturadas a comparar o help inteiro.
- Teste os dois caminhos de operador (`withOperator: true/false`) quando a política variar.
- Mantenha fakes de domínio (SSH, banco, HTTP) dentro da tool, sobre `ProjectFixture`.
- Sempre limpe projetos temporários (`ProjectFixture` faz isso automaticamente).

## 🤝 Contribuições

Contribuições são bem-vindas. Abra uma issue ou envie um pull request.

## 📄 Licença

MIT. Consulte [LICENSE](LICENSE).
