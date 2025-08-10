<?php

if (!function_exists('filedb')) {
    function filedb() {
        static $instance = null;
        if ($instance === null) {
            $instance = new \App\Support\FileDatabase();
        }
        return $instance;
    }
}
