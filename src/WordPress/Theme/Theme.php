<?php

namespace Qce\WordPressBundle\WordPress\Theme;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class Theme
{
    /**
     * @param array<string, string> $headers
     * @param array<string, ThemeController> $controllers
     */
    public function __construct(
        private string           $slug,
        private array            $headers = [],
        private array            $controllers = [],
        private string           $staticDir = '',
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

    /** @return ResourceInterface[] */
    public function getResources(): array
    {
        $resources = [new FileExistenceResource($this->staticDir)];
        if (file_exists($this->staticDir)) {
            $resources[] = new DirectoryResource($this->staticDir);
        }
        foreach ($this->controllers as $controller) {
            /** @var class-string $class */
            [$class] = explode('::', $controller->controller);

            /** @var string $filePath */
            $filePath = (new \ReflectionClass($class))->getFileName();
            $resource = new FileResource($filePath);
            $resources[$filePath] = $resource;
        }
        return array_values($resources);
    }

    public function getStaticDir(): string
    {
        return $this->staticDir;
    }

    /**
     * @return array<string, ThemeController>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @param array<string, mixed> $wordpressContext
     */
    public function render(string $file, array $wordpressContext): ?string
    {
        if (!isset($this->controllers[$file])) {
            return null;
        }

        return $this->handler?->render(new ControllerReference($this->controllers[$file]->controller, $wordpressContext));
    }
}
