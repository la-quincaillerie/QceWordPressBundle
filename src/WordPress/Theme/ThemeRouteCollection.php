<?php

namespace Qce\WordPressBundle\WordPress\Theme;

use Symfony\Component\Config\Resource\ResourceInterface;

class ThemeRouteCollection
{
    /** @var array<string, ResourceInterface> */
    private array $resources = [];

    /** @var array<string, ThemeRoute> $routes */
    private array $routes = [];

    public function add(string $path, ThemeRoute $route): void
    {
        $this->routes[$path] = $route;
    }

    /**
     * @return array<string, ThemeRoute>
     */
    public function all(): array
    {
        return $this->routes;
    }

    public function addRouteCollection(ThemeRouteCollection $collection): void
    {
        foreach ($collection->all() as $path => $route) {
            $this->add($path, $route);
        }
        foreach ($collection->getResources() as $resource) {
            $this->addResource($resource);
        }
    }

    public function addResource(ResourceInterface $resource): void
    {
        $this->resources[(string)$resource] = $resource;
    }

    /**
     * @return ResourceInterface[]
     */
    public function getResources(): array
    {
        return array_values($this->resources);
    }
}
