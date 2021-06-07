安装方法:
一、首先配置好php环境:
二、创建数据库：
数据引擎建议支持InnoDB, 创建一个char_set="utf8",dbcollat="utf8_general_ci"的数据库,
导入"application/admin/config/sql/init.sql"
三、配置数据库连接：
后台：打开"application/admin/config/database.php"文件进行配置
前台：打开"application/client/config/database.php"文件进行配置
四、配置网站访问地址：
后台：打开"application/admin/config/config.php"文件，对$config['base_url']进行配置
前台：打开"application/client/config/config.php"文件进行配置，对$config['base_url']进行配置
五：文件访问路径配置：
后台:打开根目录下的"admincp.php"进行配置
前台:打开根目录下的"index.php"进行配置


/********************seesion文件权限0700************************/
application/admin/session_log
application/client/session_log

建议配置到单独的文件目录下


/********************uploads文件权限0777************************/
./uploads