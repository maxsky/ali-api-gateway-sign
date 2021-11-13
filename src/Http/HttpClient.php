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

namespace AliCloud\ApiGateway\Http;

use AliCloud\ApiGateway\Util\HttpUtil;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\StreamInterface;

/**
 *httpClient对象
 */
class HttpClient {

    private static $instance;

    private static $appKey;
    private static $appSecret;

    /**
     * @param string $app_key
     * @param string $app_secret
     *
     * @return HttpClient
     */
    public static function setKey(string $app_key, string $app_secret): HttpClient {
        if (!self::$instance) {
            self::$instance = new self();
        }

        self::$appKey = $app_key;
        self::$appSecret = $app_secret;

        return self::$instance;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return StreamInterface|string
     */
    public function execute(string $method, string $url, array $options = []) {
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? [];
        $form = $body['form'] ?? [];

        HttpUtil::preHandleHeaderAndBody($headers, $body);

        $uri = (new Uri($url))->withQuery(http_build_query($options['query'] ?? []));

        HttpUtil::buildSignHeader(
            self::$appKey, self::$appSecret,
            $method, $uri->getPath(), Query::parse($uri->getQuery()), $form, $headers
        );

        $request = new Request($method, $url, $headers, $body);

        var_dump($request->getHeaders());die;

        try {
            return (new Client())->send($request)->getBody();
        } catch (GuzzleException $e) {
            return $e->getResponse()->getHeader('X-Ca-Error-Message')[0] ?? '请求错误';
        }
    }
}
