<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\WordPressBundle\Bridge\Twig\WordPressExtension;
use Qce\WordPressBundle\Bridge\Twig\WordPressVariable;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('qce_wordpress.twig.extension', WordPressExtension::class)
            ->tag('twig.extension');
};
