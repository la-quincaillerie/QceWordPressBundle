<?php

namespace Qce\WordPressBundle\WordPress\Theme\Attribute;

use Symfony\Component\Filesystem\Path;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ThemeRoute
{
    /**
     * @param iterable<string, string> $headers
     */
    public function __construct(
        public string   $path,
        public iterable $headers = [],
    )
    {
        if (Path::isAbsolute($path) || str_starts_with($this->path = Path::canonicalize($path), '../')) {
            throw new \InvalidArgumentException("File cannot be configured outside of the theme directory.");
        }
    }
}

