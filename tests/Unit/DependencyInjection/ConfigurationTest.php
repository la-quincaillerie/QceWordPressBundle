<?php

namespace Qce\WordPressBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @phpstan-import-type Config from \Qce\WordPressBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{
    public const DEFAULT_CONFIG = [
        'url' => 'https://localhost',
        'dir' => __DIR__ . '/../test-wordpress',
        'db' => 'mysql://db:db@db/db',
    ];
    private Configuration $configuration;
    private Processor $processor;

    public function testDatabaseConfig(): void
    {
        $config = $this->processConfig([
            'db' => [
                'url' => 'url',
                'user' => 'user',
                'password' => 'pass',
                'host' => 'host',
                'port' => 1234,
                'dbname' => 'dbname',
                'charset' => 'charset',
                'collate' => 'collate',
            ]
        ]);
        self::assertEquals([
            'url' => 'url',
            'user' => 'user',
            'password' => 'pass',
            'host' => 'host',
            'port' => 1234,
            'dbname' => 'dbname',
            'charset' => 'charset',
            'collate' => 'collate',
            'table_prefix' => 'wp_'
        ], $config['db']);
    }

    /**
     * @param array<string, mixed> $config
     * @return Config
     */
    private function processConfig(array $config = [], bool $withDefault = true): array
    {
        if ($withDefault) {
            $configs[] = self::DEFAULT_CONFIG;
        }
        $configs[] = $config;

        /** @var Config $processed */
        $processed = $this->processor->processConfiguration($this->configuration, $configs);
        return $processed;
    }

    public function testMissingDatabaseConfig(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "db" under "qce_wordpress" must be configured.');
        $defaultConfig = self::DEFAULT_CONFIG;
        unset($defaultConfig['db']);
        $this->processConfig($defaultConfig, false);
    }

    public function testMissingDatabaseName(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('You need to configure the database name through qce_wordpress.db.dbname or qce_wordpress.db.url.');
        $defaultConfig = self::DEFAULT_CONFIG;
        $defaultConfig['db'] = [];
        $this->processConfig($defaultConfig, false);
    }

    public function testThemeConfig(): void
    {
        $config = $this->processConfig();
        self::assertEquals([
            'enabled' => true,
            'static' => '%kernel.project_dir%/theme',
            'slug' => 'qce-theme',
            'headers' => [],
            'annotations' => [
                'namespace' => 'App\\',
                'directory' => '%kernel.project_dir%/src',
            ],
            'supports' => [],
        ], $config['theme']);
    }

    public function testThemeSupport(): void
    {
        $config = $this->processConfig([
            'theme' => [
                'supports' => [
                    'feature1',
                    ['feature' => 'feature2', 'args' => 'argumentValue'],
                ]
            ]
        ]);
        self::assertSame([
            ['feature' => 'feature1', 'args' => []],
            ['feature' => 'feature2', 'args' => 'argumentValue'],
        ], $config['theme']['supports']);
    }

    public function testThemeDisabledConfig(): void
    {
        $config = $this->processConfig(['theme' => false]);
        self::assertFalse($config['theme']['enabled']);
    }

    public function testEnableTwig(): void
    {
        $config = $this->processConfig(['twig' => true]);
        self::assertTrue($config['twig']['enabled']);
    }

    public function testDisableTwig(): void
    {
        $config = $this->processConfig(['twig' => false]);
        self::assertFalse($config['twig']['enabled']);
    }

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }
}
