<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Constant;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Constant\Provider\DirectoryConstantProvider;

class DirectoryConstantProviderTest extends TestCase
{
    public function testProvideCanonicalizedDirectoryConstants(): void
    {
        $constantProvider = new DirectoryConstantProvider(__DIR__ . '/subdir/..');
        self::assertSame(['WP_CONTENT_DIR' => __DIR__], $constantProvider->getConstants());
    }
}
