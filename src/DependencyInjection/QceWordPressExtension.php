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

        $this->loadConstantProviders($config, $container);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadConstantProviders(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('qce_wordpress.constant_providers.database')->setArguments([$config['db']]);
        $container->getDefinition('qce_wordpress.constant_providers.url')->setArguments([$config['home'], $config['site_url']]);

        $container->registerForAutoconfiguration(ConstantProviderInterface::class)->addTag('qce_wordpress.constant_provider');
    }

    public function getAlias(): string
    {
        return 'qce_wordpress';
    }
}
