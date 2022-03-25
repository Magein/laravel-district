<?php

require_once __DIR__ . '/vendor/autoload.php';

require './src/District.php';
require './src/Address.php';

use Magein\District\District;

$name = District::getName('110108', '110113', '110116');

$code = District::getCode('安徽省', '河北省', '阜阳市', '巢湖市');
var_dump($code);

$name = District::getAddress(['province_id' => 340000, 'city_id' => 340100, 'district_id' => 340103]);

$address = District::getAddress([340000, 340100, 340103]);
var_dump($address);
var_dump($address->province);
var_dump($address->toString('-'));


