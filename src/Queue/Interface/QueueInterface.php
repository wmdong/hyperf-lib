<?php

namespace Wmud\HyperfLib\Queue\Interface;

interface QueueInterface
{
    /**
     * @param array $params
     * @param int $delay
     * @return bool
     */
    public function push(array $params, int $delay = 0): bool;
}