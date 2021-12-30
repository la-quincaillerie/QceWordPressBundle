<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Constant;

use Qce\WordPressBundle\WordPress\Constant\ConstantManagerInterface;

class TestConstantManager implements ConstantManagerInterface
{
    /** @var array<string, mixed> $constants */
    private array $constants = [];

    public function define(string $name, mixed $value): bool
    {
        if ($this->defined($name)){
            return false;
        }
        $this->constants[$name] = $value;
        return true;
    }

    public function defined(string $name): bool
    {
        return isset($this->constants[$name]);
    }

    public function constant(string $name): mixed
    {
        return $this->constants[$name] ?? null;
    }
}
