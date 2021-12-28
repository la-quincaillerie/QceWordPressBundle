<?php

namespace Qce\WordPressBundle\WordPress\Constant;

class ConstantProvider implements ConstantProviderInterface
{
    /**
     * @param array<string, mixed> $constants
     */
    public function __construct(private array $constants)
    {
    }

    public function getConstants(): iterable
    {
        return $this->constants;
    }
}
