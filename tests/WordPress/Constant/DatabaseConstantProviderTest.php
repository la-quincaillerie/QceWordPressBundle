<?php

namespace Qce\WordPressBundle\Tests\WordPress\Constant;

use PHPUnit\Framework\TestCase;
use Qce\WordPressBundle\WordPress\Constant\DatabaseConstantProvider;

class DatabaseConstantProviderTest extends TestCase
{
    public function testVariables(): void
    {
        $provider = new DatabaseConstantProvider([
            'dbname' => 'dbname',
            'host' => 'host',
            'port' => '1234',
            'user' => 'user',
            'password' => 'password',
            'charset' => 'charset',
            'collate' => 'collate',
        ]);

        self::assertEquals([
            'DB_NAME' => 'dbname',
            'DB_HOST' => 'host:1234',
            'DB_USER' => 'user',
            'DB_PASSWORD' => 'password',
            'DB_CHARSET' => 'charset',
            'DB_COLLATE' => 'collate',
        ], $provider->getConstants());
    }

    public function testDSN(): void
    {
        $provider = new DatabaseConstantProvider([
            'url' => 'mysql://user:password@host:1234/dbname?charset=charset&collate=collate'
        ]);

        self::assertEquals([
            'DB_NAME' => 'dbname',
            'DB_HOST' => 'host:1234',
            'DB_USER' => 'user',
            'DB_PASSWORD' => 'password',
            'DB_CHARSET' => 'charset',
            'DB_COLLATE' => 'collate',
        ], $provider->getConstants());
    }
}
