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
use Aliyun\ApiGateway\Constant\ContentType;
use Aliyun\ApiGateway\Constant\HttpHeader;
use Aliyun\ApiGateway\Constant\SystemHeader;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Utils;

class HttpUtil {

    /**
     * @param array $headers
     * @param array $body
     */
    public static function preHandleHeaderAndBody(array &$headers, array &$body) {
        if (!($headers[HttpHeader::HTTP_HEADER_ACCEPT] ?? null)) {
            $headers[HttpHeader::HTTP_HEADER_ACCEPT] = '';
        }

        if (!($headers[HttpHeader::HTTP_HEADER_USER_AGENT] ?? null)) {
            $headers[HttpHeader::HTTP_HEADER_USER_AGENT] = Constants::USER_AGENT;
        }

        $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] = '';
        $headers[HttpHeader::HTTP_HEADER_DATE] = Carbon::now()->toRfc7231String() . '+00:00';

        if ($body['form'] ?? null) {
            $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] = ContentType::CONTENT_TYPE_FORM;
            $body = Utils::streamFor(http_build_query($body['form']));
        } elseif ($body['json'] ?? null) {
            $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] = ContentType::CONTENT_TYPE_JSON;
            $body = json_encode($body['json']);

            $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] = generateContentMD5($body);

            $body = Utils::streamFor($body);
        } elseif ($body['xml'] ?? null) {
            $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] = ContentType::CONTENT_TYPE_XML;
            $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] = generateContentMD5($body['xml']);
            $body = Utils::streamFor($body['xml']);
        } elseif ($body['stream'] ?? null) {
            $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] = ContentType::CONTENT_TYPE_STREAM;
            $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] = generateContentMD5($body['stream']);

            $body = Utils::streamFor($body['stream']);
        } else {
            $headers[HttpHeader::HTTP_HEADER_CONTENT_TYPE] = ContentType::CONTENT_TYPE_TEXT;
            $headers[HttpHeader::HTTP_HEADER_CONTENT_MD5] = generateContentMD5($body['text']);

            $body = Utils::streamFor($body['text']);
        }

        if (!$body) {
            $body = '';
        }
    }

    /**
     * @param string $app_key
     * @param string $app_secret
     * @param string $method
     * @param string $path
     * @param array  $query
     * @param array  $form
     * @param array  $headers
     * @param array  $ex_sign_headers
     */
    public static function buildSignHeader(string $app_key, string $app_secret, string $method,
                                           string $path, array $query, array $form, array &$headers, array $ex_sign_headers = []) {
        $headers[SystemHeader::X_CA_KEY] = $app_key;
        // 协议层不能进行重试，否则会报 NONCE 被使用；如果需要协议层重试，请注释此行
        $headers[SystemHeader::X_CA_NONCE] = generateGuid();
        $headers[SystemHeader::X_CA_TIMESTAMP] = msectime();
        $headers[SystemHeader::X_CA_SIGNATURE_METHOD] = Constants::HMAC_SHA256;
        $headers[SystemHeader::X_CA_SIGNATURE] =
            SignUtil::Sign($app_secret, $method, $path, $query, $form, $headers, $ex_sign_headers);
    }
}
