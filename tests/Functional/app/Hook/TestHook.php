<?php

namespace TestApp\Hook;

use Qce\WordPressBundle\Attribute\WPHook;

#[WPHook('custom_hook')]
class TestHook
{
    public function __invoke(): string
    {
        return 'custom_hook_result';
    }
}
