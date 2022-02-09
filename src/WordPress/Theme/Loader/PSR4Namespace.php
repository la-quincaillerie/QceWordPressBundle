<?php

namespace Qce\WordPressBundle\WordPress\Theme\Loader;

class PSR4Namespace
{
    public function __construct(
        public string $directory,
        public string $namespace,
    )
    {
    }
}
