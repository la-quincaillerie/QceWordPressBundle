<?php

namespace Qce\WordPressBundle\WordPress\Constant;

/**
 * @phpstan-type Param array{
 *      url?: string,
 *      dbname?: string,
 *      host?: string,
 *      port?: string,
 *      user?: string,
 *      password?: string,
 *      charset?: string,
 *      collate?: string,
 * }
 */
class DatabaseConstantProvider implements ConstantProviderInterface
{
    private string $database;
    private string $host;
    private string $port;
    private string $user;
    private string $password;
    private string $charset;
    private string $collate;

    /**
     * @param Param $params
     */
    public function __construct(array $params)
    {
        $parsed = $this->parseDSN($params['url'] ?? "") + $params;

        $this->database = $parsed['dbname'] ?? "";
        $this->host = $parsed['host'] ?? "";
        $this->port = $parsed['port'] ?? "";
        $this->user = $parsed['user'] ?? "";
        $this->password = $parsed['password'] ?? "";
        $this->charset = $parsed['charset'] ?? "";
        $this->collate = $parsed['collate'] ?? "";
    }

    /**
     * @return Param
     */
    private function parseDSN(string $url): array
    {
        $dsn = parse_url($url) ?: [];

        parse_str($dsn['query'] ?? '', $query);
        return array_filter([
            'dbname' => ltrim($dsn['path'] ?? '', '/'),
            'host' => $dsn['host'] ?? '',
            'port' => (string)($dsn['port'] ?? ''),
            'user' => $dsn['user'] ?? '',
            'password' => $dsn['pass'] ?? '',
            'charset' => $query['charset'] ?? '',
            'collate' => $query['collate'] ?? '',
        ]);
    }

    public function getConstants(): iterable
    {
        return [
            'DB_NAME' => $this->database,
            'DB_HOST' => $this->host . ':' . $this->port,
            'DB_USER' => $this->user,
            'DB_PASSWORD' => $this->password,
            'DB_CHARSET' => $this->charset,
            'DB_COLLATE' => $this->collate,
        ];
    }
}
