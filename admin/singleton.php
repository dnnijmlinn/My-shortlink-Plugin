<?php

namespace MyShortlinkPlugin\Admin;

abstract class Singleton {
    private static $instances = [];

    protected function __construct() {}

    public static function getInstance() {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
    }
}
