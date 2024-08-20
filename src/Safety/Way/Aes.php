<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety\Way;

use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Safety\SafetyContract;

class Aes implements SafetyContract
{
    /**
     * 加密算法
     * @var string|null
     */
    public string|null $cipherAlgo;

    /**
     * 密钥
     * @var string
     */
    protected static string $accessKey;

    /**
     * 初始化
     */
    public function __construct()
    {
        self::$accessKey = config('aes.access_key');
        $this->cipherAlgo = config('aes.cipher_algo', 'AES-128-CBC');
    }

    /**
     * 数据加密
     * @param array|string $data
     * @return string|false
     */
    public function encrypt(array|string $data): string|false
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $ivLen = openssl_cipher_iv_length($this->cipherAlgo); // 初始化向量长度
        $iv = openssl_random_pseudo_bytes($ivLen); // 初始化向量
        // 数据加密
        if (!$encrypt = openssl_encrypt($data, $this->cipherAlgo, self::$accessKey, 1, $iv)) {
            AppLog::error('AES数据加密失败', ['data' => $data]);
            return false;
        }
        return base64_encode("$iv$encrypt");
    }

    /**
     * 数据解密
     * @param string $encrypt
     * @return array|false
     */
    public function decrypt(string $encrypt): array|false
    {
        $base64Decode = base64_decode($encrypt);
        $ivLen = openssl_cipher_iv_length($this->cipherAlgo); // 初始化向量长度
        $iv = substr($base64Decode, 0, $ivLen); // 初始化向量
        if (!$decrypt = openssl_decrypt($base64Decode, $this->cipherAlgo, self::$accessKey, 1, $iv)) {
            AppLog::error('AES数据解密失败', ['encrypt' => $encrypt]);
            return false;
        }
        return json_decode($decrypt, true);
    }
}