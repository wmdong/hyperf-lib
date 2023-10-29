<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Controller;

use Wmud\HyperfLib\Constants\AppErrorCodeConstant;
use Hyperf\Collection\Arr;
use Wmud\HyperfLib\Log\AppLog;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

abstract class AbstractController
{
    /**
     * @var ContainerInterface
     */
    #[Inject]
    protected ContainerInterface $container;

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
     * @Explan: 成功响应
     * @param array $result 结果
     * @param string $message 消息
     * @return PsrResponseInterface
     */
    protected function success(array $result, string $message = 'success'): PsrResponseInterface
    {
        return $this->responseHandle($result, $message);
    }

    /**
     * @Explan: 失败响应
     * @param string $message 消息
     * @param int $code code
     * @param array $result 结果
     * @param int $status http状态
     * @return PsrResponseInterface
     */
    protected function failed(string $message = 'failed', int $code = AppErrorCodeConstant::PROCESS_ERROR, array $result = [], int $status = 200): PsrResponseInterface
    {
        return $this->responseHandle($result, $message, $code, $status);
    }

    /**
     * @param array $result 数据
     * @param string $message 消息
     * @param int $code code
     * @param int $status http状态
     * @return PsrResponseInterface
     */
    protected function responseHandle(array $result, string $message, int $code = 0, int $status = 200): PsrResponseInterface
    {
        $response = compact('code', 'message', 'result');
        $serverParams = $this->request->getServerParams();
        $ip = Arr::get($serverParams, 'remote_addr');
        $uri = Arr::get($serverParams, 'request_uri');
        $startTime = Arr::get($serverParams, 'request_time_float');
        $endTime = microtime(true);
        $useTime = bcsub((string)$endTime, (string)$startTime, 6);
        AppLog::info("[IP: $ip] [URI: $uri] [UseTime: {$useTime}s]", [
            'params' => $this->request->all(),
            'response' => $response
        ]);
        return $this->response->json($response)->withStatus($status);
    }
}
