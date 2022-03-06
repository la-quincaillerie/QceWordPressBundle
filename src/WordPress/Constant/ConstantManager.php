<?php

namespace Qce\WordPressBundle\WordPress\Constant;

/**
 * @codeCoverageIgnore -- Untestable class that was only added not to use define directly in unit tests
 */
class ConstantManager implements ConstantManagerInterface
{
    public function define(string $name, mixed $value): bool
    {
        return define($name, $value);
    }

    public function defined(string $name): bool
    {
        return defined($name);
    }

    public function constant(string $name): mixed
    {
        return constant($name);
    }
}
