<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\WordPress\Constant\ConstantManager;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.constant_manager', ConstantManager::class)
    ;
};
