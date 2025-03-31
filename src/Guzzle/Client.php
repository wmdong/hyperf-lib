<?php

namespace Wmud\HyperfLib\Guzzle;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Wmud\HyperfLib\Log\AppLog;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * HTTP客户端
 */
class Client
{
    /**
     * 选项
     * @var array
     */
    public array $options = [
        'verify' => false, // 不验证SSL
    ];

    /**
     * 请求头
     * @var array
     */
    public array $headers = [
        'Content-Type' => 'application/json; charset=utf-8'
    ];

    /**
     * 域名
     * @var string
     */
    private string $domain;

    /**
     * http客户端
     * @var GuzzleClient
     */
    protected GuzzleClient $http;

    /**
     * @param string $domain
     * @param int|float $timeout
     */
    public function __construct(string $domain = '', int|float $timeout = 10)
    {
        $this->domain = $domain;
        $this->http = new GuzzleClient([
            'base_uri' => $this->domain,
            RequestOptions::TIMEOUT => $timeout,
            RequestOptions::CONNECT_TIMEOUT => $timeout,
            RequestOptions::HTTP_ERRORS => false, // 禁用4xx和5xx抛出异常
        ]);
    }

    /**
     * 请求
     * @param string $method
     * @param string $uri
     * @return Response
     */
    public function request(string $method, string $uri): Response
    {
        $sTime = microtime(true);
        $response = new Response();
        try {
            $this->options['headers'] = $this->headers;
            $res = $this->http->request($method, $uri, $this->options);
        } catch (RequestException $exception) {
            $response->httpError = $exception->getMessage();
            $res = $exception->getResponse();
        } catch (GuzzleException $exception) {
            $response->httpError = $exception->getMessage();
        }
        if (isset($res) && $res instanceof ResponseInterface) {
            $response->httpStatus = $res->getStatusCode();
            $response->responseBody = $res->getBody()->getContents();
            if ($parse = json_decode($response->responseBody, true)) {
                $response->code = $parse['code'] ?? null;
                $response->data = $parse['data'] ?? null;
                $response->message = $parse['message'] ?? ($parse['msg'] ?? null);
            }
        }
        $eTime = microtime(true);
        $useTime = round($eTime - $sTime, 3);
        AppLog::info("请求日志", [
            'url' => "$this->domain$uri",
            'useTime' => "{$useTime}s",
            'httpStatus' => $response->httpStatus,
            'options' => $this->options,
            'responseBody' => $response->responseBody
        ], 'Http');
        return $response;
    }

    /**
     * @param string $uri
     * @return Response
     */
    public function get(string $uri): Response
    {
        return $this->request('GET', $uri);
    }

    /**
     * @param string $uri
     * @param mixed $params
     * @return Response
     */
    public function post(string $uri, mixed $params = null): Response
    {
        if ($params) {
            $this->options['body'] = $this->bodyFormat($params);
        }
        return $this->request('POST', $uri);
    }

    /**
     * body格式化
     * @param array $params
     * @return string
     */
    private function bodyFormat(mixed $params): mixed
    {
        if (is_array($params)) {
            if (str_contains($this->headers['Content-Type'], 'application/json')) {
                return $this->bodyJson($params);
            }
            if (str_contains($this->headers['Content-Type'], 'application/x-www-form-urlencoded')) {
                return $this->bodyUrlencoded($params);
            }
        }
        return $params;
    }

    /**
     * json
     * @param array $params
     * @return string
     */
    private function bodyJson(array $params): string
    {
        return json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * 表单
     * @param array $params
     * @return string
     */
    private function bodyUrlencoded(array $params): string
    {
        $body = '';
        foreach ($params as $key => $val) {
            $body .= "&$key=$val";
        }
        return substr($body, 1);
    }

}