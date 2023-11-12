<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Wmud\HyperfLib\Log\AppLog;
use Hyperf\Collection\Arr;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Throwable;

class BasisExceptionHandler extends ExceptionHandler
{
    /**
     * @param Throwable $throwable
     * @param PsrResponseInterface $response
     * @return PsrResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Throwable $throwable, PsrResponseInterface $response): PsrResponseInterface
    {
        return $this->responseHandle([], 'BaseExceptionHandle', 'error');
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
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
        $response = $container->get(ResponseInterface::class);
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
