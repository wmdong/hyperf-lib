<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Logic;

use Hyperf\Context\Context;

/**
 * 逻辑层基类
 */
class AppBaseLogic
{
    /**
     * @var string|null
     */
    public ?string $requestId = null;

    /**
     * @var string|null
     */
    public ?string $message = null;

    /**
     * @var array
     */
    public array $params = [];

    /**
     * 初始化
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->requestId = Context::get('RequestId');
    }

}