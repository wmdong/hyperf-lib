<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wmud\HyperfLib\Safety\AppSafety;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * 初始化中间件
 */
class InitializeMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): PsrResponseInterface
    {
        if (config('data_safety')) {
            $safetyWay = $request->getHeaderLine('Safety-Way'); // 安全方式
            $body = $request->getBody()->getContents(); // 请求体
            $appSafety = new AppSafety($safetyWay);
            if (!$decrypt = $appSafety->decrypt($body)) {
                $container = ApplicationContext::getContainer();
                $response = $container->get(ResponseInterface::class);
                return $response->json([
                    'code' => AppErrorCodeConstant::VALIDATION_ERROR,
                    'message' => '参数解密失败',
                    'result' => []
                ]);
            }
            $request = $request->withParsedBody($decrypt);
        }
        return $handler->handle($request);
    }
}
