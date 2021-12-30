<?php

namespace Qce\WordPressBundle\DependencyInjection;

use Qce\WordPressBundle\Attribute\WPHook;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
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
        $this->loadHooks($config, $container);
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

    /**
     * @param array<string, mixed> $configs
     */
    private function loadHooks(array $configs, ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            WPHook::class,
            static function (ChildDefinition $definition, WPHook $hook, \Reflector $reflector) {
                $methodReflector = match (true) {
                    $reflector instanceof \ReflectionMethod => $reflector,
                    $reflector instanceof \ReflectionClass && $reflector->hasMethod('__invoke') => $reflector->getMethod('__invoke'),
                    default => throw new InvalidConfigurationException(sprintf("%s can only be used on methods or invokable services", WPHook::class))
                };
                $args = [
                    'name' => $hook->name,
                    'priority' => $hook->priority,
                    'accepted_args' => $hook->acceptedArgs ?? count($methodReflector->getParameters()),
                    'method' => $methodReflector->getName(),
                ];
                $definition->addTag('qce_wordpress.wordpress_hook', $args);
            });
    }

    public function getAlias(): string
    {
        return 'qce_wordpress';
    }
}
