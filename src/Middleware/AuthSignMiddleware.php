<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpServerResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Safety\AppSafety;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\Collection\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 签名验证中间件
 */
class AuthSignMiddleware implements MiddlewareInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (config('sign.auth', false)) {
            $timestamp = (int)$request->getHeaderLine('Timestamp'); // 请求时间戳
            $diffTime = time() - $timestamp;
            $timeout = (int)config('sign.timeout', 10); // 签名失效时间
            if ($diffTime > $timeout || $diffTime < 0) { // 签名时效验证（秒）
                return $this->responseHandel([
                    'message' => '签名已过期',
                    'timestamp' => $timestamp,
                    'diffTime' => $diffTime
                ], $request);
            }
            $body = $request->getBody()->getContents(); // 请求体
            $requestSign = $request->getHeaderLine('Authorization'); // 请求签名
            $appSafety = new AppSafety();
            $sign = $appSafety->sign($body, $timestamp);
            if ($sign !== $requestSign) {
                return $this->responseHandel([
                    'message' => '签名对比错误',
                    'sign' => $sign,
                    'requestSign' => $requestSign,
                    'body' => $body,
                    'timestamp' => $timestamp
                ], $request);
            }
        }
        return $handler->handle($request);
    }

    /**
     * 响应处理
     * @param array $params
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function responseHandel(array $params, ServerRequestInterface $request): ResponseInterface
    {
        $container = ApplicationContext::getContainer();
        $response = $container->get(HttpServerResponse::class);
        $serverParams = $request->getServerParams();
        $ip = Arr::get($serverParams, 'remote_addr');
        $uri = Arr::get($serverParams, 'request_uri');
        AppLog::info("[IP: $ip] [URI: $uri] [Message: 签名验证失败]", $params); // 记录日志
        return $response->json([
            'code' => AppErrorCodeConstant::VALIDATION_ERROR,
            'message' => 'illegal request!',
            'result' => []
        ])->withStatus(403);
    }
}
