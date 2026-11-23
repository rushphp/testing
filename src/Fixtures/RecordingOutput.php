<?php declare(strict_types=1);

namespace Rush\Testing\Fixtures;

use Rush\Contract\OutputInterface;


final class RecordingOutput implements OutputInterface
{
    /** @var list<array{level: string, text: string}> */
    private array $entries = [];


    public function write(string $text): void
    {
        $this->entries[] = ['level' => 'write', 'text' => $text];
    }


    public function line(string $text = ''): void
    {
        $this->entries[] = ['level' => 'line', 'text' => $text];
    }


    public function info(string $text): void
    {
        $this->entries[] = ['level' => 'info', 'text' => $text];
    }


    public function success(string $text): void
    {
        $this->entries[] = ['level' => 'success', 'text' => $text];
    }


    public function warning(string $text): void
    {
        $this->entries[] = ['level' => 'warning', 'text' => $text];
    }


    public function error(string $text): void
    {
        $this->entries[] = ['level' => 'error', 'text' => $text];
    }


    public function separator(): void
    {
        $this->line(str_repeat('-', 60));
    }


    /** @return list<array{level: string, text: string}> */
    public function entries(): array
    {
        return $this->entries;
    }


    /** @return list<string> */
    public function lines(): array
    {
        return array_map(
            static fn (array $entry): string => $entry['text'],
            $this->entries,
        );
    }


    /** @return list<string> */
    public function byLevel(string $level): array
    {
        $lines = [];

        foreach ($this->entries as $entry) {
            if ($entry['level'] === $level) {
                $lines[] = $entry['text'];
            }
        }

        return $lines;
    }


    public function stdout(): string
    {
        $output = '';

        foreach ($this->entries as $entry) {
            if ($entry['level'] === 'error') {
                continue;
            }

            $output .= $entry['level'] === 'write'
                ? $entry['text']
                : $entry['text'] ."\n";
        }

        return $output;
    }


    public function stderr(): string
    {
        $output = '';

        foreach ($this->entries as $entry) {
            if ($entry['level'] === 'error') {
                $output .= $entry['text'] ."\n";
            }
        }

        return $output;
    }
}
