# 运行流程

## Composer 加载链

1. Composer 读取 `composer.json`。
2. `autoload.files` 执行 `bin/autoload.php`。
3. `bin/autoload.php` 执行 `require_once __DIR__ . "/autorun.php"`。
4. `bin/autorun.php` 检查 `\pms\hook\LifecycleHook` 是否存在。
5. 如果存在，挂载 `LIFECYCLE_BOOT` 回调。
6. 启动阶段回调执行 `Cache::init(Path::getRuntime('cache'))`。

## 默认缓存目录

本包自身不计算项目根路径，只使用基础框架提供的：

```php
\pms\facade\Path::getRuntime('cache')
```

因此默认目录归属项目 runtime，而不是包目录、vendor 目录或当前工作目录。

## 文件落点

每个缓存 key 会通过：

```php
$this->rootPath . DIRECTORY_SEPARATOR . md5($key)
```

变成缓存文件路径。

结果：

- 文件名不暴露原始 key。
- 不会按 key 自动分级目录。
- 不同 key 如果 md5 冲突会指向同一文件，虽然常规场景概率极低。

## 数据格式

缓存文件内容是 PHP 序列化结果：

```php
serialize([
    $value,
    $expire <= 0 ? 0 : time() + $expire,
])
```

`get()` 读取后会解包为 `[$data, $expire]`。

## 生命周期边界

本包只在 `LIFECYCLE_BOOT` 初始化缓存目录。后续每次读写都直接操作文件系统，不再依赖额外生命周期 hook。

如果 autoload 发生在没有 `LifecycleHook` 的环境，自动初始化不会执行，调用者必须手动执行 `init()`。
