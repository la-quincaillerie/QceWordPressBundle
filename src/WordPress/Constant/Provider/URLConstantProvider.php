<?php

namespace Qce\WordPressBundle\WordPress\Constant\Provider;

use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;

class URLConstantProvider implements ConstantProviderInterface
{
    public function __construct(
        private string $home,
        private string $site_url,
    )
    {
    }

    public function getConstants(): iterable
    {
        return [
            'WP_HOME' => $this->home,
            'WP_SITEURL' => $this->site_url,
        ];
    }
}
