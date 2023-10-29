<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Exception\Handler;

use Wmud\HyperfLib\Log\AppLog;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Throwable;

class BasisExceptionHandler extends ExceptionHandler
{
    /**
     * @var RequestInterface
     */
    #[Inject]
    protected RequestInterface $request;

    /**
     * @var ResponseInterface
     */
    #[Inject]
    protected ResponseInterface $response;

    /**
     * @param Throwable $throwable
     * @param PsrResponseInterface $response
     * @return PsrResponseInterface
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
     */
    protected function responseHandle(array $data, string $message, string $level, array $context = [], int $status = 200): PsrResponseInterface
    {
        // 日志信息
        $params = $this->request->all();
        $serverParams = $this->request->getServerParams();
        $ip = Arr::get($serverParams, 'remote_addr');
        $uri = Arr::get($serverParams, 'request_uri');
        $context['params'] = $params;
        $context['response'] = $data;
        AppLog::$level("[IP: $ip] [URI: $uri] [Message: $message]", $context);
        return $this->response->json($data)->withStatus($status);
    }
}
