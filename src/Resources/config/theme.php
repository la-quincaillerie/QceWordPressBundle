<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\WordPress\Theme\Builder\ThemeBuilder;
use Qce\WordPressBundle\WordPress\Theme\Cache\ThemeClearerWarmer;
use Qce\WordPressBundle\WordPress\Theme\Theme;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.theme', Theme::class)
            ->args([
                abstract_arg('qce_wordpress.theme.slug'),
                abstract_arg('qce_wordpress.theme.headers'),
                [],
                abstract_arg('qce_wordpress.theme.static'),
                service('fragment.handler')->nullOnInvalid(),
            ])
            ->public()

        ->set('qce_wordpress.theme.builder', ThemeBuilder::class)
            ->args([
                '%qce_wordpress.dir.content%/themes',
                service('qce_wordpress.theme'),
                param('kernel.debug'),
            ])
            ->call('setConfigCacheFactory', [service('config_cache_factory')])
            ->tag('qce_wordpress.hook', ['name' => 'setup_theme', 'method' => 'build', 'accepted_args' => 0])

        ->set('qce_wordpress.theme.clearer_warmer', ThemeClearerWarmer::class)
            ->args([service('qce_wordpress.theme.builder')])
            ->tag('kernel.cache_clearer')
            ->tag('kernel.cache_warmer');
};
