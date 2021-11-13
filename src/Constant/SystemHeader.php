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

namespace AliCloud\ApiGateway\Constant;

/**
 * 系统 HTTP 头常量
 */
class SystemHeader {

    // 签名方法
    const X_CA_SIGNATURE_METHOD = 'x-ca-signature-method';

    // 签名 Header
    const X_CA_SIGNATURE = 'x-ca-signature';

    // 所有参与签名的 Header
    const X_CA_SIGNATURE_HEADERS = 'x-ca-signature-headers';

    // 请求时间戳
    const X_CA_TIMESTAMP = 'x-ca-timestamp';

    // 请求放重放 Nonce，15 分钟内保持唯一，建议使用 UUID
    const X_CA_NONCE = 'x-ca-nonce';

    // App Key
    const X_CA_KEY = 'x-ca-key';
}
