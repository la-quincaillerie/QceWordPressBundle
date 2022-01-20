<?php

namespace Qce\WordPressBundle\WordPress\Theme\Attribute;

use Symfony\Component\Filesystem\Path;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ThemeFile
{
    /**
     * @param iterable<string, string> $headers
     */
    public function __construct(
        public string   $target,
        public iterable $headers = [],
    )
    {
        if (Path::isAbsolute($target) || str_starts_with($this->target = Path::canonicalize($target), '../')) {
            throw new \InvalidArgumentException("File cannot be configured outside of the theme directory.");
        }
    }
}

