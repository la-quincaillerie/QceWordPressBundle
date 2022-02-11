<?php

namespace Qce\WordPressBundle\WordPress\Constant\Provider;

use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Symfony\Component\Filesystem\Path;

class DirectoryConstantProvider implements ConstantProviderInterface
{
    public function __construct(private string $contentDir)
    {
    }

    public function getConstants(): iterable
    {
        return [
            'WP_CONTENT_DIR' => Path::canonicalize($this->contentDir),
        ];
    }
}
