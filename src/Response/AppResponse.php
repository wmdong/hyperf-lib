<?php

namespace Wmud\HyperfLib\Response;

use Hyperf\Collection\Arr;
use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Wmud\HyperfLib\Exception\AppException;
use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Safety\AppSafety;

/**
 * 接口响应类
 */
class AppResponse
{

    /**
     * @param mixed $data
     * @param string $level
     * @param int $status
     * @return PsrResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws AppException
     */
    public static function response(mixed $data = [], string $level = 'info', int $status = 200): PsrResponse
    {
        $container = ApplicationContext::getContainer();
        $request = $container->get(RequestInterface::class);
        $response = $container->get(ResponseInterface::class);
        $params = $request->all();
        $uri = $request->getUri()->getPath();
        $serverParams = $request->getServerParams();
        $remoteAddr = Arr::get($serverParams, 'remote_addr');
        $realIp = $request->getHeaderLine('X-Real-IP');
        $ip = $realIp ?: $remoteAddr;
        $sTime = Arr::get($serverParams, 'request_time_float');
        $eTime = microtime(true);
        $useTime = round(($eTime - $sTime), 3);
        AppLog::{$level}("Api Response $uri $ip $useTime", [
            'params' => $params,
            'response' => $data
        ]);
        if (config('safety.cipher')) {
            $safety = new AppSafety();
            $data = $safety->encrypt($data);
        }
        return $response->json($data)->withStatus($status);
    }
}