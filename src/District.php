<?php

namespace Magein\District;

use Illuminate\Support\Facades\Redis;

/**
 * @method static array getName(...$params)
 * @method static array getCode(...$params)
 * @method static array getPostal(...$params)
 * @method static array getTel(...$params)
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

    protected function load($name)
    {
        $local = function () use ($name) {
            $path = __DIR__ . '/files/' . $name . '.php';
            if (is_file($path)) {
                return require($path);
            }
            return [];
        };

        $redis = config('database.redis.district');
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

    /**
     * 获取行政区划代码
     * @return array|mixed
     */
    public function codes()
    {
        return $this->load('codes');
    }

    /**
     * 获取邮政编码
     * @return array|mixed
     */
    public function postals()
    {
        return $this->load('postals');
    }

    /**
     * 获取区号
     * @return array|mixed
     */
    public function tels()
    {
        return $this->load('tels');
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

    protected function code(...$arguments): array
    {
        $codes = $this->codes();
        $data = [];
        foreach ($arguments as $name) {
            $value = array_search($name, $codes);
            if (empty($value) && preg_match('/市/', $name)) {
                $value = array_search(preg_replace('/市/', '', $name), $codes);
            }
            $data[] = $value;
        }
        return $data;
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

    protected function postal(...$arguments): array
    {
        return $this->extract('postals', ...$arguments);
    }

    protected function tel(...$arguments): array
    {
        return $this->extract('tels', ...$arguments);
    }

    protected function extract($name, ...$arguments): array
    {
        $data = $this->load($name);
        $res = [];
        foreach ($arguments as $item) {
            if (preg_match('/^[0-9]{6}$/', $item)) {
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
}
