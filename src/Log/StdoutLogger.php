<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Log;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class StdoutLogger
{
    /**
     * @param ContainerInterface $container
     * @return LoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        return $container->get(LoggerFactory::class)->get(config('app_name'));
    }

    /**
     * @param string $name
     * @return LoggerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function get(string $name = 'app'): LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
    }
}