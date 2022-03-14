<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Theme;

use Qce\WordPressBundle\Tests\WordPressTestCase;
use Qce\WordPressBundle\WordPress\Theme\ThemeSupports;

class ThemeSupportTest extends WordPressTestCase
{
    public function testThemeSupportsAreRegistered(): void
    {
        $themeSupport = new ThemeSupports([
            ['feature' => 'feature1', 'args' => []],
            ['feature' => 'feature2', 'args' => 'args'],
            ['feature' => 'feature3', 'args' => ['args']],
        ]);

        $this->createMockFunction('add_theme_support')->with('feature1', []);
        $this->createMockFunction('add_theme_support')->with('feature2', 'args');
        $this->createMockFunction('add_theme_support')->with('feature3', ['args']);

        $this->expectNotToPerformAssertions();
        $themeSupport->registerThemeSupports();
    }
}
