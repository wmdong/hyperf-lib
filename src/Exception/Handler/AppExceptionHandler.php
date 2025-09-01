<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Wmud\HyperfLib\Exception\AppException;
use Wmud\HyperfLib\Response\AppResponse;
use Throwable;

/**
 * 主动异常处理
 */
class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws AppException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        if ($throwable instanceof AppException) {
            $this->stopPropagation(); // 阻止异常冒泡
            return AppResponse::response([
                'code' => $throwable->getCode(),
                'msg' => $throwable->getMessage(),
                'data' => []
            ], 'warning');
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
        return $throwable instanceof AppException;
    }
}
