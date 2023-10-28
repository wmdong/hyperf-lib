<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety;

use Wmud\HyperfLib\Log\AppLog;
use function Hyperf\Config\config;

class AppSafety
{
    /**
     * @var string
     */
    protected static string $accessKey;

    /**
     * @var string
     */
    protected static string $accessIV;

    public function __construct()
    {
        self::$accessKey = config('access_key', '');
        self::$accessIV = config('access_iv', '');
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
     * @param array $data
     * @return string|false
     */
    public function encrypt(array $data): string|false
    {
        $encrypt = openssl_encrypt(
            json_encode($data),
            'AES-128-CBC',
            self::$accessKey,
            OPENSSL_RAW_DATA,
            self::$accessIV
        );
        if (!$encrypt) {
            AppLog::error('数据解密失败', ['data' => $data]);
            return false;
        }
        return $encrypt;
    }

    /**
     * 数据解密
     * @param string $encrypt
     * @return array|false
     */
    public function decrypt(string $encrypt): array|false
    {
        $decrypt = openssl_decrypt(
            $encrypt,
            'AES-128-CBC',
            self::$accessKey,
            OPENSSL_RAW_DATA,
            self::$accessIV
        );
        if (!$decrypt) {
            AppLog::error('数据解密失败', ['encrypt' => $encrypt]);
            return false;
        }
        return json_decode($decrypt, true);
    }
}