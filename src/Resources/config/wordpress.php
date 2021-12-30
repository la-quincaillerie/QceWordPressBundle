<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\Controller\WordPressController;
use Qce\WordPressBundle\WordPress\Constant\ConstantManager;
use Qce\WordPressBundle\WordPress\Constant\Provider\DatabaseConstantProvider;
use Qce\WordPressBundle\WordPress\Constant\Provider\URLConstantProvider;
use Qce\WordPressBundle\WordPress\WordPress;
use Qce\WordPressBundle\WordPress\WordPressConfig;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.wordpress.config', WordPressConfig::class)
            ->args([
                param('qce_wordpress.wordpress_dir'),
                abstract_arg('qce_wordpress.table_prefix'),
                tagged_iterator('qce_wordpress.constant_provider'),
                service('qce_wordpress.constant_manager'),
            ])
            ->public()
        ->set('qce_wordpress.constant_providers.url', URLConstantProvider::class)
            ->args([abstract_arg('qce_wordpress.home_url'), abstract_arg('qce_wordpress.site_url')])
            ->tag('qce_wordpress.constant_provider')
        ->set('qce_wordpress.constant_providers.database', DatabaseConstantProvider::class)
            ->args([abstract_arg('qce_wordpress.db')])
            ->tag('qce_wordpress.constant_provider')
        ->set('qce_wordpress.constant_manager', ConstantManager::class)

        ->set('qce_wordpress.wordpress', WordPress::class)
            ->args([
                param('qce_wordpress.wordpress_dir'),
                abstract_arg('qce_wordpress.globals')
            ])

        ->set('qce_wordpress.wordpress.controller', WordPressController::class)
            ->args([service('qce_wordpress.wordpress')])
            ->public()
    ;
};
