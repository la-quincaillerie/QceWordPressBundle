<?php

namespace Qce\WordPressBundle\WordPress;

use Qce\WordPressBundle\WordPress\Constant\ConstantManagerInterface;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;

class WordPressConfig
{
    /** @var array{string, callable, int, int}[] $hooks */
    private array $hooks = [];

    /**
     * @param iterable<ConstantProviderInterface> $constantProviders
     */
    public function __construct(
        private string                   $wordpressDir,
        private string                   $tablePrefix,
        private iterable                 $constantProviders,
        private ConstantManagerInterface $constantManager,
        private WordPressHooks           $hooksManager,
    )
    {
    }

    public function setup(): void
    {
        // We register the hooks before loading the settings because some filters and actions are triggered there.
        $this->registerHooks();
        $this->defineConstants();
        $this->includeSettings();
    }

    public function registerHooks(): void
    {
        foreach ($this->hooks as $args) {
            $this->hooksManager->addHook(...$args);
        }
    }

    public function defineConstants(): void
    {
        foreach ($this->getConstants() as $name => $value) {
            $this->constantManager->define($name, $value);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getConstants(): array
    {
        $constants = [];
        foreach ($this->constantProviders as $p) {
            $constants[] = (array)$p->getConstants();
        }
        return array_merge(...$constants);
    }

    public function includeSettings(): void
    {
        $table_prefix = $this->tablePrefix;
        include $this->wordpressDir . "/wp-settings.php";
    }

    public function addHook(string $name, callable $callback, int $priority, int $acceptedArgs): void
    {
        $this->hooks[] = [$name, $callback, $priority, $acceptedArgs];
    }
}
