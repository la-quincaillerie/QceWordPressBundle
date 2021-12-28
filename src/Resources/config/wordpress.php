<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\WordPress\Constant\ConstantManager;
use Qce\WordPressBundle\WordPress\Constant\DatabaseConstantProvider;
use Qce\WordPressBundle\WordPress\Constant\URLConstantProvider;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.constant_manager', ConstantManager::class)
        ->set('qce_wordpress.constant_providers.url', URLConstantProvider::class)
            ->args([abstract_arg('qce_wordpress.home_url'), abstract_arg('qce_wordpress.site_url')])
            ->tag('qce_wordpress.constant_provider')
        ->set('qce_wordpress.constant_providers.database', DatabaseConstantProvider::class)
            ->args([abstract_arg('qce_wordpress.db')])
            ->tag('qce_wordpress.constant_provider')
    ;
};
