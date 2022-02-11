<?php

namespace Qce\WordPressBundle\WordPress\Theme;

class ThemeRoute
{
    /**
     * @param iterable<string, string> $headers
     */
    public function __construct(
        private string   $controller,
        private iterable $headers = [],
    )
    {
    }

    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return iterable<string, string>
     */
    public function getHeaders(): iterable
    {
        return $this->headers;
    }
}
