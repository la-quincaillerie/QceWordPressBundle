<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Constant;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;

class ConstantProviderTest extends TestCase
{
    public function testProvideConstant(): void
    {
        $constant = ['CONSTANT_NAME' => 'value'];
        $constantProvider = new ConstantProvider($constant);
        self::assertSame($constant, $constantProvider->getConstants());
    }
}
