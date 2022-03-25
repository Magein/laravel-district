### 简介

> laravel项目中使用的地区表

### 安装

```
composer magein/laravel-region:*@dev -vvv -o
```

### 使用

```php

\Magein\District\District::getName('110108')
\Magein\District\District::getName('110108', '110113', '110116')

\Magein\District\District::getCode('杭州','合肥')
\Magein\District\District::getCode('安徽省','合肥','杭州')


$address=\Magein\District\District::getAddress(['province_id' => 340000, 'city_id' => 340100, 'district_id' => 340103])
$address=\Magein\District\District::getAddress([340000,340100,340103])

echo $address->toString()
echo $address->toString(' | ')


```

### 延伸

> district、region、area、section、zone、belt、quarter与neighbourhood均含有“地区”之意

district

    多指由政府等机构出于行政管理等目的而明确划分的地区

region

    普通用词，常指地球上、大气中具有自然分界线的区域，特指按照气候、人体或其他特征鲜明、自成一体的地区

area

    普通用词，指整体中较大的，界线不分明的一部分

section

    普通用词，指城市、国家或天然界线形成的地区

zone

    科技用词，指圆形或弧形地带，尤指地图上按温度划分的五个地带。用作一般意义时，也可指具有某种特征的其它地区