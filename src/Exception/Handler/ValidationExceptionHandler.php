<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LogLevel;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends AppExceptionHandler
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
        $message = ''; // 错误消息
        $context = []; // 日志内容
        if (isset($throwable->validator)) {
            $this->stopPropagation(); // 阻止异常冒泡
            $errors = $throwable->validator->errors(); // 错误信息
            $message = $errors->first(); // 首个错误信息
            $context['errors'] = $errors;
        }
        // 格式化输出
        return $this->responseHandle(
            [
                'result' => [],
                'code' => AppErrorCodeConstant::VALIDATION_ERROR,
                'message' => $message
            ],
            'Parameter validation failed!',
            LogLevel::WARNING,
            $context
        );
    }

    /**
     * 判断该异常处理器是否要对该异常进行处理
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
