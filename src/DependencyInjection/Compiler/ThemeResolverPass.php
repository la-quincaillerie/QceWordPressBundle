<?php

namespace Qce\WordPressBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ThemeResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->hasDefinition('qce_wordpress.theme.resolver')) {
            return;
        }

        $definition = $container->getDefinition('qce_wordpress.theme.resolver');

        foreach ($container->findTaggedServiceIds('qce_wordpress.theme.loader') as $id => $tags) {
            $definition->addMethodCall('addLoader', [new Reference($id)]);
        }
    }
}
