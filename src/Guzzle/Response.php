<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Guzzle;

/**
 * http响应
 */
class Response
{
    /**
     * 响应body
     * @var string
     */
    public string $responseBody = '';

    /**
     * 解析后的相应内容
     * @var array|null
     */
    public ?array $response = null;

    /**
     * 相应内容中的code
     * @var int|string|null
     */
    public int|string|null $code = null;

    /**
     * 相应内容中的data
     * @var string|array|null
     */
    public string|array|null $data = null;

    /**
     * 相应内容中的message（msg）
     * @var string|null
     */
    public ?string $message = null;

    /**
     * http状态
     * @var int|null
     */
    public ?int $httpStatus = null;

    /**
     * http错误
     * @var string|null
     */
    public ?string $httpError = null;
}