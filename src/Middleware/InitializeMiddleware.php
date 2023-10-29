<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Middleware;

use Wmud\HyperfLib\Safety\AppSafety;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use function Hyperf\Config\config;

/**
 * 初始化中间件
 */
class InitializeMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseInterface
     */
    #[Inject]
    protected ResponseInterface $response;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return PsrResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): PsrResponseInterface
    {
        if (config('data_safety')) {
            $body = $request->getBody()->getContents(); // 请求体
            $appSafety = new AppSafety();
            if (!$decrypt = $appSafety->decrypt($body)) {
                return $this->response->json([
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
