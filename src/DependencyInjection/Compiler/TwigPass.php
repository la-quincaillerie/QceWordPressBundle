<?php

namespace Qce\WordPressBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig') || !$container->hasDefinition('qce_wordpress.twig.wp_variable')) {
            return;
        }

        $container->getDefinition('twig')->addMethodCall('addGlobal', ['wp', new Reference('qce_wordpress.twig.wp_variable')]);
    }
}
