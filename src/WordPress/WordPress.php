<?php

namespace Qce\WordPressBundle\WordPress;

use Symfony\Component\HttpFoundation\Response;

class WordPress
{
    public function __construct(private string $wordpressDir)
    {
    }

    public function frontResponse(): Response
    {
        return new Response($this->loadFront(), headers: headers_list());
    }

    private function loadFront(): string
    {
        ob_start();
        include $this->wordpressDir . '/index.php';
        return ob_get_clean() ?: '';
    }
}
