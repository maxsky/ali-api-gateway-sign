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
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Throwable;

class TestRequest extends TestCase {

    public function testHttpRequest() {
        try {
            $result = HttpClient::setKey('', '')
                ->execute('POST', 'https://test.market.alicloudapi.com/*', [
                    'headers' => [
                        //'x-ca-stage' => 'RELEASE',
                        //'x-ca-version' => 1
                    ],
                    'query' => [
                        'param1' => 'value1'
                    ],
                    'body' => [
                        'form' => [
                        ],
                        'json' => [
                            'param2' => 'value2'
                        ],
                        'text' => ''
                    ]
                ]);
        } catch (Throwable|BadResponseException $e) {
            var_dump(json_decode($e->getResponse()->getBody(), true));
            die;
        }

        print_r(json_decode($result, true));

        $this->assertInstanceOf(StreamInterface::class, $result);
    }
}
