<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Http\Swoole;

use Swoole\Coroutine\Http\Client as HttpClient;

/**
 * Swoole http客户端
 */
class Client extends HttpClient
{
    /**
     * @param string $domain
     * @param int $port
     * @param bool $ssl
     */
    public function __construct(string $domain, int $port = 0, bool $ssl = false)
    {
        $parse = parse_url($domain);
        $port = $port ?: ($parse['port'] ?? 0);
        parent::__construct($parse['host'], $port, $ssl);
    }


}