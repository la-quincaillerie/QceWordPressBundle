<?php

namespace Qce\WordPressBundle\Tests\WordPress;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\Tests\WordPress\Constant\TestConstantManager;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Qce\WordPressBundle\WordPress\WordPressConfig;

class WordPressConfigTest extends TestCase
{
    private TestConstantManager $constantManager;

    public function testDefinesConstants(): void
    {
        $wpConfig = new WordPressConfig(
            'dir',
            'prefix',
            [ new ConstantProvider(['NAME' => 'value']) ],
            $this->constantManager,
        );
        $wpConfig->defineConstants();

        self::assertTrue($this->constantManager->defined('NAME'));
        self::assertSame('value', $this->constantManager->constant('NAME'));
    }

    public function testDefinesConstantsOverride(): void
    {
        $wpConfig = new WordPressConfig(
            'dir',
            'prefix',
            [
                new ConstantProvider(['NAME' => 'value1']),
                new ConstantProvider(['NAME' => 'value2']),
            ],
            $this->constantManager,
        );
        $wpConfig->defineConstants();

        self::assertSame('value2', $this->constantManager->constant('NAME'));
    }

    public function testIncludeSettings(): void
    {
        global $table_prefix;
        $wpConfig = new WordPressConfig(
            __DIR__.'/test-wordpress',
            'table_prefix_',
            [],
            $this->constantManager,
        );
        $wpConfig->includeSettings();

        self::assertSame('table_prefix_', $table_prefix);
    }

    public function setUp(): void
    {
        $this->constantManager = new TestConstantManager();
    }
}
