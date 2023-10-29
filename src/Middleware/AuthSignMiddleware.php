<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Safety\AppSafety;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\Collection\Arr;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use function Hyperf\Config\config;

/**
 * 签名验证中间件
 */
class AuthSignMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var HttpResponse
     */
    protected HttpResponse $response;

    /**
     * @param ContainerInterface $container
     * @param HttpResponse $response
     */
    public function __construct(ContainerInterface $container, HttpResponse $response)
    {
        $this->container = $container;
        $this->response = $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (config('sign_auth')) {
            $timestamp = (int)$request->getHeaderLine('timestamp'); // 请求时间戳
            $diffTime = time() - $timestamp;
            if ($diffTime > 10 || $diffTime < 0) { // 签名时效验证（秒）
                return $this->responseHandel([
                    'message' => '签名已过期',
                    'timestamp' => $timestamp,
                    'diffTime' => $diffTime
                ], $request);
            }
            $body = $request->getBody()->getContents(); // 请求体
            $requestSign = $request->getHeaderLine('sign'); // 请求签名
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
     */
    private function responseHandel(array $params, ServerRequestInterface $request): ResponseInterface
    {
        $serverParams = $request->getServerParams();
        $ip = Arr::get($serverParams, 'remote_addr');
        $uri = Arr::get($serverParams, 'request_uri');
        AppLog::info("[IP: $ip] [URI: $uri] [Message: 签名验证失败]", $params); // 记录日志
        return $this->response->json([
            'code' => AppErrorCodeConstant::VALIDATION_ERROR,
            'message' => 'illegal request!',
            'result' => []
        ])->withStatus(403);
    }
}
