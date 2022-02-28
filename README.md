# aliyun-api-gateway-sdk

[![996.icu](https://img.shields.io/badge/link-996.icu-red.svg)](https://996.icu)

## 要求

PHP >= 7.1

## 安装

```shell
composer require maxsky/aliyun-api-gateway-sdk
```

## 使用

```php
use Aliyun\ApiGateway\Http;

try {
    $result = HttpClient::setKey('appKey', 'appSecret')
        ->execute('POST', 'https://test.alicloudapi.com', [
            'headers' => [
                // 额外请求头键值对
                'Accept' => 'application/json; charset=utf-8',
                'User-Agent' => 'aliyun/api-gateway/php-sdk'
            ],
            'query' => [
                //  query 参数无需拼接在 URL 上，写在此处即可
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
                'json' => [
                    // application/json
                    'name' = 'Max Sky',
                    'gender' => 'male'
                ],
                'text' => 'Message' // application/text
            ]
        ]);
} catch (GuzzleException $e) {
    var_dump($e->getResponse()->getHeaders());
    $this->fail();
}

var_dump(json_decode($result, true));
```
