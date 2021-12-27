<?php

namespace Qce\WordPressBundle\DependencyInjection;

use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class QceWordPressExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('wordpress.php');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->getDefinition('qce_wordpress.constant_providers.database')->setArguments([$config['db']]);

        $container->registerForAutoconfiguration(ConstantProviderInterface::class)->addTag('qce_wordpress.constant_provider');
    }

    public function getAlias(): string
    {
        return 'qce_wordpress';
    }
}
