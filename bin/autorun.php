<?php
\pms\hook\LifecycleHook::mount(LIFECYCLE_BOOT, function () {
    \pms\facade\Cache::init(\pms\facade\Path::getRuntime('cache'));
});