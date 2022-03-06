<?php

namespace Qce\WordPressBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\Attribute\WPHook;
use Qce\WordPressBundle\Controller\WordPressController;
use Qce\WordPressBundle\DependencyInjection\QceWordPressExtension;
use Qce\WordPressBundle\WordPress\Constant\ConstantManagerInterface;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;
use Qce\WordPressBundle\WordPress\Constant\Provider\ConstantProvider;
use Qce\WordPressBundle\WordPress\Constant\Provider\DatabaseConstantProvider;
use Qce\WordPressBundle\WordPress\Constant\Provider\DirectoryConstantProvider;
use Qce\WordPressBundle\WordPress\Theme\Theme;
use Qce\WordPressBundle\WordPress\WordPress;
use Qce\WordPressBundle\WordPress\WordPressConfig;
use Qce\WordPressBundle\WordPress\WordPressHooks;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class QceWordPressExtensionTest extends TestCase
{
    public const DEFAULT_CONFIGS = [ConfigurationTest::DEFAULT_CONFIG, ['theme' => ['static' => 'test']]];
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
        self::assertTrue(\is_subclass_of($this->container->findDefinition('qce_wordpress.constant_manager')->getClass() ?? '', ConstantManagerInterface::class));
    }

    /**
     * @param class-string $expectedServiceClass
     * @param array<string, mixed> $extraConfig
     *
     * @dataProvider getConstantProviders
     */
    public function testContantProviderResult(string $serviceName, string $expectedServiceClass, array $extraConfig = []): void
    {
        $configs = self::DEFAULT_CONFIGS;
        if (!empty($extraConfig)) {
            $configs[] = $extraConfig;
        }
        $this->extension->load($configs, $this->container);
        $serviceId = 'qce_wordpress.constant_providers.' . $serviceName;
        self::assertTrue($this->container->has($serviceId));

        $serviceDefinition = $this->container->findDefinition($serviceId);
        $serviceClass = $serviceDefinition->getClass();
        self::assertTrue($serviceDefinition->hasTag('qce_wordpress.constant_provider'));
        self::assertNotNull($serviceClass);
        self::assertTrue(\is_subclass_of($serviceClass, ConstantProviderInterface::class, true));
        self::assertSame($expectedServiceClass, $serviceClass);
    }

    /**
     * @return array{string, class-string, 2?:array<string, mixed>}[]
     */
    public function getConstantProviders(): array
    {
        return [
            ['database', DatabaseConstantProvider::class],
            ['default', ConstantProvider::class],
            ['directories', DirectoryConstantProvider::class],
            ['extra', ConstantProvider::class, ['constants' => ['EXTRA_1' => 'extra_1']]],
        ];
    }

    public function testNoExtraConstantProvider(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertFalse($this->container->has('qce_wordpress.constant_providers.extra'));
    }

    public function testAutowiredConstantProviders(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        $autoconfigured = $this->container->getAutoconfiguredInstanceof();
        self::assertArrayHasKey(ConstantProviderInterface::class, $autoconfigured);
        self::assertTrue($autoconfigured[ConstantProviderInterface::class]->hasTag('qce_wordpress.constant_provider'));
    }

    public function testWordPressConfig(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.wordpress.config'));
        self::assertTrue($this->container->findDefinition('qce_wordpress.wordpress.config')->isPublic());
        self::assertSame(WordPressConfig::class, $this->container->findDefinition('qce_wordpress.wordpress.config')->getClass());
    }

    public function testWordPress(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.wordpress'));
        self::assertSame(WordPress::class, $this->container->findDefinition('qce_wordpress.wordpress')->getClass());
    }

    public function testWordPressController(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.wordpress.controller'));
        self::assertTrue($this->container->findDefinition('qce_wordpress.wordpress.controller')->isPublic());
        self::assertInstanceOf(WordPressController::class, $this->container->get('qce_wordpress.wordpress.controller'));
    }

    public function testWordPressHooks(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.wordpress.hooks'));
        self::assertTrue($this->container->has(WordPressHooks::class));
        $definition = $this->container->findDefinition('qce_wordpress.wordpress.hooks');
        self::assertTrue($definition->isPublic());
        self::assertSame(WordPressHooks::class, $definition->getClass());
        $pluginFile = \realpath($this->container->getParameterBag()->resolveValue($definition->getFile()));
        self::assertSame(\dirname(__DIR__) . '/test-wordpress/wp-includes/plugin.php', $pluginFile);
    }

    public function testAutoConfiguredHooks(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertArrayHasKey(WPHook::class, $this->container->getAutoconfiguredAttributes());
    }

    public function testAutoConfiguredHookClass(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        $callback = $this->container->getAutoconfiguredAttributes()[WPHook::class];

        $definition = new ChildDefinition('');
        $reflection = new \ReflectionClass(new class {
            public function __invoke(): void
            {
            }
        });
        $callback($definition, new WPHook('test', 1, 2), $reflection);

        self::assertTrue($definition->hasTag('qce_wordpress.hook'));
        self::assertEquals([
            'name' => 'test',
            'priority' => 1,
            'accepted_args' => 2,
            'method' => '__invoke',
        ], $definition->getTag('qce_wordpress.hook')[0]);
    }

    public function testAutConfiguredHookNoInvokeMethod(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        $callback = $this->container->getAutoconfiguredAttributes()[WPHook::class];

        $definition = new ChildDefinition('');
        $reflection = new \ReflectionClass(\stdClass::class);
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage("WPHook can only be used on methods or invokable services.");
        $callback($definition, new WPHook('test', 1, 2), $reflection);
    }

    public function testAutConfiguredHookClass(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        $callback = $this->container->getAutoconfiguredAttributes()[WPHook::class];

        $definition = new ChildDefinition('');
        $reflection = new \ReflectionMethod(new class {
            public function test(): void
            {
            }
        }, 'test');
        $callback($definition, new WPHook('test', 1, 2), $reflection);

        self::assertTrue($definition->hasTag('qce_wordpress.hook'));
        self::assertEquals([
            'name' => 'test',
            'priority' => 1,
            'accepted_args' => 2,
            'method' => 'test',
        ], $definition->getTag('qce_wordpress.hook')[0]);
    }

    public function testTheme(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.theme'));
        self::assertSame(Theme::class, $this->container->findDefinition('qce_wordpress.theme')->getClass());
    }

    public function testNoTheme(): void
    {
        $noThemeConfig = [['theme' => false]];
        $this->extension->load(\array_merge(self::DEFAULT_CONFIGS, $noThemeConfig), $this->container);
        self::assertFalse($this->container->has('qce_wordpress.theme'));
    }

    public function testThemeBuilder(): void
    {
        $this->extension->load(self::DEFAULT_CONFIGS, $this->container);
        self::assertTrue($this->container->has('qce_wordpress.theme.builder'));
        $definition = $this->container->findDefinition('qce_wordpress.theme.builder');
        self::assertTrue($definition->hasTag('qce_wordpress.hook'));
        $hookTags = array_filter($definition->getTag('qce_wordpress.hook'), static fn($tag) => $tag['name'] === 'setup_theme');
        self::assertCount(1, $hookTags);
    }

    protected function setUp(): void
    {
        $this->extension = new QceWordPressExtension();
        $this->container = new ContainerBuilder();
    }
}
