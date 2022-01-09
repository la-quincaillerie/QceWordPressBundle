<?php

namespace Qce\WordPressBundle\Tests\Unit\WordPress\Theme;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Theme\Builder\ThemeBuilder;
use Qce\WordPressBundle\WordPress\Theme\Theme;
use Qce\WordPressBundle\WordPress\Theme\ThemeController;
use Symfony\Component\Filesystem\Filesystem;

class ThemeBuilderTest extends TestCase
{
    private string $dir;
    private string $themeSlug;

    public function testBuildTheme(): void
    {
        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, ['Header' => 'value']));
        $themeBuilder->build();

        $stylePath = "$this->dir/$this->themeSlug/style.css";
        self::assertFileExists($stylePath);
        self::assertStringContainsString('Header: value', file_get_contents($stylePath) ?: "");
    }

    public function testBuildThemeController(): void
    {
        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, controllers: [
            'index.php' => new ThemeController(__METHOD__),
            'woocommerce/test.php' => new ThemeController(__METHOD__),
        ]));
        $themeBuilder->build();
        self::assertFileExists("$this->dir/$this->themeSlug/index.php");
        self::assertFileExists("$this->dir/$this->themeSlug/woocommerce/test.php");
    }

    public function testBuildThemeStatic(): void
    {
        $staticDir = "$this->dir/$this->themeSlug-staticAssets";
        $fs = new Filesystem();
        $fs->mkdir($staticDir);
        $fs->touch("$staticDir/test.txt");

        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, staticDir: $staticDir));
        $themeBuilder->build();
        self::assertFileExists("$this->dir/$this->themeSlug/test.txt");
    }

    public function testBuildAssetsNoChangeInProd(): void
    {
        $staticDir = "$this->dir/$this->themeSlug-staticAssets";
        $fs = new Filesystem();
        $fs->mkdir($staticDir);

        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, staticDir: $staticDir), false);
        $themeBuilder->build();

        $fs->touch("$staticDir/test.txt", time() + 5);
        $themeBuilder->build();

        self::assertFileDoesNotExist("$this->dir/$this->themeSlug/test.txt");
    }

    public function testBuildThemeChangeAssetsInDebug(): void
    {
        $staticDir = "$this->dir/$this->themeSlug-staticAssets";
        $fs = new Filesystem();
        $fs->mkdir($staticDir);

        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, staticDir: $staticDir), true);
        $themeBuilder->build();
        self::assertFileDoesNotExist("$staticDir/test.txt");

        $fs->touch("$staticDir/test.txt", time() + 5);
        $themeBuilder->build();
        self::assertFileExists("$this->dir/$this->themeSlug/test.txt");
    }

    public function testBuildThemeRemoveFiles(): void
    {

        $staticDir = "$this->dir/$this->themeSlug-staticAssets";
        $fs = new Filesystem();
        $fs->mkdir($staticDir);
        $fs->touch("$staticDir/test.txt");

        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, staticDir: $staticDir), true);
        $themeBuilder->build();
        self::assertFileExists("$this->dir/$this->themeSlug/test.txt");

        $fs->remove($staticDir . '/test.txt');
        $fs->touch($staticDir, time() + 5); // simulate some delay to when file was deleted
        $themeBuilder->build();
        self::assertFileDoesNotExist("$this->dir/$this->themeSlug/test.txt");
    }

    public function testBuildThemeRemoveStatic(): void
    {
        $staticDir = "$this->dir/$this->themeSlug-staticAssets";
        $fs = new Filesystem();
        $fs->mkdir($staticDir);
        $fs->touch("$staticDir/test.txt");

        $themeBuilder = new ThemeBuilder($this->dir, new Theme($this->themeSlug, staticDir: $staticDir), true);
        $themeBuilder->build();
        self::assertFileExists("$this->dir/$this->themeSlug/test.txt");

        $fs->remove($staticDir);
        $themeBuilder->build();

        self::assertFileDoesNotExist("$this->dir/$this->themeSlug/test.txt");
    }

    public function setUp(): void
    {
        $this->dir = sys_get_temp_dir();
        $this->themeSlug = 'qcewordpressbundle-theme';
    }

    protected function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove("$this->dir/$this->themeSlug");
        $fs->remove("$this->dir/$this->themeSlug-staticAssets");
    }
}
