<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Hyperf\Context\Context;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Wmud\HyperfLib\Exception\AppException;
use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Response\AppResponse;
use Throwable;

/**
 * 全局异常处理
 */
class SystemExceptionHandler extends ExceptionHandler
{
    /**
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AppException
     */
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        AppLog::error('System Exception', [
            'message' => $throwable->getMessage(),
            'line' => $throwable->getLine(),
            'file' => $throwable->getFile(),
        ]);
        $this->stopPropagation(); // 阻止异常冒泡
        return AppResponse::response([
            'code' => AppErrorCodeConstant::SYSTEM,
            'message' => 'Internal Server Error!',
            'data' => [],
        ], 'error', 500);
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
