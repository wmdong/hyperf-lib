<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LogLevel;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\HttpMessage\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends AppExceptionHandler
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
        $message = $throwable->getMessage(); // 异常消息
        // 格式化输出
        return $this->responseHandle(
            [
                'result' => [],
                'code' => AppErrorCodeConstant::HTTP_ERROR,
                'message' => $message
            ],
            $message,
            LogLevel::WARNING,
            [],
            404
        );
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
