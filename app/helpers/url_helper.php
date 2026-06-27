<?php

if (!function_exists('base_url')) {
    function base_url()
    {
        $config = require __DIR__ . '/../../config/app.php';
        return rtrim($config['base_url'], '/');
    }
}

if (!function_exists('url')) {
    function url($path = '')
    {
        return base_url() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return base_url() . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect($path)
    {
        header('Location: ' . url($path));
        exit;
    }
}
