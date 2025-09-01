<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Queue;

use Wmud\HyperfLib\Queue\Interface\QueueInterface;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 队列工厂
 */
class QueueFactory
{
    /**
     * @param string $queueClass
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function queue(string $queueClass): QueueInterface
    {
        return ApplicationContext::getContainer()->get($queueClass);
    }
}