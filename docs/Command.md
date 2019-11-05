## 命令行（Command）

- 将服务端注册到服务中心

```shell

php artisan microservice:register_server

```

- 将服务端从服务中心中注销

```shell

php artisan microservice:logout_server

```

- 显示服务端及节点列表

```shell

php artisan microservice:server_list

```

- 报告服务端心跳（当服务中心存在服务端节点状态检查时，需要定时报告心跳）

```shell

php artisan microservice:report_server

```