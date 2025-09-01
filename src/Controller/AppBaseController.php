<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Controller;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Wmud\HyperfLib\Response\AppResponse;

/**
 * 控制器基类
 */
abstract class AppBaseController
{
    /**
     * @var ContainerInterface
     */
    #[Inject]
    protected ContainerInterface $container;

    /**
     * @var RequestInterface
     */
    #[Inject]
    protected RequestInterface $request;

    /**
     * 成功响应
     * @param array $data 结果
     * @param string $message 消息
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function success(array $data, string $message = 'success'): ResponseInterface
    {
        $code = 0;
        $result = compact('data', 'message', 'code');
        return AppResponse::response($result);
    }

    /**
     * 失败响应
     * @param string $message 消息
     * @param int|null $code code
     * @param array $data 结果
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function failed(string $message = 'failed', ?int $code = null, array $data = []): ResponseInterface
    {
        if ($code === null) {
            $code = AppErrorCodeConstant::PROCESS;
        }
        $result = compact('data', 'message', 'code');
        return AppResponse::response($result);
    }

}
