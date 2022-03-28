<?php

namespace Magein\District;

use Illuminate\Support\Facades\Redis;

/**
 * @method static array getName(...$params)
 * @method static array getCode(...$params)
 * @method static array getCodes()
 * @method static array getPostal(...$params)
 * @method static array getPostals()
 * @method static array getTel(...$params)
 * @method static array getTels()
 * @method static Address getAddress(array $data, $only_district = true)
 */
class District
{
    public static function __callStatic($name, $arguments)
    {
        $name = preg_replace('/get/', '', $name);
        $name = lcfirst($name);

        try {
            $data = call_user_func_array([new static(), $name], $arguments);
        } catch (\RedisException $exception) {
            throw new \Exception('【District redis】' . $exception->getMessage());
        } catch (\Exception $exception) {
            $data = [];
        }

        return $data;
    }

    protected function name(...$arguments): array
    {
        $codes = $this->codes();
        $data = [];
        foreach ($arguments as $item) {
            $data[] = $codes[$item] ?? '';
        }
        return $data;
    }

    /**
     * 获取所有的行政区划代码
     * @return array|mixed
     */
    protected function codes()
    {
        return $this->load('DistrictCode');
    }

    /**
     * 获取指定的行政区划代码
     * @param ...$arguments
     * @return array
     */
    protected function code(...$arguments): array
    {
        $codes = $this->codes();
        $data = [];
        foreach ($arguments as $name) {
            if (preg_match('/^[0-9]+$/', $name)) {
                $value = $name;
            } else {
                $value = array_search($name, $codes);
            }
            $data[] = intval($value);
        }
        return $data;
    }

    /**
     * 获取所有的邮政编码
     * @return array|mixed
     */
    protected function postals()
    {
        return $this->load('Postals');
    }

    /**
     * 获取指定的邮政编码
     * @param ...$arguments
     * @return array
     */
    protected function postal(...$arguments): array
    {
        return $this->trans('postals', ...$arguments);
    }

    /**
     * 获取所有的区号
     * @return array|mixed
     */
    protected function tels()
    {
        return $this->load('Tels');
    }

    /**
     * 获取指定的区号
     * @param ...$arguments
     * @return array
     */
    protected function tel(...$arguments): array
    {
        return $this->trans('tels', ...$arguments);
    }

    /**
     * @param $name
     * @param ...$arguments
     * @return array
     */
    protected function trans($name, ...$arguments): array
    {
        $data = $this->load($name);
        $res = [];
        foreach ($arguments as $item) {
            if (preg_match('/^[0-9]+$/', $item)) {
                $res[] = $data[$item];
            } else {
                $code = $this->code($item)[0] ?? '';
                if ($code) {
                    $res[] = $data[$code];
                } else {
                    $res[] = '';
                }
            }
        }
        return $res;
    }

    protected function address($arguments): Address
    {
        if (isset($arguments['province_id'])) {
            $province_id = $arguments['province_id'] ?? '';
            $city_id = $arguments['city_id'] ?? '';
            $district_id = $arguments['district_id'] ?? '';
        } else {
            $province_id = $arguments[0] ?? '';
            $city_id = $arguments[1] ?? '';
            $district_id = $arguments[2] ?? '';
        }

        $codes = $this->codes();
        $address = new Address();
        $address->province = $codes[$province_id] ?? '';
        $address->city = $codes[$city_id] ?? '';
        $address->district = $codes[$district_id] ?? '';

        return $address;
    }

    protected function load($name)
    {
        $local = function () use ($name) {
            $path = __DIR__ . '/assets/' . $name . '.php';
            if (is_file($path)) {
                return require($path);
            }
            return [];
        };

        try {
            $redis = config('database.redis.district');
        } catch (\Exception $exception) {
            $redis = [];
        }

        if (empty($redis)) {
            return $local();
        }

        $key = md5('district_' . $name);
        $redis = Redis::connection('district');
        if (empty($redis)) {
            return [];
        }
        $data = $redis->client()->get($key);
        if (empty($data)) {
            $data = $local();
            $redis->client()->set($key, json_encode($data));
        } else {
            $data = json_decode($data, true);
        }

        return $data;
    }
}
