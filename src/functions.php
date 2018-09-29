<?php
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('getUri')) {
    function getUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $baseurl = $_SERVER['SCRIPT_NAME'];
        if (false === strpos($uri, $baseurl)) {
            $baseurl = rtrim(dirname($baseurl), '/\\');
        }

        $uri = str_replace($baseurl, '', $uri);
        return ltrim($uri, '/');
    }
}
