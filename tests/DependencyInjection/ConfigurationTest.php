<?php

namespace Qce\WordPressBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public const DEFAULT_CONFIG = [
        'home' => 'https://localhost',
        'site_url' => 'https://localhost/wp',
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
        ], $config['db']);
    }

    /**
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function processConfig(array $config = [], bool $withDefault = true): array
    {
        if ($withDefault) {
            $configs[] = self::DEFAULT_CONFIG;
        }
        $configs[] = $config;
        return $this->processor->processConfiguration($this->configuration, $configs);
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

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

}
