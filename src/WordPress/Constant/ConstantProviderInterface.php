<?php

namespace Qce\WordPressBundle\WordPress\Constant;

interface ConstantProviderInterface
{
    /**
     * @return iterable<string, mixed>
     */
    public function getConstants(): iterable;
}
