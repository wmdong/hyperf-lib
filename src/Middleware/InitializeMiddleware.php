<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Hyperf\Collection\Arr;
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
 * 初始化中间件
 */
class InitializeMiddleware extends AppBaseMiddleware
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
        $ciphertext = Arr::get($this->params, 'ciphertext', '');
        $cipheriv = Arr::get($this->params, 'cipheriv', '');
        if (config('safety.cipher') && $ciphertext && $cipheriv) {
            $appSafety = new AppSafety();
            if (!$data = $appSafety->decrypt($ciphertext, $cipheriv)) {
                return AppResponse::response([
                    'code' => AppErrorCodeConstant::VALIDATION,
                    'message' => 'Parameter error',
                    'data' => []
                ]);
            }
            $request = $request->withParsedBody($data);
        }
        return $handler->handle($request);
    }
}
