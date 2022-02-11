<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Theme;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Theme\Theme;
use Qce\WordPressBundle\WordPress\Theme\ThemeController;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class ThemeTest extends TestCase
{
    public function testRender(): void
    {
        $fragmentHandler = $this->createMock(FragmentHandler::class);

        $controller = __METHOD__;
        $theme = new Theme('theme', handler: $fragmentHandler);
        $fragmentHandler
            ->expects($this->once())
            ->method('render')
            ->with($this->callback(function (ControllerReference $ref) use ($controller): bool {
                return $ref->controller === $controller && $ref->attributes === ['var' => 'value'];
            }))
            ->willReturn('Rendered');
        self::assertSame('Rendered', $theme->render(__METHOD__, ['var' => 'value']));
    }
}
