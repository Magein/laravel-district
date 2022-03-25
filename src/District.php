<?php

namespace Magein\District;

/**
 * @method static getName(...$params)
 * @method static getCode(...$params)
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
        } catch (\Exception $exception) {
            $data = null;
        }

        return $data;
    }

    public function codes()
    {
        return require(__DIR__ . '/files/codes.php');
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
}