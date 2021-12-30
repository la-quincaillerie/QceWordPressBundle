<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\WordPress;

class WordPressTest extends TestCase
{
    public function testWordPressResponse(): void
    {
        global $wp_query;

        $wp = new WordPress(
            __DIR__.'/../test-wordpress',
            ['wp', 'wp_query'],
        );
        $response = $wp->frontResponse();

        self::assertSame('query', $wp_query);
        self::assertSame('test content', $response->getContent());
    }
}
