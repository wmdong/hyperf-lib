<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Hyperf\Collection\Arr;
use Hyperf\Context\ApplicationContext;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Log\LogLevel;
use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Wmud\HyperfLib\Exception\AppException;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Wmud\HyperfLib\Log\AppLog;

class AppExceptionHandler extends ExceptionHandler
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
                'code' => AppErrorCodeConstant::EXCEPTION_ERROR,
                'message' => $message
            ],
            $message,
            LogLevel::WARNING
        );
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

    /**
     * 响应处理
     * @param array $data 响应数据
     * @param string $message 日志错误消息
     * @param string $level 日志错误级别 warning error critical alert
     * @param array $context 日志内容
     * @param int $status http状态
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function responseHandle(array $data, string $message, string $level, array $context = [], int $status = 200): PsrResponseInterface
    {
        $container = ApplicationContext::getContainer();
        $request = $container->get(RequestInterface::class);
        $response = $container->get(\Hyperf\HttpServer\Contract\ResponseInterface::class);
        $params = $request->all();
        $serverParams = $request->getServerParams();
        $ip = Arr::get($serverParams, 'remote_addr');
        $uri = Arr::get($serverParams, 'request_uri');
        $context['params'] = $params;
        $context['response'] = $data;
        AppLog::$level("[IP: $ip] [URI: $uri] [Message: $message]", $context);
        return $response->json($data)->withStatus($status);
    }
}
