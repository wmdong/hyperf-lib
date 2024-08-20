<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety\Way;

use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Safety\SafetyContract;

class Rsa implements SafetyContract
{
    /**
     * 公钥
     * @var string|null
     */
    public string|null $publicKey;

    /**
     * 私钥
     * @var string|null
     */
    public string|null $privateKey;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->publicKey = config('rsa.public_key'); // 需要去掉头部、尾部和换行符
        $this->privateKey = config('rsa.private_key'); // 需要去掉头部、尾部和换行符
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
        // 格式化
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($this->publicKey, 64, "\n") . "-----END PUBLIC KEY-----\n";
        // 加载公钥
        $resource = openssl_get_publickey($publicKey);
        // 使用公钥加密数据
        if (!openssl_public_encrypt($data, $encrypt, $resource)) {
            AppLog::error("RSA数据加密失败", ['data' => $data]);
            return false;
        }
        return base64_encode($encrypt);
    }

    /**
     * 数据解密
     * @param string $encrypt
     * @return array|false
     */
    public function decrypt(string $encrypt): array|false
    {
        $encrypt = base64_decode($encrypt);
        // 格式化
        $privateKey = "-----BEGIN PRIVATE KEY-----\n" . chunk_split($this->privateKey, 64, "\n") . "-----END PRIVATE KEY-----\n";
        // 加载私钥
        $resource = openssl_get_privatekey($privateKey);
        // 使用私钥解密数据
        if (!openssl_private_decrypt($encrypt, $decrypt, $resource)) {
            AppLog::error('RSA数据解密失败', ['encrypt' => $encrypt]);
            return false;
        }
        return json_decode($decrypt, true);
    }
}