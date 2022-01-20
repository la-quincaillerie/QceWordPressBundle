<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Theme;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Theme\Theme;
use Qce\WordPressBundle\WordPress\Theme\ThemeController;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ThemeTest extends TestCase
{
    public function testStaticResourcesNoFolder(): void
    {
        $theme = new Theme('theme');
        self::assertCount(1, $theme->getResources());
    }

    public function testStaticResources(): void
    {
        $theme = new Theme('theme', staticDir: __DIR__);
        self::assertCount(2, $theme->getResources());
    }

    public function testControllerResources(): void
    {
        $theme = new Theme('theme', controllers: ['index.php' => new ThemeController(__METHOD__)]);
        $resources = $theme->getResources();
        self::assertCount(2, $resources);
        self::assertSame(__FILE__, (string)$resources[1]);
    }

    public function testRender(): void
    {
        $fragmentHandler = $this->createMock(FragmentHandler::class);

        $controller = new ThemeController(__METHOD__);
        $theme = new Theme('theme', controllers: ['index.php' => $controller], handler: $fragmentHandler);
        $fragmentHandler
            ->expects($this->once())
            ->method('render')
            ->with($this->callback(function (ControllerReference $ref) use ($controller): bool {
                return $ref->controller === $controller->controller && $ref->attributes === ['var' => 'value'];
            }))
            ->willReturn('Rendered');
        self::assertSame('Rendered', $theme->render('index.php', ['var' => 'value']));
    }

    public function testRenderNoController(): void
    {
        $theme = new Theme('theme');
        self::assertNull($theme->render('missing.php', []));
    }
}
