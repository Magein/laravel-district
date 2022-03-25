<?php

namespace Magein\District;

/**
 * @method static getName()
 * @method static getCode()
 */
class District
{
    public static function __callStatic($name, $arguments)
    {
        var_dump($name, $arguments);
    }

    public function codes()
    {
        return require(__DIR__ . '/files/codes.php');
    }
}