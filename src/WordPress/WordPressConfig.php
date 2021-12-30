<?php

namespace Qce\WordPressBundle\WordPress;

use Qce\WordPressBundle\WordPress\Constant\ConstantManagerInterface;
use Qce\WordPressBundle\WordPress\Constant\ConstantProviderInterface;

class WordPressConfig
{
    /**
     * @param iterable<ConstantProviderInterface> $constantProviders
     */
    public function __construct(
        private string                   $wordpressDir,
        private string                   $tablePrefix,
        private iterable                 $constantProviders,
        private ConstantManagerInterface $constantManager,
    )
    {
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
        foreach ($this->constantProviders as $p){
            $constants[] = (array)$p->getConstants();
        }
        return array_merge(...$constants);
    }

    public function includeSettings(): void
    {
        $table_prefix = $this->tablePrefix;
        include $this->wordpressDir . "/wp-settings.php";
    }
}
