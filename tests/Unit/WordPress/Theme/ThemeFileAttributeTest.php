<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Theme;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Theme\Attribute\ThemeRoute;

class ThemeFileAttributeTest extends TestCase
{
    /**
     * @dataProvider provideFilePaths
     */
    public function testNoFileOutsideTheme(string $path): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ThemeRoute($path);
    }

    /** @return string[][] */
    public function provideFilePaths(): array
    {
        return [
            ['/absolute/path'],
            ['../other/directory'],
            ['disguise/../../other/directory'],
        ];
    }

    public function testCanonicalizePaths(): void
    {
        $themeFile = new ThemeRoute("woocommerce/dir/../test.php");
        self::assertSame("woocommerce/test.php", $themeFile->path);
    }
}
