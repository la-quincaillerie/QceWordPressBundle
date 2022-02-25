<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\Tests\Unit\WordPress\Constant\TestConstantManager;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Qce\WordPressBundle\WordPress\WordPressConfig;
use Qce\WordPressBundle\WordPress\WordPressHooks;

class WordPressConfigTest extends TestCase
{
    private TestConstantManager $constantManager;

    public function testDefinesConstants(): void
    {
        $wpConfig = new WordPressConfig(
            [new ConstantProvider(['NAME' => 'value'])],
            $this->constantManager,
            new WordPressHooks(),
        );
        $wpConfig->defineConstants();

        self::assertTrue($this->constantManager->defined('NAME'));
        self::assertSame('value', $this->constantManager->constant('NAME'));
    }

    public function testDefinesConstantsOverride(): void
    {
        $wpConfig = new WordPressConfig(
            [
                new ConstantProvider(['NAME' => 'value1']),
                new ConstantProvider(['NAME' => 'value2']),
            ],
            $this->constantManager,
            new WordPressHooks(),
        );
        $wpConfig->defineConstants();

        self::assertSame('value2', $this->constantManager->constant('NAME'));
    }

    public function setUp(): void
    {
        $this->constantManager = new TestConstantManager();
    }
}
