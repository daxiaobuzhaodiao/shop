<?php

return [
    'alipay' => [
        'app_id'         => '2016092300575102',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtMTb0ZH1SJgWIg0Dge4u805g/5dlb1hzBCdIsEJPnBAN3q8/EU7gs154uOlYqvNkBU6Pysubc1EXkpdsDxmluwMA4Aw42i/k8gm7ELmyVFNoDsHYXTwtXQFW6Fm7f0x95POLokBWkED9WpasnI36GRtETxUhrHdbnW1X3i/yKHjNy8cOY1VgkeCIzluIDt8GSadkVXwHO0qNvP9boeV4cf2B0CZz4rqGRUINaxgr/Tskz0T0zeMEn6KHsTYIAKfW8Mieaz9ilY4AMGtyAsdbB6pTBKyK2EgmWNmYfUXkKII+rnIah/IsHdjQsxsvpFP5/pE+BWnmUntKWxFaqUYESwIDAQAB',
        'private_key'    => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCBqhRJFhKZ4eKYgf1KG+hwJBg1Hqp4YT7r6Ku9Cu3lMN26rCYrSnMbjS673AkXhSLm0Gy04IclqMsz2TZGGXj3zTKqgtrMydFGelCBgmW1QJXQDMTVdIPFJ2vXuvRq2oxYFQXsn1ja6M6cd0DtyxqfVNewZxWsJa4kJomZxky/afjpzQFN0LANpPlmJY7edmmfu0GNaAqvDfdprH9x25ovcWzsKLdJ+3ZI9PPhd1qBsMlIpva3nW//VVtP+GgLo2/sgP4/nxFZCppDkVQzGU9EP+PAsHWwxayGfgSJI/AXvL0vWxvISeTVluGOhthWAWtWMcvpc7Hgc0AC3rU35HqzAgMBAAECggEAFN17mi81GxIgVo5EZhKLmtAUHSgsImbzqx6r1CXYFlOAurNNCRZX440aghxsUh6YeS/87jZbSRIacON5OlqK/sVEa+/WqbGoPYEdR1nmZ/5WnT4QxpCTS7yGPBbXkgJNJzdq1qLlt4RTCxadmiZeyFjsxHQyJpuBpEuXROmGHB8D3EHDMv2bw4VAOn3SYYz2nZipPdfi+U3wzer5D9tjwNZcFdAkNqFusODkpu4r1vTJBAq67EdMUydII4vrfNuSI/Xs0almUSd7jszpK4vvpciuiCjSPpQXS1e/ZwXYvv5rOqryGwUQbNnEvLsFtm19NocQrf8SHG66OdpaEriqQQKBgQDZ9q0H9ZSkxxyUNbRmlo2O5bQF4lYTW3Kj74g8shKP4wHf7IzxfWlZu0cIpFRBhMxqbNPpi7Dsu68p6v70+Jx7oEcrToCJjJpVei/U4CLwOe3IYnVXtSLLpwASjdH+JXKAqPb1kXM2GqpLNeA0m4Az+Yw3iDIvjo7ZrboixgEtEQKBgQCYSreCYXzjMexdO8C1VONdQw07m4HNvVMheZCAb2B5hPG8xEad33cJnJgVSWZqqzeVppJ1I7a0oLpCzbGSUjZpQ6GOAxPAyCvMCbJ6aNTLdR76F2nPyOTojrq19MjAKcDiok2BrohWa9UyUqL81GaeuQd5XFvFhijKIrwYj2+7gwKBgFOMM30mjGixDepdv53HPdG1iNDsljPrTME8Esu5MlZHHLPZt5jlipljqPHOE6HdpoFw37XY3uOPebAmrL34Aft9T2pxN+IBrDLxmB4QIDRW8CdsXjBVdVX82JFEHWc+sWpR2f/9Vd06UWZs61ke9gB2OmIa6anDCky0Q/IKVe8RAoGAXSQFTBZ3nTRhTTxIRh7e16E0rKoVH1GVUDnbKNH1fRzUBBE+5ztOwHAni41SuBZzbnFrzXzEN4C9qjHHUqg5YvPcENDM+fDy6F1d7QPEcZL2GRrMuiox5hG1G6fUR8LBl1qQcKnf9IsK3zYvPNZi1NxxN8ZG0m2U8NuBkrvXKCUCgYBQLA86OAdfI144s12fkWcUJ3Tqx96/bcPznSTlrVcFMIhDFHdI2NQb78zdha3SG8UKwB5pQ6IC8LAgpa63RIlxry+Q9L8a5O1Dvyw6LvzwA90aafZWaKm4BYu4O7VhmMVvWCSjaUPvEUGMFyU0/QatUQfXvJhWSlzLNa8RzlLQJA==',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];