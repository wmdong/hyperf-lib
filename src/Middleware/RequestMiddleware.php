<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wmud\HyperfLib\Log\AppLog;

/**
 * 请求中间件
 */
class RequestMiddleware extends AppBaseMiddleware
{
    /**
     * 逻辑处理
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function logic(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestId = $request->getHeaderLine('Request-Id') ?: uniqid();
        Context::set('requestId', $requestId);
        AppLog::info("Api Request $this->uri $this->ip", [
            'params' => $this->params,
        ]);
        return $handler->handle($request);
    }
}
