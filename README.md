# 基于 CAS 实现通用的单点登录
### CAS 单点登录实现方案涉及至少三个方面：CAS Server、CAS Client（需要认证的 Web 应用）、客户端浏览器。
![流程图](https://laravel.gstatics.cn/wp-content/uploads/2019/01/28208b4498cbb1acd25639e3406574ec.jpg)

1.在 .env 中配置数据库信息，以便后续实现基于数据库的用户认证。然后安装 leo108/laravel_cas_server 扩展包：**composer require leo108/laravel_cas_server**

2.将 CAS 扩展包配置文件 cas.php 发布到 config 目录下：**php artisan vendor:publish --provider="Leo108\CAS\CASServerServiceProvider"**

3.运行数据库迁移命令创建相关的数据表了：**php artisan migrate**

![数据表](https://laravel.gstatics.cn/wp-content/uploads/2019/01/474f699033e0b4cee38c40cd8920d644.jpg)

1.cas_tickets 用于存放所有生成的 Ticket

2.cas_services 和 cas_service_hosts 用于存放所有需要认证的 Web 应用及对应服务，需要提前注册才能进行单点登录
### 在 CAS 服务端还是借助 Laravel 自带的认证视图进行登录认证，运行 **php artisan make:auth** 生成认证脚手架视图

![总结流程图](https://helixlife-static.oss-cn-beijing.aliyuncs.com/circle/images/43b399280aa6d6fda6782764f5501a3.png)