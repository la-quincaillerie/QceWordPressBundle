<?php

namespace Qce\WordPressBundle\DependencyInjection;

use Qce\WordPressBundle\Attribute\WPHook;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Qce\WordPressBundle\WordPress\Theme\Attribute\ThemeFile;
use Qce\WordPressBundle\WordPress\Theme\ThemeController;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @phpstan-import-type Config from Configuration
 * @phpstan-import-type ThemeConfig from Configuration
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
        $this->loadHooks($config, $container);
        $this->loadTheme($config['theme'], $container, $loader);
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
     * @param Config $config
     */
    private function loadHooks(array $config, ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            WPHook::class,
            static function (ChildDefinition $definition, WPHook $hook, \Reflector $reflector) {
                $methodReflector = match (true) {
                    $reflector instanceof \ReflectionMethod => $reflector,
                    $reflector instanceof \ReflectionClass && $reflector->hasMethod('__invoke') => $reflector->getMethod('__invoke'),
                    default => throw new InvalidConfigurationException(sprintf("%s can only be used on methods or invokable services.", WPHook::class))
                };
                $args = [
                    'name' => $hook->name,
                    'priority' => $hook->priority,
                    'accepted_args' => $hook->acceptedArgs ?? count($methodReflector->getParameters()),
                    'method' => $methodReflector->getName(),
                ];
                $definition->addTag('qce_wordpress.hook', $args);
            });
    }

    /**
     * @param ThemeConfig $config
     */
    public function loadTheme(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        if (!$config['enabled']) {
            return;
        }

        $loader->load('theme.php');

        $themeDefinition = $container->findDefinition('qce_wordpress.theme');
        $themeDefinition->setArgument(0, $config['slug']);
        $themeDefinition->setArgument(1, $config['headers']);
        $themeDefinition->setArgument(3, $config['static']);

        $container->registerAttributeForAutoconfiguration(
            ThemeFile::class,
            function (ChildDefinition $definition, ThemeFile $file, \Reflector $reflector) use ($themeDefinition) {
                $methodReflector = match (true) {
                    $reflector instanceof \ReflectionMethod => $reflector,
                    $reflector instanceof \ReflectionClass && $reflector->hasMethod('__invoke') => $reflector->getMethod('__invoke'),
                    default => throw new InvalidConfigurationException(sprintf("%s can only be used on methods or invokable services.", ThemeFile::class))
                };
                $controller = $methodReflector->class . '::' . $methodReflector->name;

                /** @var array<string, Definition> $controllers */
                $controllers = $themeDefinition->getArgument(2);
                $controllers[$file->target] = new Definition(ThemeController::class, [$controller, $file->headers]);
                $themeDefinition->replaceArgument(2, $controllers);
            }
        );
    }

    public function getAlias(): string
    {
        return 'qce_wordpress';
    }
}
