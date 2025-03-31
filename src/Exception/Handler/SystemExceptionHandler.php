<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LogLevel;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class SystemExceptionHandler extends AppExceptionHandler
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
        $this->stopPropagation(); // 阻止异常冒泡
        // 格式化输出
        return $this->responseHandle(
            [
                'result' => [],
                'code' => AppErrorCodeConstant::SYSTEM_ERROR,
                'message' => 'Internal Server Error!'
            ],
            '系统异常',
            LogLevel::ERROR,
            [
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'message' => $throwable->getMessage(),
                'trace' => $throwable->getTrace()
            ],
            500
        );
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
