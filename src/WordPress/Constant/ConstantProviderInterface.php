<?php

namespace Qce\WordPressBundle\WordPress\Constant;

interface ConstantProviderInterface
{
    /**
     * @return iterable<string, string>
     */
    public function getConstants(): iterable;
}
