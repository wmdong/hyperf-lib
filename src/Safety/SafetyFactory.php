<?php

namespace Wmud\HyperfLib\Safety;

use Wmud\HyperfLib\Safety\Way\Aes;
use Wmud\HyperfLib\Safety\Way\Rsa;

class SafetyFactory
{
    /**
     * 安全方式
     */
    public const WAY = [
        'AES' => Aes::class,
        'RSA' => Rsa::class,
    ];

    /**
     * 安全方式实例
     * @param string $safetyWay
     * @return SafetyContract
     */
    public static function instance(string $safetyWay): SafetyContract
    {
        return new (self::WAY[$safetyWay])();
    }
}