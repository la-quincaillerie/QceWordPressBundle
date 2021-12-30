<?php

namespace Qce\WordPressBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AutoConfiguredHookTest extends KernelTestCase
{
    public function testLoadWPHookAttributes(): void
    {
        self::getContainer()->get('qce_wordpress.wordpress.hooks');
        self::assertSame('custom_hook_result', apply_filters('custom_hook', ''));
    }
}
