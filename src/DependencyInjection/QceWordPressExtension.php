<?php

namespace Qce\WordPressBundle\DependencyInjection;

use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
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

        $container->setParameter('qce_wordpress.wordpress_dir', $config['wordpress_dir']);

        $container->getDefinition('qce_wordpress.wordpress.config')->setArgument(1, $config['table_prefix']);
        $this->loadConstantProviders($config, $container);
        $this->loadWordPress($config, $container);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadConstantProviders(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('qce_wordpress.constant_providers.database')->setArguments([$config['db']]);
        $container->getDefinition('qce_wordpress.constant_providers.url')->setArguments([$config['home'], $config['site_url']]);
        if (!empty($config['constants'])) {
            $container->register('qce_wordpress.constant_providers.extra', ConstantProvider::class)
                ->setArguments([$config['constants']])
                ->addTag('qce_wordpress.constant_provider', ['priority' => -10]);
        }

        $container->registerForAutoconfiguration(ConstantProviderInterface::class)->addTag('qce_wordpress.constant_provider');
    }

    /**
     * @param array<string, mixed> $configs
     */
    private function loadWordPress(array $configs, ContainerBuilder $container): void
    {
        /** @var string[] $globals */
        $globals = $configs['globals'];
        $coreGlobals = ['wp', 'wp_the_query', 'wpdb', 'wp_query'];

        $container->findDefinition('qce_wordpress.wordpress')->setArgument(1, array_merge($coreGlobals, $globals));
    }

    public function getAlias(): string
    {
        return 'qce_wordpress';
    }
}
