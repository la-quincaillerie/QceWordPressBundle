<?php

namespace Qce\WordPressBundle\WordPress\Theme;

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class Theme
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string           $slug,
        private array            $headers = [],
        private ?FragmentHandler $handler = null,
    )
    {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /** @return array<string, string> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array<string, mixed> $wordpressContext
     */
    public function render(string $controller, array $wordpressContext): ?string
    {
        return $this->handler?->render(new ControllerReference($controller, $wordpressContext));
    }
}
