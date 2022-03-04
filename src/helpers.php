<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2021/11/13
 * Time: 5:09 PM
 */

if (!function_exists('msectime')) {
    /**
     * 取毫秒级时间戳，默认返回普通秒级时间戳 time() 及 3 位长度毫秒字符串
     *
     * @param int $msec_length 毫秒长度，默认 3
     *
     * @return string
     */
    function msectime(int $msec_length = 3): string {
        [$msec, $sec] = explode(' ', microtime());

        return sprintf('%.0f', (floatval($msec) + floatval($sec)) * pow(10, $msec_length));
    }
}

if (!function_exists('generateGuid')) {
    /**
     * @return string
     */
    function generateGuid(): string {
        mt_srand();

        return strtoupper(md5(uniqid(rand(), true)));
    }
}

if (!function_exists('generateContentMD5')) {
    /**
     * @param string $input
     *
     * @return string
     */
    function generateContentMD5(string $input): string {
        return base64_encode(md5(pack('C*', $input)));
    }
}
