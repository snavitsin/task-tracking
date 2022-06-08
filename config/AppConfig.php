<?php

namespace App\Config;

class AppConfig
{
    /**
     * Домашний адрес
     * @var string
     */
    public static $homeUrl = '';

    /**
     * Root директория
     * @var string
     */
    public static $root = '';

    /**
     * Является ли запрос ajax'ом
     * @var bool
     */
    public static $isAjax = false;


    public static function set($key, $value) {
        AppConfig::$$key = $value;
    }

    public static function get($key) {
        return AppConfig::$$key;
    }

}