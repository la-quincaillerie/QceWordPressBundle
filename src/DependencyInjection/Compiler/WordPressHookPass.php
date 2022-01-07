<?php

namespace Qce\WordPressBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WordPressHookPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('qce_wordpress.wordpress.config')) {
            return;
        }

        $definition = $container->findDefinition('qce_wordpress.wordpress.config');
        $taggedServices = $container->findTaggedServiceIds('qce_wordpress.hook');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $args = [
                    $tag['name'],
                    isset($tag['method']) ? [new Reference($id), $tag['method']] : new Reference($id),
                    $tag['priority'] ?? 10,
                    $tag['accepted_args'] ?? 1,
                ];
                $definition->addMethodCall('addHook', $args);
            }
        }
    }
}
