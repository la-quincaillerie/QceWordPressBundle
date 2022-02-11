<?php

namespace Qce\WordPressBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Router;
use TestApp\Controller\TestController;

class RouteTest extends KernelTestCase
{
    public function testHasWordPressRoute(): void
    {
        /** @var Router $router */
        $router = self::getContainer()->get('router');

        self::assertSame([
            '_route' => 'custom_route',
            '_controller' => TestController::class,
        ], $router->match('/custom'));

        self::assertSame([
            '_route' => '_qce_wordpress_catch_all',
            '_controller' => 'qce_wordpress.wordpress.controller',
            'path' => 'another/route/with/slashes',
        ], $router->match('/another/route/with/slashes'));
    }
}
