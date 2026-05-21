# 快速接入

## 安装

```bash
composer require superpms/program-cache
```

包声明了：

```json
{
  "autoload": {
    "files": ["bin/autoload.php"],
    "psr-4": {
      "pms\\": "src/pms/"
    }
  }
}
```

Composer autoload 会执行 `bin/autoload.php`，它继续加载 `bin/autorun.php`。

## 框架内自动初始化

当运行环境里存在 `pms\hook\LifecycleHook` 时，`bin/autorun.php` 会在 `LIFECYCLE_BOOT` 挂载初始化逻辑：

```php
\pms\facade\Cache::init(\pms\facade\Path::getRuntime('cache'));
```

初始化会规范化目录分隔符、去掉末尾分隔符，并在目录不存在时递归创建。

## 框架外手动初始化

如果直接实例化 driver，或在没有生命周期 hook 的场景使用，需要先初始化缓存根目录：

```php
use pms\program\cache\Driver;

$cache = new Driver();
$cache->init(__DIR__ . '/runtime/cache');

$cache->set('token', 'abc', 60);
```

## 常见用法

基础读写：

```php
use pms\facade\Cache;

Cache::set('profile:1', ['id' => 1], 300);
$profile = Cache::get('profile:1', []);
Cache::del('profile:1');
```

只在 key 不存在时写入：

```php
$created = Cache::setnx('job:lock', time(), 5);
```

抢占式生成缓存：

```php
$data = Cache::setnxCache('expensive:data', function (callable $setExpire) {
    $setExpire(120);
    return query_expensive_data();
}, 60);
```

锁定与释放：

```php
Cache::lock('export', 3, 50);

try {
    export_data();
} finally {
    Cache::unlock('export');
}
```

## 当前项目引用示例

当前 server 端 `server/app/administrator/action/AdminAction.php` 使用：

- `Cache::setnxCache()` 缓存管理员权限数据
- `Cache::delete()` 清理对应权限缓存

这说明本包在项目里的主要角色是轻量文件缓存和抢占式缓存生成。
