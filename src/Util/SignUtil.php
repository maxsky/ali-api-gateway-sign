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

namespace AliCloud\ApiGateway\Util;

use AliCloud\ApiGateway\Constant\Constants;
use AliCloud\ApiGateway\Constant\HttpHeader;
use AliCloud\ApiGateway\Constant\SystemHeader;

/**
 * 签名处理
 */
class SignUtil {

    /**
     * @param string $app_secret
     * @param string $method
     * @param string $path
     * @param array  $query
     * @param array  $body
     * @param array  $headers
     * @param array  $ex_sign_headers
     *
     * @return string
     */
    public static function sign(string $app_secret, string $method,
                                string $path, array $query, array $body, array &$headers, array $ex_sign_headers): string {
        $signStr = strtoupper($method) . Constants::LF;
        $signStr .= self::buildHeaderStringToSign($headers, $ex_sign_headers)
            . self::buildResource($path, $query, $body);

        return base64_encode(hash_hmac('sha256', $signStr, $app_secret, true));
    }

    /**
     * @param array $headers
     * @param array $ex_sign_headers
     *
     * @return string
     */
    private static function buildHeaderStringToSign(array &$headers, array $ex_sign_headers = []): string {
        $signStr = $headers[HttpHeader::HTTP_HEADER_ACCEPT] . Constants::LF;
        $signStr .= $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] . Constants::LF;
        $signStr .= $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] . Constants::LF;
        $signStr .= $headers[HttpHeader::HTTP_HEADER_DATE] . Constants::LF;

        return $signStr . self::buildHeaders($headers, $ex_sign_headers);
    }

    /**
     * @param string $path
     * @param array  $query
     * @param array  $body
     *
     * @return string
     */
    private static function buildResource(string $path, array $query, array $body): string {
        $signStr = $path;
        $params = $query;

        if ($body['form'] ?? null) {
            $params = array_merge($params, $body['form']);
        }

        ksort($params);

        if ($params) {
            $signStr .= '?';

            foreach ($params as $param => $value) {
                if (is_array($value)) {
                    $value = current($value);
                }

                $signStr .= "$param=$value";
            }
        }

        return $signStr;
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
            $mergedHeaders[SystemHeader::X_CA_SIGNATURE_HEADERS]
        );

        ksort($mergedHeaders);

        if ($mergedHeaders) {
            $signHeadersStr = '';

            foreach ($mergedHeaders as $header => $value) {
                $signStr .= $header . Constants::SPE_COLON . $value . Constants::LF;

                $signHeadersStr .= $header . Constants::SPE_COMMA;
            }

            $headers[SystemHeader::X_CA_SIGNATURE_HEADERS] = $signHeadersStr;
        }

        return $signStr;
    }
}
