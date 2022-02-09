<?php

namespace Qce\WordPressBundle\WordPress\Theme\Loader;

use Qce\WordPressBundle\WordPress\Theme\ThemeRouteCollection;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\GlobResource;
use Symfony\Component\Config\Resource\ResourceInterface;

class NamespaceLoader extends FileLoader
{
    /**
     * @param PSR4Namespace $resource
     */
    public function load(mixed $resource, string $type = null): ThemeRouteCollection
    {
        $collection = new ThemeRouteCollection();

        $classes = $this->findClasses($resource->namespace, $resource->directory, $resources);

        foreach ($classes as $class) {
            $loader = $this->resolve($class);
            /** @var ThemeRouteCollection $classCollection */
            $classCollection = $loader->load($class);
            $collection->addRouteCollection($classCollection);
        }

        foreach ($resources as $r) {
            $collection->addResource($r);
        }

        return $collection;
    }

    /**
     * @param ?ResourceInterface[] $resources
     * @return \ReflectionClass<object>[]
     */
    private function findClasses(string $namespace, string $pattern, array &$resources = null): array
    {
        $classes = [];
        $extRegexp = '/\\.php$/';
        $prefixLen = null;

        /**
         * @var string $path
         * @var \SplFileInfo $info
         */
        foreach ($this->glob($pattern, true, $resource, false, false) as $path => $info) {
            if (null === $prefixLen) {
                $prefixLen = \strlen($resource->getPrefix());
            }

            if (!preg_match($extRegexp, $path, $m) || !$info->isReadable()) {
                continue;
            }
            /** @var class-string $class */
            $class = $namespace . ltrim(str_replace('/', '\\', substr($path, $prefixLen, -\strlen($m[0]))), '\\');

            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)*+$/', $class)) {
                continue;
            }

            if (($r = new \ReflectionClass($class))->isInstantiable()) {
                $classes[] = $r;
            }
        }

        if ($resource instanceof GlobResource) {
            $resources = [$resource];
        } else {
            $resources = $resource;
        }

        return $classes;
    }

    public function supports(mixed $resource, string $type = null): bool
    {
        return
            $resource instanceof PSR4Namespace
            && (!$type || 'annotation' === $type);
    }
}
