# 源码清单与包入口

本文记录 `superpms/program-cache` 的包级入口、自动加载声明和一方源码文件，作为功能覆盖核查的基准。

## composer.json

| 项 | 值 |
| --- | --- |
| `autoload.files` | `bin/autoload.php` |
| `autoload.psr-4` | `pms\\` -> `src/pms/` |
| `extra.pms` | 无 |
| `bin` | 无 |

## bin 文件

| 文件 | 作用 |
| --- | --- |
| `bin/autoload.php` | Composer files 入口，加载 `bin/autorun.php` |
| `bin/autorun.php` | 如果存在 `LifecycleHook`，在 `LIFECYCLE_BOOT` 调用 `Cache::init(Path::getRuntime('cache'))` |

## resource/config.php

无。缓存目录来自基础框架 `Path::getRuntime('cache')`，不是配置投影。

## src 一方源码

| 文件 | 公开功能面 |
| --- | --- |
| `src/pms/facade/Cache.php` | `Cache` facade，代理 `pms\program\cache\Driver` |
| `src/pms/program/cache/Driver.php` | 文件缓存 driver；提供 `init`、`set`、`get`、`delete/del`、`exists`、`setnx`、`setnxCache`、`lock`、`unlock` |

## 覆盖入口

- 使用入口见 [快速接入](../guide/quick-start.md)。
- API 行为见 [API 参考](api.md)。
- 加载和缓存文件格式见 [运行流程](../internals/runtime-flow.md)。
- 空值、过期和并发语义见 [实现注意事项](../internals/implementation-notes.md)。
