<?php

namespace Qce\WordPressBundle\WordPress\Theme\Loader;

use Qce\WordPressBundle\WordPress\Theme\Attribute\ThemeRoute as ThemeRouteAttribute;
use Qce\WordPressBundle\WordPress\Theme\ThemeRoute;
use Qce\WordPressBundle\WordPress\Theme\ThemeRouteCollection;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\ReflectionClassResource;

class AnnotationClassLoader extends Loader
{
    /**
     * @param \ReflectionClass<object> $resource
     */
    public function load(mixed $resource, string $type = null): ThemeRouteCollection
    {
        $collection = new ThemeRouteCollection();
        $collection->addResource(new ReflectionClassResource($resource));

        $attributes = $resource->getAttributes(ThemeRouteAttribute::class, \ReflectionAttribute::IS_INSTANCEOF);

        // Add class attributes
        foreach ($attributes as $attribute) {
            /** @var ThemeRouteAttribute $route */
            $route = $attribute->newInstance();
            $collection->add($route->path, new ThemeRoute($resource->name, $route->headers));
        }

        // Add method attributes
        foreach ($resource->getMethods() as $method) {
            foreach ($method->getAttributes(ThemeRouteAttribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                /** @var ThemeRouteAttribute $route */
                $route = $attribute->newInstance();
                $collection->add($route->path, new ThemeRoute($resource->name . '::' . $method->name, $route->headers));
            }
        }

        return $collection;
    }

    public function supports(mixed $resource, string $type = null): bool
    {
        return
            $resource instanceof \ReflectionClass
            && !$resource->isAbstract()
            && (!$type || 'annotation' === $type);
    }
}
