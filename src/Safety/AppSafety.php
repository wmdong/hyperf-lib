<?php

declare(strict_types=1);

namespace Wmud\HyperfLib\Safety;

use Wmud\HyperfLib\Exception\AppException;
use Wmud\HyperfLib\Safety\Algo\Aes;
use Wmud\HyperfLib\Safety\Algo\Rsa;

/**
 * 应用安全
 * 采用 RSA+AES混合加密
 */
class AppSafety
{

    /**
     * AES 密钥
     * @var string|null
     */
    protected ?string $accessKey = null;

    /**
     * RSA 公钥
     * @var string|null
     */
    protected ?string $publicKey = null;

    /**
     * RSA 私钥
     * @var string|null
     */
    protected ?string $privateKey = null;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->accessKey = config('safety.access_key');
        $this->publicKey = config('safety.public_key');
        $this->privateKey = config('safety.private_key');
    }

    /**
     * 签名
     * @param array $params
     * @param array $ignore
     * @return string
     * @throws AppException
     */
    public function signature(array $params, array $ignore = []): string
    {
        $ignore['cipheriv'] = 1;
        $ignore['ciphertext'] = 1;
        ksort($params);
        $text = "accessKey=$this->accessKey";
        foreach ($params as $key => $val) {
            if (!isset($ignore[$key]) && (!empty($val) || $val == 0)) {
                $text .= "&$key=$val";
            }
        }
        if (config('safety.cipher')) {
            $rsa = new Rsa($this->publicKey, $this->privateKey);
            $iv = $rsa->decrypt($params['cipheriv']);
            $aes = new Aes($this->accessKey);
            $cipherData = $aes->encrypt($text, $iv);
            $text = $cipherData['ciphertext'];
        }
        return strtoupper(md5($text));
    }

    /**
     * 数据加密
     * @param array|string $data
     * @return array
     * @throws AppException
     */
    public function encrypt(array|string $data): array
    {
        $aes = new Aes($this->accessKey);
        ['ciphertext' => $ciphertext, 'iv' => $iv] = $aes->encrypt($data);
        $rsa = new Rsa($this->publicKey, $this->privateKey);
        $cipheriv = $rsa->encrypt($iv);
        return [
            'ciphertext' => $ciphertext,
            'cipheriv' => $cipheriv,
        ];
    }

    /**
     * 数据解密
     * @param string $ciphertext
     * @param string $cipheriv
     * @return array|false
     * @throws AppException
     */
    public function decrypt(string $ciphertext, string $cipheriv): array|false
    {
        $rsa = new Rsa($this->publicKey, $this->privateKey);
        $iv = $rsa->decrypt($cipheriv);
        $aes = new Aes($this->accessKey);
        $plaintext = $aes->decrypt($ciphertext, $iv);
        return json_decode($plaintext, true);
    }
}