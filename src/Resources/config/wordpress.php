<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\Controller\WordPressController;
use Qce\WordPressBundle\WordPress\Constant\ConstantManager;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Qce\WordPressBundle\WordPress\Constant\Provider\DatabaseConstantProvider;
use Qce\WordPressBundle\WordPress\WordPress;
use Qce\WordPressBundle\WordPress\WordPressConfig;
use Qce\WordPressBundle\WordPress\WordPressHooks;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.wordpress.config', WordPressConfig::class)
            ->args([
                param('qce_wordpress.dir.wordpress'),
                abstract_arg('qce_wordpress.table_prefix'),
                tagged_iterator('qce_wordpress.constant_provider'),
                service('qce_wordpress.constant_manager'),
                service('qce_wordpress.wordpress.hooks')
            ])
            ->public()
        ->set('qce_wordpress.constant_providers.default', ConstantProvider::class)
            ->args([[
                'WP_HOME' => param('qce_wordpress.url.home'),
                'WP_SITEURL' => param('qce_wordpress.url.site'),
                'WP_CONTENT_URL' => param('qce_wordpress.url.content'),
                'WP_CONTENT_DIR' => param('qce_wordpress.dir.content'),
            ]])
            ->tag('qce_wordpress.constant_provider')
        ->set('qce_wordpress.constant_providers.database', DatabaseConstantProvider::class)
            ->args([abstract_arg('qce_wordpress.db')])
            ->tag('qce_wordpress.constant_provider')
        ->set('qce_wordpress.constant_manager', ConstantManager::class)

        ->set('qce_wordpress.wordpress', WordPress::class)
            ->args([param('qce_wordpress.dir.wordpress')])

        ->set('qce_wordpress.wordpress.controller', WordPressController::class)
            ->args([service('qce_wordpress.wordpress')])
            ->public()

        ->set('qce_wordpress.wordpress.hooks', WordPressHooks::class)
            ->file('%qce_wordpress.dir.wordpress%/wp-includes/plugin.php')
            ->args([tagged_iterator('qce_wordpress.hook')])
            ->public()
        ->alias(WordPressHooks::class, 'qce_wordpress.wordpress.hooks')
    ;
};
