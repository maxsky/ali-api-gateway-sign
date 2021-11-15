<?php

/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

namespace Aliyun\ApiGateway\Util;

use Aliyun\ApiGateway\Constant\Constants;
use Aliyun\ApiGateway\Constant\HttpHeader;
use Aliyun\ApiGateway\Constant\SystemHeader;

/**
 * 签名处理
 */
class SignUtil {

    /**
     * @param string $app_secret
     * @param string $method
     * @param string $path
     * @param array  $query
     * @param array  $form
     * @param array  $headers
     * @param array  $ex_sign_headers
     *
     * @return string
     */
    public static function sign(string $app_secret, string $method,
                                string $path, array $query, array $form, array &$headers, array $ex_sign_headers): string {
        $signStr = $method . Constants::LF;
        $signStr .= self::buildHeaderStringToSign($headers, $ex_sign_headers)
            . self::buildPathAndParameters($path, $query, $form);

        return base64_encode(hash_hmac('sha256', $signStr, $app_secret, true));
    }

    /**
     * @param array $headers
     * @param array $ex_sign_headers
     *
     * @return string
     */
    private static function buildHeaderStringToSign(array &$headers, array $ex_sign_headers = []): string {
        $signStr = '';

        if ($headers[HttpHeader::HTTP_HEADER_ACCEPT] ?? null) {
            $signStr .= $headers[HttpHeader::HTTP_HEADER_ACCEPT];
        }

        $signStr .= Constants::LF;

        if ($headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] ?? null) {
            $signStr .= $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5];
        }

        $signStr .= Constants::LF;

        if ($headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] ?? null) {
            $signStr .= $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE];
        }

        $signStr .= Constants::LF;

        if ($headers[HttpHeader::HTTP_HEADER_DATE] ?? null) {
            $signStr .= $headers[HttpHeader::HTTP_HEADER_DATE];
        }

        $signStr .= Constants::LF;

        return $signStr . self::buildHeaders($headers, $ex_sign_headers);
    }

    /**
     * @param array $headers
     * @param array $ex_sign_headers
     *
     * @return string
     */
    private static function buildHeaders(array &$headers, array $ex_sign_headers = []): string {
        $signStr = '';

        $mergedHeaders = array_merge($headers, $ex_sign_headers);

        unset(
            $mergedHeaders[HttpHeader::HTTP_HEADER_ACCEPT],
            $mergedHeaders[HttpHeader::HTTP_HEADER_CONTENT_MD5],
            $mergedHeaders[HttpHeader::HTTP_HEADER_CONTENT_TYPE],
            $mergedHeaders[HttpHeader::HTTP_HEADER_DATE],
            $mergedHeaders[SystemHeader::X_CA_SIGNATURE],
            $mergedHeaders[SystemHeader::X_CA_SIGNATURE_HEADERS],
        );

        if ($mergedHeaders) {
            ksort($mergedHeaders);

            foreach ($mergedHeaders as $header => $value) {
                if (stripos($header, 'x-ca-') !== false) {
                    $signStr .= $header . Constants::SPE_COLON . $value . Constants::LF;
                } else {
                    unset($mergedHeaders[$header]);
                }
            }

            $headers[SystemHeader::X_CA_SIGNATURE_HEADERS] = implode(',', array_keys($mergedHeaders));
        }

        return $signStr;
    }

    /**
     * @param string $path
     * @param array  $query
     * @param array  $form
     *
     * @return string
     */
    private static function buildPathAndParameters(string $path, array $query, array $form): string {
        $signStr = $path;
        $params = $query;

        if ($form) {
            $params = array_merge($params, $form);
        }

        ksort($params);

        if ($params) {
            $signStr .= '?';

            foreach ($params as $param => $value) {
                if (is_array($value)) {
                    $value = current($value);
                }

                if ($value === '') {
                    $signStr .= "$param&";
                } else {
                    $signStr .= "$param=$value&";
                }
            }

            $signStr = rtrim($signStr, '&');
        }

        return $signStr;
    }
}
