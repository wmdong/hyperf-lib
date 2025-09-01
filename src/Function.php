<?php

declare(strict_types=1);

/**
 * @param string $str
 * @param int $start
 * @param int $len
 * @return array|string
 */
function str_hide(string $str, int $start = 3, int $len = 4): array|string
{
    return substr_replace($str, '*****', $start, $len);
}

/**
 * 日期范围
 * @param string|array $date
 * @return array
 */
function date_between(string|array $date): array
{
    if (is_array($date)) {
        $startDate = $date[0];
        $endDate = $date[1];
    } else {
        $startDate = $endDate = $date;
    }
    $startTime = strtotime(date('Y-m-d 00:00:00', strtotime($startDate)));
    $endTime = strtotime(date('Y-m-d 23:59:59', strtotime($endDate)));
    return [$startTime, $endTime];
}


/**
 * 密码强度验证(0-1:弱,2:中,3:强)
 * @param string $pass
 * @return int
 */
function pwd_strength(string $pass): int
{
    $regs = [
        '/\d/', // 弱
        '/[a-z | A-Z]/', // 中
        '/\W/' // 强
    ];
    $strength = 0;
    foreach ($regs as $reg) {
        if (preg_match($reg, $pass)) {
            $strength++;
        }
    }
    return $strength;
}

/**
 * json序列化 256|64
 * @param mixed $data
 * @return string|false
 */
function json_encode_256_64(mixed $data): string|false
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}