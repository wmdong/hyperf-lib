<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Wmud\HyperfLib\Response\AppResponse;
use Throwable;

/**
 * 验证异常处理
 */
class ValidationExceptionHandler extends ExceptionHandler
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
        if ($throwable instanceof ValidationException) {
            if (isset($throwable->validator)) {
                $this->stopPropagation(); // 阻止异常冒泡
                $errors = $throwable->validator->errors(); // 错误信息
                return AppResponse::response([
                    'code' => AppErrorCodeConstant::VALIDATION,
                    'msg' => $errors->first(),
                    'data' => []
                ], 'warning');
            }
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
        return $throwable instanceof ValidationException;
    }
}
