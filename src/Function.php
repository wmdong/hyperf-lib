<?php

declare(strict_types=1);

/**
 * @param string $tel
 * @param int $start
 * @param int $len
 * @return array|string
 */
function hideMobile(string $tel, int $start = 3, int $len = 4): array|string
{
    return substr_replace($tel, '*****', $start, $len);
}

/**
 * 日期范围
 * @param string|array $date
 * @return array
 */
function dateToBetween(string|array $date): array
{
    $startDate = $endDate = $date;
    if (is_array($date)) {
        $startDate = $date[0];
        $endDate = end($date);
    }
    $startTime = strtotime(date('Y-m-d 00:00:00', strtotime($startDate)));
    $endTime = strtotime(date('Y-m-d 23:59:59', strtotime($endDate)));
    return [$startTime, $endTime];
}


/**
 * 密码强度验证
 * @param string $pass
 * @return bool
 */
function passStrengthVerify(string $pass): bool
{
    // 验证规则
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
    if ($strength > 1) return true;
    return false;
}

