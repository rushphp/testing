<?php declare(strict_types=1);

namespace Rush\Testing\Fixtures;

use Rush\Support\Operator;
use Rush\Support\OperatorFile;


final class OperatorFixture
{
    public static function write(
        string $projectRoot,
        string $name = 'Test',
        string $email = 'test@example.com',
    ): Operator {
        $operator = new Operator($name, $email);

        OperatorFile::save($projectRoot, $operator);

        return $operator;
    }
}
