<?php
/**
 * Created by PhpStorm.
 * User: mrred
 * Date: 11.01.2019
 * Time: 17:42
 */

spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    $filename = __DIR__ . '/class/' . $class . '.php';

    if(!file_exists($filename)) {
        return false;
    }

    include $filename;
    return true;
});