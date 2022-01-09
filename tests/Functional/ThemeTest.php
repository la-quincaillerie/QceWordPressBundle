<?php

namespace Qce\WordPressBundle\Tests\Functional;

use Qce\WordPressBundle\WordPress\Theme\Builder\ThemeBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThemeTest extends KernelTestCase
{
    public function testCreateThemeFile(): void
    {
        $container = self::getContainer();

        /** @var ThemeBuilder $themeBuilder */
        $themeBuilder = $container->get('qce_wordpress.theme.builder');
        $themeBuilder->build();

        self::assertFileExists($themeBuilder->getThemeDir() . '/test-file.php');
    }
}
