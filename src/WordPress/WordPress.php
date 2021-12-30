<?php

namespace Qce\WordPressBundle\WordPress;

use Symfony\Component\HttpFoundation\Response;

class WordPress
{
    /**
     * @param string[] $globals
     */
    public function __construct(
        private string $wordpressDir,
        private array  $globals,
    )
    {
    }

    public function frontResponse(): Response
    {
        return new Response($this->loadFront(), headers: headers_list());
    }

    private function loadFront(): string
    {
        // WordPress is loaded in a function scope, so we need to declare the
        // global variables that are going to be used.
        // Extra variables can be configured through the qce_wordpress.globals key
        foreach ($this->globals as $global){
            global $$global;
        }

        ob_start();
        include $this->wordpressDir . '/index.php';
        return ob_get_clean() ?: '';
    }
}
