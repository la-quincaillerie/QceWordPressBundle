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
        $content = $this->loadFront();
        $status = $this->is404() ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return new Response($content, $status, \headers_list());
    }

    private function loadFront(): string
    {
        \ob_start();
        include $this->wordpressDir . '/index.php';
        return \ob_get_clean() ?: '';
    }

    private function is404(): bool
    {
        return function_exists('is_404') && \is_404();
    }
}
