<?php

namespace Qce\WordPressBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\DependencyInjection\QceWordPressExtension;
use Qce\WordPressBundle\WordPress\Constant\ConstantManagerInterface;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\DatabaseConstantProvider;
use Qce\WordPressBundle\WordPress\Constant\URLConstantProvider;
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

    /**
     * @param class-string $serviceClass
     * @param array<string, mixed> $constants
     *
     * @dataProvider getConstantProviders
     */
    public function testContantProviderResult(string $serviceName, string $serviceClass, array $constants): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        $serviceId = 'qce_wordpress.constant_providers.' . $serviceName;
        self::assertTrue($this->container->has($serviceId));
        self::assertTrue($this->container->findDefinition($serviceId)->hasTag('qce_wordpress.constant_provider'));

        $service = $this->container->get($serviceId);
        self::assertInstanceOf(ConstantProviderInterface::class, $service);
        self::assertInstanceOf($serviceClass, $service);
        self::assertEquals($constants, $service->getConstants());
    }

    /**
     * @return array{string, class-string, array<string, mixed>}[]
     */
    public function getConstantProviders(): array
    {
        return [
            ['database', DatabaseConstantProvider::class, [
                'DB_HOST' => 'db:3306',
                'DB_NAME' => 'db',
                'DB_USER' => 'db',
                'DB_PASSWORD' => 'db',
                'DB_CHARSET' => 'utf8mb4',
                'DB_COLLATE' => '',
            ]],
            ['url', URLConstantProvider::class, [
                'WP_HOME' => 'https://localhost',
                'WP_SITEURL' => 'https://localhost/wp',
            ]],
        ];
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
