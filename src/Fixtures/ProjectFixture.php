<?php declare(strict_types=1);

namespace Rush\Testing\Fixtures;

use Rush\Support\FileSystem;
use Rush\Support\Operator;
use Rush\Support\OperatorFile;


final class ProjectFixture
{
    private bool $cleaned = false;


    private function __construct(
        private readonly string $root,
    ) {
    }


    /**
     * @param array<string, string> $files
     */
    public static function create(array $files = []): self
    {
        $root = sys_get_temp_dir() .'/rush-testing-'. bin2hex(random_bytes(6));

        FileSystem::ensureDirectory($root, 0700);

        $fixture = new self($root);

        foreach ($files as $path => $contents) {
            $fixture->write($path, $contents);
        }

        return $fixture;
    }


    public function path(?string $relative = null): string
    {
        if ($relative === null || $relative === '') {
            return $this->root;
        }

        return $this->root .'/'. ltrim($relative, '/');
    }


    public function write(string $relative, string $contents): self
    {
        FileSystem::write($this->path($relative), $contents, 0644);

        return $this;
    }


    public function operator(string $name = 'Test', string $email = 'test@example.com'): self
    {
        OperatorFile::save($this->root, new Operator($name, $email));

        return $this;
    }


    public function cleanup(): void
    {
        if ($this->cleaned) {
            return;
        }

        $this->cleaned = true;
        FileSystem::tryDelete($this->root);
    }


    public function __destruct()
    {
        $this->cleanup();
    }
}
