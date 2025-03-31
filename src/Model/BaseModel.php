<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * 模型基类
 */
abstract class BaseModel extends Model
{
    /**
     * 创建时间
     */
    public const CREATED_AT = 'createdTime';

    /**
     * 更新时间
     */
    public const UPDATED_AT = 'updatedTime';

    /**
     * 是否自动维护时间戳
     * @var bool
     */
    public bool $timestamps = false;

    /**
     * 消息
     * @var string|null
     */
    public string|null $message = null;
}
