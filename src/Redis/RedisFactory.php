<?php

namespace Wmud\HyperfLib\Redis;

use Hyperf\Context\ApplicationContext;
use Hyperf\Redis\RedisProxy;
use Hyperf\Redis\RedisFactory as HyperfRedisFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class RedisFactory
{
    /**
     * @param string $poolName
     * @return RedisProxy
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function connection(string $poolName = 'default'): RedisProxy
    {
        return ApplicationContext::getContainer()->get(HyperfRedisFactory::class)->get($poolName);
    }
}