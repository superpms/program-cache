# program-cache docs

这是 `superpms/program-cache` 面向开发者的详细文档入口。

## 先读

- [快速接入](guide/quick-start.md): 安装、启动期挂载、常见用法
- [API 参考](reference/api.md): facade 和 driver 的公开方法
- [源码清单与包入口](reference/source-inventory.md): composer、bin、config、src 覆盖清单

## 按问题读

- 想确认缓存目录从哪里来: [运行流程](internals/runtime-flow.md)
- 想核对 composer、bin、config、src 清单: [源码清单与包入口](reference/source-inventory.md)
- 想知道 key 如何落成文件: [实现注意事项](internals/implementation-notes.md)
- 想排查缓存命中、过期、锁等待: [API 参考](reference/api.md) 和 [实现注意事项](internals/implementation-notes.md)

## 不在这里读

- Redis 缓存能力不属于本包，阅读 `program-redis`。
- 业务端权限、订单、用户等缓存策略不属于本包。
- 项目 runtime 路径规则由基础包的 `Path` facade 决定，本包只消费 `Path::getRuntime('cache')`。
