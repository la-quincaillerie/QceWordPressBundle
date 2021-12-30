<?php

namespace Qce\WordPressBundle;

use Qce\WordPressBundle\DependencyInjection\Compiler\WordPressHookPass;
use Qce\WordPressBundle\DependencyInjection\QceWordPressExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class QceWordPressBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new WordPressHookPass());
    }

    public function getContainerExtension(): ExtensionInterface
    {
        return ($this->extension ??= new QceWordPressExtension());
    }
}
