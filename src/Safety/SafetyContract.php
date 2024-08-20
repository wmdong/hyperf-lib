<?php

namespace Wmud\HyperfLib\Safety;

interface SafetyContract
{
    /**
     * 加密
     * @param array|string $data
     * @return string|false
     */
    public function encrypt(array|string $data): string|false;

    /**
     * 解密
     * @param string $encrypt
     * @return array|false
     */
    public function decrypt(string $encrypt): array|false;
}