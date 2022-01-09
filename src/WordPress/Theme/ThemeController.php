<?php

namespace Qce\WordPressBundle\WordPress\Theme;

class ThemeController
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public string $controller,
        public array  $headers = [],
    )
    {
    }
}
