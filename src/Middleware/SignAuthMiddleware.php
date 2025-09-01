<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wmud\HyperfLib\Exception\AppException;
use Wmud\HyperfLib\Response\AppResponse;
use Wmud\HyperfLib\Safety\AppSafety;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 签名验证中间件
 */
class SignAuthMiddleware extends AppBaseMiddleware
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AppException
     */
    public function logic(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (config('safety.sign', false)) {
            $timestamp = (int)$request->getHeaderLine('Timestamp'); // 请求时间戳
            $diffTime = time() - $timestamp;
            $timeout = (int)config('safety.timeout', 10); // 签名失效时间
            if ($diffTime > $timeout || $diffTime < 0) { // 签名时效验证（秒）
                return AppResponse::response([
                    'code' => AppErrorCodeConstant::NO_PERMISSION,
                    'message' => 'Signature expiration',
                    'data' => [
                        'timestamp' => $timestamp,
                        'diffTime' => $diffTime
                    ],
                ], 'error', 403);
            }
            $authorization = $request->getHeaderLine('Authorization'); // 请求签名
            $appSafety = new AppSafety();
            $this->params['timestamp'] = $timestamp;
            $signature = $appSafety->signature($this->params);
            if ($signature !== $authorization) {
                return AppResponse::response([
                    'code' => AppErrorCodeConstant::NO_PERMISSION,
                    'message' => 'Signature error',
                    'data' => [
                        'signature' => $signature,
                        'authorization' => $authorization,
                        'timestamp' => $timestamp
                    ],
                ], 'error', 403);
            }
        }
        return $handler->handle($request);
    }
}
