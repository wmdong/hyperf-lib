<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Collection\Arr;

/**
 * 中间件基类
 */
abstract class AppBaseMiddleware implements MiddlewareInterface
{
    /**
     * 请求URI
     * @var string|null
     */
    protected ?string $uri = null;

    /**
     * 请求参数
     * @var array|null
     */
    protected ?array $params = null;

    /**
     * 请求IP
     * @var string|null
     */
    protected ?string $ip = null;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getParsedBody();
        $query = $request->getQueryParams();
        $this->params = array_merge($body, $query);
        $serverParams = $request->getServerParams();
        $this->uri = Arr::get($serverParams, 'request_uri');
        $remoteAddr = Arr::get($serverParams, 'remote_addr');
        $realIp = $request->getHeaderLine('X-Real-IP');
        $this->ip = $realIp ?: $remoteAddr;
        return $this->logic($request, $handler);
    }

    /**
     * 逻辑处理
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    abstract public function logic(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
