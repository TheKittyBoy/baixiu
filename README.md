# baixiu
根据别人的项目自己尝试搭建

后台管理模板 <https://preview.pro.ant.design/dashboard/analysis> 

后台管理系统 <https://wordpress.org/download/> 

后台管理系统 <http://typecho.org/> ---念念不忘，必有回响

*下载下来后，在apache中配置虚拟机，如果想用typecho需要在php.ini中改配置文件，搜索pdo,将其相关几个注释全部取消。

了解过后，开始根据别人的例子搭建

创建域名在 C:\Windows\System32\drivers\etc 目录下的hosts文件

## 创建数据库

### 建立与数据库的连接

注意：这种只有服务端代码的 PHP 文件应该去除结尾处的 `?>`，防止输出内容 

/**

 \* 数据库主机

 */

// define('DB_HOST', '127.0.0.1');

 

/**

 \* 数据库用户名

 */

// define('DB_USER', 'root');

 

/**

 \* 数据库密码

 */

// define('DB_PASS', 'wanglei')；

/**

 \* 数据库名称

 */

// define('DB_NAME', 'baixiu');

 载入脚本的几种方式对比

- `require`
- `require_once`
- `include`
- `include_once`
- 共同点： 都可以在当前 PHP 脚本文件执行时载入另外一个 PHP 脚本文件 
- `require` 和 `include` 不同点： 当载入的脚本文件不存在时，`require` 会报一个致命错误（结束程序执行），而 `include` 不会
- 有 `once` 后缀的特点： 判断当前载入的脚本文件是否已经载入过，如果载入了就不在执行



#### 显示 PHP 错误信息

当执行 PHP 文件发生错误时，如果在页面上不显示错误信息，只是提示 500 Internal Server Error 错误，应该是 PHP 配置的问题，解决方案就是：找到 `php.ini` 配置文件中 `display_errors` 选项，将值设置为 `On`（等产品发布的时候改回来就好了）

### 整合全部静态页面

1. 将静态页面全部拷贝到 `admin` 目录中。

2. 将文件扩展名由 `.html` 改为 `.php`，页面中的 `a` 链接也需要调整。

   cmd打开命令行

   C:\Users\zzq>cd desktop

   C:\Users\zzq\Desktop>cd baixiu

   C:\Users\zzq\Desktop\baixiu>cd admin

   C:\Users\zzq\Desktop\baixiu\admin>dir

   C:\Users\zzq\Desktop\baixiu\admin>ren *.html *.php       （将所有的html后缀改为php后缀~~ren命令为重命名）

3. 调整页面文件中的静态资源路径，将原本的相对路径调整为绝对路径

#### 绝对路径 vs 相对路径（重点掌握）

1. 不会跟随当前页面的访问地址变化而变化
2. 更简单明了，不容易出错，不用一层一层的找