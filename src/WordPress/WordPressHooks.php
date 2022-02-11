<?php

namespace Qce\WordPressBundle\WordPress;

class WordPressHooks
{
    public function addHook(string $name, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        if (function_exists('add_filter')) {
            add_filter($name, $callback, $priority, $acceptedArgs);
        }
    }
}
