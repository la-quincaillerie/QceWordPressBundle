<?php

namespace Qce\WordPressBundle\Tests\Functional;

use Qce\WordPressBundle\Bridge\Twig\WordPressExtension;
use Qce\WordPressBundle\Bridge\Twig\WordPressVariable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

class TwigBridgeTest extends KernelTestCase
{
    public function testRegisteredWordPressExtension(): void
    {
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');
        self::assertTrue($twig->hasExtension(WordPressExtension::class));
    }

    public function testRegisteredWordPressGlobalVariable(): void
    {
        /** @var Environment $twig */
        $twig = self::getContainer()->get('twig');
        $globals = $twig->getGlobals();
        self::assertArrayHasKey('wp', $globals);
        self::assertInstanceOf(WordPressVariable::class, $globals['wp']);
    }
}
