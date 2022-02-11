<?php

namespace Qce\WordPressBundle\WordPress\Constant\Provider;

use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;

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
