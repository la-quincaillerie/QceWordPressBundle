<?php

namespace Qce\WordPressBundle\DependencyInjection;

use Qce\WordPressBundle\Attribute\WPHook;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Twig\Environment;

/**
 * @phpstan-import-type Config from Configuration
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
        $container->setParameter('qce_wordpress.table_prefix', $config['db']['table_prefix']);

        $this->loadConstantProviders($config, $container);
        $this->loadHooks($config, $container);
        $this->loadTheme($config, $container, $loader);
        $this->loadTwig($config, $container, $loader);
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
     * @param Config $config
     */
    private function loadTheme(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        $themeConfig = $config['theme'];

        if (!$this->isConfigEnabled($container, $themeConfig)) {
            return;
        }

        $loader->load('theme.php');

        $themeDefinition = $container->findDefinition('qce_wordpress.theme');
        $themeDefinition->setArgument(0, $themeConfig['slug']);
        $themeDefinition->setArgument(1, $themeConfig['headers']);

        $themeBuilderDefinition = $container->findDefinition('qce_wordpress.theme.builder');
        $themeBuilderDefinition->setArgument(3, $themeConfig['annotations']['directory']);
        $themeBuilderDefinition->setArgument(4, $themeConfig['annotations']['namespace']);
        $themeBuilderDefinition->setArgument(5, $themeConfig['static']);

        if (!$themeConfig['supports']) {
            $container->removeDefinition('qce_wordpress.theme.supports');
        } else {
            $themeSupportsDefinition = $container->findDefinition('qce_wordpress.theme.supports');
            $themeSupportsDefinition->setArgument(0, $themeConfig['supports']);
        }
    }

    /**
     * @param Config $config
     */
    private function loadTwig(array $config, ContainerBuilder $container, LoaderInterface $loader): void
    {
        if (!$this->isConfigEnabled($container, $config['twig'])) {
            return;
        }

        if (!ContainerBuilder::willBeAvailable('twig/twig', Environment::class, ['symfony/twig-bundle'])) {
            throw new \LogicException('Twig support cannot be enabled as the Twig bundle is not installed. Try running "composer require symfony/twig-bundle".');
        }

        $loader->load('twig.php');
    }

    public function getAlias(): string
    {
        return 'qce_wordpress';
    }
}
