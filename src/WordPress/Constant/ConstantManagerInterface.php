<?php

namespace Qce\WordPressBundle\WordPress\Constant;

interface ConstantManagerInterface
{
    public function define(string $name, mixed $value): bool;

    public function defined(string $name): bool;

    public function constant(string $name): mixed;
}
