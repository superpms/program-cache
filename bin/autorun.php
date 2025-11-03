<?php
\pms\hook\LifecycleHook::mount(LIFECYCLE_BOOT,function () {
    $dbConfig = config('redis');
    if ($dbConfig !== null) {
        \pms\facade\Cache::init(\pms\facade\Path::getRuntime('cache'));
    }
});