<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\HttpException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Wmud\HyperfLib\Response\AppResponse;
use Throwable;

/**
 * http异常处理
 */
class HttpExceptionHandler extends ExceptionHandler
{
    /**
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof HttpException) {
            $this->stopPropagation(); // 阻止异常冒泡
            return AppResponse::response([
                'code' => AppErrorCodeConstant::HTTP,
                'msg' => $throwable->getMessage(),
                'data' => []
            ], 'warning', 404);
        }
        return $response;
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}
