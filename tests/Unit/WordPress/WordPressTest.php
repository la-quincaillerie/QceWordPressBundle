<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\WordPress;

class WordPressTest extends TestCase
{
    public function testWordPressResponse(): void
    {
        $wp = new WordPress(__DIR__ . '/../test-wordpress');
        $response = $wp->frontResponse();

        self::assertSame('test content', $response->getContent());
    }
}
