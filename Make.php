<?php

require_once __DIR__ . '/vendor/autoload.php';


$sql_path = __DIR__ . '/src/sql/region.sql';
$fs = fopen($sql_path, 'r');
$sql = fread($fs, filesize($sql_path));

message('开始创建regions表');

try {
    \Illuminate\Support\Facades\DB::statement($sql);
    $res = true;
} catch (Exception $exception) {
    $res = false;
}

if ($res) {
    $codes = require(__DIR__ . '/src/files/list.php');
    foreach ($codes as $item) {
        try {
            \Illuminate\Support\Facades\DB::table('regions')->insert($item);
        } catch (Exception $exception) {
            message('插入失败地区信息失败');
            break;
        }
    }
} else {
    message('创建表结构失败');
}

function message($message)
{
    echo $message;
    echo "\n";
}


