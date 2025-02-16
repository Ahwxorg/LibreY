<?php
    if (!function_exists("apcu_fetch"))
        error_log("apcu is not installed! Please consider installing php-pecl-apcu for significant performance improvements");


    function load_cooldowns() {
        if (function_exists("apcu_fetch"))
            return apcu_exists("cooldowns") ? apcu_fetch("cooldowns") : array();
        return array();
    }

    function save_cooldowns($cooldowns) {
        if (function_exists("apcu_store"))
            apcu_store("cooldowns", $cooldowns);
    }

    function set_cooldown($instance, $timeout, $cooldowns) {
        $cooldowns[$instance] = time() + $timeout;
        save_cooldowns($cooldowns);
        return $cooldowns;
    }

    function has_cooldown($instance, $cooldowns) {
        return ($cooldowns[$instance] ?? 0) > time();
    }
    
    function has_cached_results($url) {
        if (function_exists("apcu_exists"))
            return apcu_exists("cached:$url");

        return false;
    }

    function store_cached_results($url, $results, $ttl = 0) {
        if (function_exists("apcu_store") && !empty($results) && $ttl >= 0)
            return apcu_store("cached:$url", $results, $ttl);
    }

    function fetch_cached_results($url) {
        if (function_exists("apcu_fetch"))
            return apcu_fetch("cached:$url");

        return array();
    }
?>
