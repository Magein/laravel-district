<?php

namespace Magein\District;

class Address
{
    public $province = '';

    public $city = '';

    public $district = '';

    public function toString($sp = ',')
    {
        $data = [
            $this->province,
            $this->city,
            $this->district
        ];

        return implode($sp, $data);
    }
}