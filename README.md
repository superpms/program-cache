# superpms/program-cache

`program-cache` 是 superpms 的文件缓存包，提供 `pms\facade\Cache` facade 和 `pms\program\cache\Driver` 文件型缓存实现。

它的职责是把简单 key/value 缓存、抢占式缓存生成和轻量文件锁接入框架运行期；它不是 Redis 包，也不负责业务端缓存策略。

## 要求

- PHP `>=8.1`
- 依赖 superpms 基础 facade、路径和生命周期能力

## 安装与挂载

```bash
composer require superpms/program-cache
```

包的 `composer.json` 会通过 `autoload.files` 加载 `bin/autoload.php`，再加载 `bin/autorun.php`。

当框架存在 `pms\hook\LifecycleHook` 时，包会在 `LIFECYCLE_BOOT` 阶段执行：

```php
\pms\facade\Cache::init(\pms\facade\Path::getRuntime('cache'));
```

也就是说，默认缓存目录是项目 runtime 下的 `cache` 目录。这个包没有 `resource/config.php`，也不会通过 `extra.pms.config` 投影项目配置文件。

## 快速使用

```php
use pms\facade\Cache;

Cache::set('demo:user:1', ['name' => 'Ada'], 300);

$user = Cache::get('demo:user:1', []);

Cache::delete('demo:user:1');
```

抢占式生成缓存：

```php
$data = Cache::setnxCache('report:daily', function (callable $setExpire) {
    $setExpire(600);
    return build_daily_report();
});
```

简单文件锁：

```php
Cache::lock('sync-job', 3, 50);

try {
    run_sync_job();
} finally {
    Cache::unlock('sync-job');
}
```

## 主要模块

- `bin/autoload.php`: Composer files autoload 入口
- `bin/autorun.php`: 生命周期挂载入口
- `src/pms/facade/Cache.php`: facade 入口
- `src/pms/program/cache/Driver.php`: 文件缓存 driver

## Docs 导航

- [文档入口](docs/README.md)
- [快速接入](docs/guide/quick-start.md)
- [API 参考](docs/reference/api.md)
- [运行流程](docs/internals/runtime-flow.md)
- [实现注意事项](docs/internals/implementation-notes.md)

## 注意事项

- 缓存文件名是 `md5($key)`，不会保留原始 key。
- 缓存值通过 `serialize([value, expire])` 写入文件。
- `get()` 遇到文件不存在、读取失败、反序列化失败或过期时返回默认值。
- `exists()`、`setnx()` 和 `setnxCache()` 会把 `empty()` 结果视作不存在；缓存 `0`、`false`、空字符串、空数组时要特别注意。
- 过期缓存读取时不会自动删除文件。
- `setnxCache()` 等待超时会抛出 `RedisException`，虽然当前实现是文件缓存。
