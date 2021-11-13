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
     * @param array  $headers
     * @param array  $query
     * @param array  $body ['form_params' => []] or ['json' => []]
     *
     * @return StreamInterface
     */
    public function execute(string $method,
                            string $url, array $headers = [], array $query = [], array $body = []): StreamInterface {
        $query = Query::build($query);

        if ($query) {
            $url = "$url?$query";
        }

        $originBody = $body;

        HttpUtil::preHandleHeaderAndBody($headers, $body);

        $request = new Request($method, $url, $headers, $body);

        $uri = $request->getUri();

        HttpUtil::buildSignHeader(
            self::$appKey, self::$appSecret,
            $request->getMethod(), $uri->getPath(), Query::parse($uri->getQuery()), $originBody, $headers
        );

        $request = new Request($method, $url, $headers, $body);

        try {
            return (new Client())->send($request)->getBody();
        } catch (GuzzleException $e) {

            var_dump($e->getMessage());
        }
    }
}
