# API 参考

公开入口：

- `pms\facade\Cache`
- `pms\program\cache\Driver`

`Cache` facade 代理到 `Driver`，方法行为以 `Driver` 为准。

## init(string $rootPath): void

设置缓存文件根目录。

- 会把非当前系统目录分隔符替换成当前系统分隔符。
- 会移除根目录末尾的目录分隔符。
- 目录不存在时会用 `mkdir($rootPath, 0777, true)` 创建。

框架内通常由 `bin/autorun.php` 自动调用。

## set(string $key, mixed $value, int $expire = 0): bool

写入缓存。

- `$expire <= 0` 表示不过期。
- `$expire > 0` 时保存为 `time() + $expire`。
- 文件内容为 `serialize([$value, $expireTimestamp])`。
- 写入失败返回 `false`。

## get(string $key, mixed $default = null): mixed

读取缓存。

返回 `$default` 的情况：

- 缓存文件不存在。
- 文件打开失败。
- 反序列化或读取过程抛出异常。
- 缓存设置了过期时间且已经过期。

注意：过期文件不会在 `get()` 时自动删除。

## delete(string $key): bool

删除缓存。

- 文件不存在时返回 `true`。
- 文件存在时返回 `unlink()` 结果。

## del(string $key): bool

`delete()` 的别名。

## exists(string $key, ...$other_keys): int

返回存在的 key 数量。

实现使用 `get()` 读取每个 key，并用 `empty($data)` 判断是否存在。因此以下缓存值会被视为不存在：

- `0`
- `"0"`
- `false`
- `""`
- `[]`
- `null`

如果需要缓存这些值，不要用 `exists()` 判断命中。

## setnx(string $key, mixed $value, int $expire = 0): bool

只在 key 不存在时写入。

实现要点：

- 先用 `get($key)` 判断已有数据。
- 如果已有数据被 `empty()` 判定为空，会先删除旧文件。
- 使用 `fopen($path, 'x')` 抢占创建文件。
- 并发下只有抢到创建权的进程写入成功。

## setnxCache(string $name, Closure $callback, int $expireTime = 0, int $retryCount = 10): mixed

如果缓存存在，直接返回缓存；如果不存在，用文件抢占锁让一个进程生成缓存，其余进程等待。

回调签名：

```php
Cache::setnxCache('key', function (callable $setExpire) {
    $setExpire(300);
    return build_value();
});
```

行为：

- 锁名为 `lock:` 加原始 `$name`。
- 抢锁成功的进程执行 `$callback`。
- 回调返回值不是 `false`、`null`、空字符串时才写入缓存。
- 等待进程每次 `usleep(100000)`，默认最多等待约 1 秒。
- 等待超过 `$retryCount` 后抛出 `RedisException("请求终止")`。

注意：该方法同样用 `empty()` 判断缓存是否存在，空值不适合作为有效缓存结果。

## lock(string $name, int $occupy = 3, int $pause = 50): void

获取简单文件锁。

- 实际缓存 key 为 `lock:` 加 `$name`。
- `$occupy` 是锁占用秒数。
- `$pause` 是抢锁间隔毫秒数。
- 如果发现锁内保存的时间戳小于当前时间，会删除旧锁。

该锁没有 owner token，释放时只按名称删除。

## unlock(string $name): void

释放锁。

- 实际删除 key 为 `lock:` 加 `$name`。
- 不校验调用方是否是原持锁者。
