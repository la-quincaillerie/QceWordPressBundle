<?php

namespace Qce\WordPressBundle\Tests\Functional;

use Qce\WordPressBundle\WordPress\WordPressConfig;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AutoConfiguredHookTest extends KernelTestCase
{
    public function testLoadWPHookAttributes(): void
    {
        /** @var WordPressConfig $config */
        $config = self::getContainer()->get('qce_wordpress.wordpress.config');
        $config->registerHooks();
        self::assertSame('custom_hook_result', apply_filters('custom_hook', ''));
    }
}
