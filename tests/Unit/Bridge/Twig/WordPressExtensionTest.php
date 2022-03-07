<?php

namespace Qce\WordPressBundle\Tests\Unit\Bridge\Twig;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\Bridge\Twig\WordPressExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class WordPressExtensionTest extends TestCase
{
    private WordPressExtension $extension;

    public function testFunctions(): void
    {
        self::assertContainsOnlyInstancesOf(TwigFunction::class, $this->extension->getFunctions());
    }

    public function testFilters(): void
    {
        self::assertContainsOnlyInstancesOf(TwigFilter::class, $this->extension->getFilters());
    }

    protected function setUp(): void
    {
        $this->extension = new WordPressExtension();
    }
}
