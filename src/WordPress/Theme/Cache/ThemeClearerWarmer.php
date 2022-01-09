<?php

namespace Qce\WordPressBundle\WordPress\Theme\Cache;

use Qce\WordPressBundle\WordPress\Theme\Builder\ThemeBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class ThemeClearerWarmer implements CacheWarmerInterface, CacheClearerInterface
{
    public function __construct(private ThemeBuilder $themeBuilder)
    {
    }

    public function warmUp(string $cacheDir): array
    {
        $this->themeBuilder->build();
        return [];
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function clear(string $cacheDir): void
    {
        $fs = new Filesystem();
        $fs->remove($this->themeBuilder->getThemeDir());
    }
}
