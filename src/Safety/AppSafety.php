<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety;

class AppSafety
{

    /**
     * 密钥
     * @var string
     */
    protected static string $accessKey;

    /**
     * @var SafetyContract
     */
    public SafetyContract $way;

    /**
     * @param string|null $safetyWay
     */
    public function __construct(string $safetyWay = null)
    {
        if (!in_array($safetyWay, ['AES', 'RSA'])) {
            $safetyWay = 'RSA'; // 默认RSA
        }
        $this->way = SafetyFactory::instance($safetyWay);
        self::$accessKey = config('sign.access_key');
    }

    /**
     * 签名
     * @param string $body
     * @param int|string $timestamp
     * @return string
     */
    public function sign(string $body, int|string $timestamp): string
    {
        $accessKey = self::$accessKey;
        $signText = "body=$body&key=$accessKey&timestamp=$timestamp";
        return strtoupper(md5($signText));
    }

    /**
     * 数据加密
     * @param array|string $data
     * @return string|false
     */
    public function encrypt(array|string $data): string|false
    {
        return $this->way->encrypt($data);
    }

    /**
     * 数据解密
     * @param string $encrypt
     * @return array|false
     */
    public function decrypt(string $encrypt): array|false
    {
        return $this->way->encrypt($encrypt);
    }
}