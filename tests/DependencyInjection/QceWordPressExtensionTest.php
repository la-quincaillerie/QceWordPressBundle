<?php

namespace Qce\WordPressBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\DependencyInjection\QceWordPressExtension;
use Qce\WordPressBundle\WordPress\Constant\ConstantManagerInterface;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\DatabaseConstantProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class QceWordPressExtensionTest extends TestCase
{
    public const DEFAULT_CONFIGS = [ConfigurationTest::DEFAULT_CONFIG];
    private QceWordPressExtension $extension;
    private ContainerBuilder $container;

    public function testAliasRegister(): void
    {
        $this->container->registerExtension($this->extension);
        self::assertTrue($this->container->hasExtension('qce_wordpress'));
    }

    public function testConstantManagerService(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.constant_manager'));
        self::assertInstanceOf(ConstantManagerInterface::class, $this->container->get('qce_wordpress.constant_manager'));
    }

    public function testDatabaseConstantProviderService(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.constant_providers.database'));
        self::assertInstanceOf(DatabaseConstantProvider::class, $this->container->get('qce_wordpress.constant_providers.database'));
        self::assertTrue($this->container->findDefinition('qce_wordpress.constant_providers.database')->hasTag('qce_wordpress.constant_provider'));
        self::assertEquals([
            'DB_HOST' => 'db:3306',
            'DB_NAME' => 'db',
            'DB_USER' => 'db',
            'DB_PASSWORD' => 'db',
            'DB_CHARSET' => 'utf8mb4',
            'DB_COLLATE' => '',
        ], $this->container->get('qce_wordpress.constant_providers.database')->getConstants());
    }

    public function testAutowiredConstantProviders(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        $autoconfigured = $this->container->getAutoconfiguredInstanceof();
        self::assertArrayHasKey(ConstantProviderInterface::class, $autoconfigured);
        self::assertTrue($autoconfigured[ConstantProviderInterface::class]->hasTag('qce_wordpress.constant_provider'));
    }

    protected function setUp(): void
    {
        $this->extension = new QceWordPressExtension();
        $this->container = new ContainerBuilder();
    }
}
