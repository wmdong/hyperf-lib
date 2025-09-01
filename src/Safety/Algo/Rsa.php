<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety\Algo;

use Wmud\HyperfLib\Exception\AppException;

class Rsa
{
    /**
     * 公钥 需要去掉头部、尾部和换行符
     * @var string|null
     */
    public ?string $publicKey = null;

    /**
     * 私钥 需要去掉头部、尾部和换行符
     * @var string|null
     */
    public ?string $privateKey = null;

    /**
     * @param string $publicKey
     * @param string $privateKey
     */
    public function __construct(string $publicKey, string $privateKey)
    {
        $this->publicKey = $this->publicKeyFormat($publicKey);
        $this->privateKey = $this->privateKeyFormat($privateKey);
    }

    /**
     * 公钥格式化
     * @param string $publicKey
     * @return string
     */
    private function publicKeyFormat(string $publicKey): string
    {
        return "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split($publicKey, 64) .
            "-----END PUBLIC KEY-----";
    }

    /**
     * 私钥格式化
     * @param string $privateKey
     * @return string
     */
    private function privateKeyFormat(string $privateKey): string
    {
        return "-----BEGIN RSA PRIVATE KEY-----\n" .
            chunk_split($privateKey, 64) .
            "-----END RSA PRIVATE KEY-----";
    }

    /**
     * 数据加密
     * @param array|string $data
     * @return string
     * @throws AppException
     */
    public function encrypt(array|string $data): string
    {
        if (is_array($data)) {
            $data = json_encode_256_64($data);
        }
        if (!$key = openssl_pkey_get_public($this->publicKey)) {
            throw new AppException("RSA openssl_pkey_get_public fail");
        }
        if (!openssl_public_encrypt($data, $ciphertext, $key)) {
            throw new AppException("RSA Encrypt fail");
        }
        return base64_encode($ciphertext);
    }

    /**
     * 数据解密
     * @param string $data
     * @return string
     * @throws AppException
     */
    public function decrypt(string $data): string
    {
        $ciphertext = base64_decode($data);
        if (!$key = openssl_pkey_get_private($this->privateKey)) {
            throw new AppException("RSA openssl_pkey_get_private fail");
        }
        if (!openssl_private_decrypt($ciphertext, $plaintext, $key)) {
            throw new AppException("RSA Decrypt fail");
        }
        return $plaintext;
    }
}