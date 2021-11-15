<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2021/11/13
 * Time: 5:43 PM
 */

namespace AliCloudApiGatewayTest;

use Aliyun\ApiGateway\Http\HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class TestRequest extends TestCase {

    public function testHttpRequest() {
        try {
            $result = HttpClient::setKey('appKey', 'appSecret')
                ->execute('POST', 'https://test.alicloudapi.com', [
                    'headers' => [
                    ],
                    'query' => [
                    ],
                    'body' => [
                        'form' => [
                        ],
                        'json' => [
                        ],
                        'text' => ''
                    ]
                ]);
        } catch (GuzzleException $e) {
            var_dump($e->getResponse()->getHeaders());
            $this->fail();
        }

        print_r(json_decode($result, true));

        $this->assertInstanceOf(StreamInterface::class, $result);
    }
}
