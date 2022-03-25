<?php

namespace Magein\District;

class Address
{
    public string $province = '';

    public string $city = '';

    public string $district = '';

    public function toString($sp = ','): string
    {
        return implode($sp, $this->toArray());
    }

    public function toArray(): array
    {
        return [
            $this->province,
            $this->city,
            $this->district
        ];
    }
}