<?php

namespace App\Utils;

class UUID
{
    public static function generate()
    {
        return uniqid();
    }
}
