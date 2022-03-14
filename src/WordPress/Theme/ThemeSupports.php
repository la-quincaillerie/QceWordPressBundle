<?php

namespace Qce\WordPressBundle\WordPress\Theme;

class ThemeSupports
{
    /**
     * @param array{feature: string, args: mixed}[] $themeSupport
     */
    public function __construct(private array $themeSupport)
    {
    }

    public function registerThemeSupports(): void
    {
        foreach ($this->themeSupport as ['feature' => $feature, 'args' => $args]) {
            \add_theme_support($feature, $args);
        }
    }
}
