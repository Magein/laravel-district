### 简介

laravel项目中使用的地区表

数据来源:

[Magein/china-district](https://github.com/Magein/china-district)

### 安装

```
composer magein/laravel-district:*@dev -vvv -o
```

### 驱动

> 默认使用的是文件驱动，即以src/files下的文件为准

使用redis驱动，在config/database.php中的redis参数中新增，

```php
'redis'=>[
    
    // 此处省略其他配置

    // 配置完成后会将src中的数据保存到redis中
    'district' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD1', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ]
]
```

### 使用

> 静态方法返回的是数组，并没有去重复，去除空值

```php

// 根据行政区划代码获取名称
\Magein\District\District::getName('110108')
\Magein\District\District::getName('110108', '110113', '110116')

// 获取所有的行政区划代码
\Magein\District\District::getCodes()
// 根据名称获取行政区划代码
\Magein\District\District::getCode('杭州','合肥')
\Magein\District\District::getCode('安徽省','合肥','杭州')

// 获取地址信息
$address=\Magein\District\District::getAddress(['province_id' => 340000, 'city_id' => 340100, 'district_id' => 340103])
$address=\Magein\District\District::getAddress([340000,340100,340103])
echo $address->toString()
echo $address->toString(' | ')

// 获取邮政编码
\Magein\District\District::getPostals()
\Magein\District\District::getPostal('杭州','合肥','330200')

// 获取固定电话区号
\Magein\District\District::getTels()
\Magein\District\District::getTel('杭州','合肥','330200')


```
