<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2021/11/13
 * Time: 5:43 PM
 */

namespace AliCloudApiGatewayTest;

use Aliyun\ApiGateway\Http\HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class TestRequest extends TestCase {

    public function testHttpRequest() {
        try {
            $result = HttpClient::setKey('AppKey', 'AppSecret')
                ->execute('POST', 'https://demo.market.alicloudapi.com/gateway_api', [
                    'headers' => [
                        // 额外请求头键值对
                        'Accept' => 'application/json',
                        'User-Agent' => 'aliyun/api-gateway/php-sdk'
                    ],
                    'query' => [
                        // query 参数无需拼接在 URL 上，写在此处即可
                        'param1' => 'value1',
                        'param2' => 'value2'
                    ],
                    'body' => [
                        // 三选一，自动设置 Content-Type
                        'form' => [
                            // application/x-www-form-urlencoded
                            'formParam1' => 'value 1',
                            'formParam2' => 'value 2'
                        ],
                        // application/json
                        'json' => [
                            'name' => 'Max Sky',
                            'gender' => 'male'
                        ],
                        // application/text
                        'text' => 'contents'
                    ]
                ]);
        } catch (GuzzleException|ClientException|BadResponseException $e) {
            if ($e instanceof ClientException) {
                // show error message from AliCloud when request failed
                var_dump($e->getResponse()->getHeader('X-Ca-Error-Message')[0]);
                die;
            }

            if ($e instanceof BadResponseException) {
                // get failed data when gateway request success
                var_dump(json_decode($e->getResponse()->getBody(), true));
                die;
            }

            var_dump($e->getMessage());
            die;
        }

        var_dump(json_decode($result, true));

        $this->assertInstanceOf(StreamInterface::class, $result);
    }
}
