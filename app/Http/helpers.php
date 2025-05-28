<?php

if (!function_exists('secure_url')) {
    function secure_url($path = null) {
        if (app()->environment('production')) {
            return str_replace('http://', 'https://', url($path));
        }
        return url($path);
    }
}