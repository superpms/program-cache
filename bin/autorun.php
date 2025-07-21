<?php
\pms\hook\LifecycleHook::mount(function () {
    $dbConfig = config('redis');
    if ($dbConfig !== null) {
        \pms\facade\Cache::init(\pms\facade\Path::getRuntime('cache'));
    }
});