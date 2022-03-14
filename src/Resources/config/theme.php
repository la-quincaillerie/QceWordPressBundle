<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\WordPress\Theme\Builder\ThemeBuilder;
use Qce\WordPressBundle\WordPress\Theme\Cache\ThemeClearerWarmer;
use Qce\WordPressBundle\WordPress\Theme\Loader\AnnotationClassLoader;
use Qce\WordPressBundle\WordPress\Theme\Loader\NamespaceLoader;
use Qce\WordPressBundle\WordPress\Theme\Theme;
use Qce\WordPressBundle\WordPress\Theme\ThemeSupports;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.theme', Theme::class)
            ->args([
                abstract_arg('qce_wordpress.theme.slug'),
                abstract_arg('qce_wordpress.theme.headers'),
                service('fragment.handler')->nullOnInvalid(),
            ])
            ->public()

        ->set('qce_wordpress.theme.supports', ThemeSupports::class)
            ->args([
                abstract_arg('qce_wordpress.theme.supports'),
            ])
            ->tag('qce_wordpress.hook', ['name' => 'setup_theme', 'method' => 'registerThemeSupports', 'accepted_args' => 0])

        ->set('qce_wordpress.theme.builder', ThemeBuilder::class)
            ->args([
                '%qce_wordpress.dir.content%/themes',
                service('qce_wordpress.theme'),
                service('qce_wordpress.theme.loader'),
                abstract_arg('qce_wordpress.theme.routes.directory'),
                abstract_arg('qce_wordpress.them.routes.namespace'),
                param('kernel.debug'),
            ])
            ->call('setConfigCacheFactory', [service('config_cache_factory')])
            ->tag('qce_wordpress.hook', ['name' => 'setup_theme', 'method' => 'build', 'accepted_args' => 0])

        ->set('qce_wordpress.theme.resolver', LoaderResolver::class)
        ->set('qce_wordpress.theme.loader.namespace', NamespaceLoader::class)
            ->args([
                service('file_locator'),
                '%kernel.environment%',
            ])
            ->tag('qce_wordpress.theme.loader')
        ->set('qce_wordpress.theme.loader.annotation', AnnotationClassLoader::class)
            ->args([
                '%kernel.environment%',
            ])
            ->tag('qce_wordpress.theme.loader')
        ->set('qce_wordpress.theme.loader', DelegatingLoader::class)
            ->args([
                service('qce_wordpress.theme.resolver'),
            ])

        ->set('qce_wordpress.theme.clearer_warmer', ThemeClearerWarmer::class)
            ->args([service('qce_wordpress.theme.builder')])
            ->tag('kernel.cache_clearer')
            ->tag('kernel.cache_warmer');
};
