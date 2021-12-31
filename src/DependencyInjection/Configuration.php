<?php

namespace Qce\WordPressBundle\DependencyInjection;

use Composer\InstalledVersions;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('qce_wordpress');
        $rootNode = $treeBuilder->getRootNode();

        $this->addPathSection($rootNode);
        $this->addDirectorySection($rootNode);
        $this->addURLSection($rootNode);
        $this->addExtraConstantsSection($rootNode);
        $this->addDatabaseSection($rootNode);
        $this->addGlobalsSection($rootNode);

        return $treeBuilder;
    }

    private function addPathSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('path')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('wordpress')->end()
                        ->scalarNode('content')->defaultValue('wp-bundles')->end()
        ;
    }

    private function addDirectorySection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('dir')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(fn($wordpress) => ['wordpress' => $wordpress])
                    ->end()
                    ->children()
                        ->scalarNode('wordpress')->defaultValue(InstalledVersions::getInstallPath('roots/wordpress'))->end()
                        ->scalarNode('content')->defaultValue('%qce_wordpress.dir.wordpress%/../%qce_wordpress.path.content%')->end()
        ;
    }

    private function addURLSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('url')
                    ->addDefaultsIfNotSet()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(fn($home_url) => ['home' => $home_url])
                    ->end()
                    ->children()
                        ->scalarNode('home')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('site')->defaultValue('%qce_wordpress.url.home%/%qce_wordpress.path.wordpress%')->end()
                        ->scalarNode('content')->defaultValue('%qce_wordpress.url.home%/%qce_wordpress.path.content%')->end()
        ;
    }

    private function addExtraConstantsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->fixXmlConfig('constant')
            ->children()
                ->arrayNode('constants')
                    ->normalizeKeys(false)
                    ->scalarPrototype()
        ;
    }

    private function addDatabaseSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('db')
                    ->isRequired()
                    ->beforeNormalization()
                        ->ifString()
                        ->then(fn ($v) => ['url' => $v])
                    ->end()
                    ->validate()
                        ->always(function ($v) {
                            if(!is_array($v) || (!isset($v['url']) && !isset($v['dbname']))) {
                                throw new InvalidConfigurationException('You need to configure the database name through qce_wordpress.db.dbname or qce_wordpress.db.url.');
                            }
                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('url')->info('A URL with connection information; any parameter value parsed from this string will override explicitly set parameters')->end()
                        ->scalarNode('dbname')->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultValue('3306')->end()
                        ->scalarNode('user')->defaultValue('root')->end()
                        ->scalarNode('password')->defaultValue('')->end()
                        ->scalarNode('charset')->defaultValue('utf8mb4')->end()
                        ->scalarNode('collate')->defaultValue('')->end()
                        ->scalarNode('table_prefix')->defaultValue('wp_')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addGlobalsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->fixXmlConfig('global')
            ->children()
                ->arrayNode('globals')
                    ->scalarPrototype()
        ;
    }
}
