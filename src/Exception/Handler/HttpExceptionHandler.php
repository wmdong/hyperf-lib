<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\HttpMessage\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends BasisExceptionHandler
{
    /**
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
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
                'message' => 'Http exception!'
            ],
            $message,
            'critical',
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
