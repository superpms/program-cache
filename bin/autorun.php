<?php
if(class_exists('\pms\hook\LifecycleHook')){
    \pms\hook\LifecycleHook::mount(LIFECYCLE_BOOT, function () {
        \pms\facade\Cache::init(\pms\facade\Path::getRuntime('cache'));
    });
}