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
    $result = HttpClient::setKey('App Key', 'App Secret')
        ->execute('POST', 'https://demo.market.alicloudapi.com/gateway_api', [
            'headers' => [
                // 额外请求头键值对，“X-Ca-”开头的请求头会自动加入签名计算
                'x-ca-header1' => 'value1',
                'x-ca-header2' => 'value2'
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
        // 请求失败时显示来自阿里云的错误消息
        var_dump($e->getResponse()->getHeader('X-Ca-Error-Message'));
        die;
    }

    if ($e instanceof BadResponseException) {
        // 请求成功但参数错误之类的响应可通过捕获该异常得到
        var_dump(json_decode($e->getResponse()->getBody(), true));
        die;
    }

    var_dump($e->getMessage());
    die;
}

// 请求成功获取响应结果
var_dump(json_decode($result, true));

```
