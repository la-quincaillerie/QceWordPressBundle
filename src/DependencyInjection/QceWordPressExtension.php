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

/**
 * @phpstan-type Config array{
 *   path: array{
 *     wordpress?: string,
 *     content: string,
 *   },
 *   dir: array{
 *     wordpress: string,
 *     content: string,
 *   },
 *   url: array{
 *     home: string,
 *     site: string,
 *     content: string,
 *   },
 *   globals: string[],
 *   constants: array<string, string>,
 *   db: array{
 *     url?: string,
 *     dbname?: string,
 *     host: string,
 *     port: string,
 *     user: string,
 *     password: string,
 *     charset: string,
 *     collate: string,
 *     table_prefix: string,
 *   },
 * }
 */
class QceWordPressExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('wordpress.php');

        /** @var Config $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->loadPathParameters($config, $container);
        $this->loadDirParameters($config, $container);
        $this->loadURLParameters($config, $container);

        $container->getDefinition('qce_wordpress.wordpress.config')->setArgument(1, $config['db']['table_prefix']);
        $this->loadConstantProviders($config, $container);
        $this->loadWordPress($config, $container);
        $this->loadHooks($config, $container);
    }

    /**
     * @param Config $config
     */
    private function loadPathParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('qce_wordpress.path.wordpress', $config['path']['wordpress'] ?? basename($config['dir']['wordpress']));
        $container->setParameter('qce_wordpress.path.content', $config['path']['content']);
    }

    /**
     * @param Config $config
     */
    private function loadDirParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('qce_wordpress.dir.wordpress', $config['dir']['wordpress']);
        $container->setParameter('qce_wordpress.dir.content', $config['dir']['content']);
    }

    /**
     * @param Config $config
     */
    private function loadURLParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('qce_wordpress.url.home', $config['url']['home']);
        $container->setParameter('qce_wordpress.url.site', $config['url']['site']);
        $container->setParameter('qce_wordpress.url.content', $config['url']['content']);
    }

    /**
     * @param Config $config
     */
    private function loadConstantProviders(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('qce_wordpress.constant_providers.database')->setArguments([$config['db']]);
        if (!empty($config['constants'])) {
            $container->register('qce_wordpress.constant_providers.extra', ConstantProvider::class)
                ->setArguments([$config['constants']])
                ->addTag('qce_wordpress.constant_provider', ['priority' => -10]);
        }

        $container->registerForAutoconfiguration(ConstantProviderInterface::class)->addTag('qce_wordpress.constant_provider');
    }

    /**
     * @param Config $configs
     */
    private function loadWordPress(array $configs, ContainerBuilder $container): void
    {
        /** @var string[] $globals */
        $globals = $configs['globals'];
        $coreGlobals = ['wp', 'wp_the_query', 'wpdb', 'wp_query'];

        $container->findDefinition('qce_wordpress.wordpress')->setArgument(1, array_merge($coreGlobals, $globals));
    }

    /**
     * @param Config $configs
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
