<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety\Algo;

use Wmud\HyperfLib\Log\AppLog;
use Wmud\HyperfLib\Exception\AppException;

class Aes
{

    /**
     * 密钥
     * @var string|null
     */
    protected ?string $accessKey = null;

    /**
     * 加密算法
     * @var string|null
     */
    public ?string $cipherAlgo = null;

    /**
     * @param string $accessKey
     * @param string $algo
     */
    public function __construct(string $accessKey, string $algo = 'AES-256-CBC')
    {
        $this->accessKey = $accessKey;
        $this->cipherAlgo = $algo;
    }

    /**
     * 数据加密
     * @param array|string $data
     * @param string|null $iv
     * @return array
     * @throws AppException
     */
    public function encrypt(array|string $data, ?string $iv = null): array
    {
        if (is_array($data)) {
            $data = json_encode_256_64($data);
        }
        if ($iv === null) {
            $ivLen = openssl_cipher_iv_length($this->cipherAlgo); // 初始化向量长度
            $iv = openssl_random_pseudo_bytes($ivLen); // 初始化向量
        }
        // 数据加密
        $ciphertext = openssl_encrypt(
            $data,
            $this->cipherAlgo,
            $this->accessKey,
            OPENSSL_RAW_DATA,
            $iv
        );
        if ($ciphertext === false) {
            $message = "AES Encrypt fail";
            AppLog::error($message, ['data' => $data]);
            throw new AppException($message);
        }
        return [
            'ciphertext' => base64_encode($ciphertext),
            'iv' => $iv,
        ];
    }

    /**
     * 数据解密
     * @param string $data
     * @param string $iv
     * @return string
     * @throws AppException
     */
    public function decrypt(string $data, string $iv): string
    {
        $ciphertext = base64_decode($data);
        $plaintext = openssl_decrypt(
            $ciphertext,
            $this->cipherAlgo,
            $this->accessKey,
            OPENSSL_RAW_DATA,
            $iv
        );
        if ($plaintext === false) {
            $message = "AES Decrypt fail";
            AppLog::error($message, ['data' => $data]);
            throw new AppException($message);
        }
        return $plaintext;
    }
}