<?php

namespace model;

class ToolsModel
{

    function __construct()
    {

    }

    public static function init()
    {
        //	1. 分配端口，避免重复
        $port = rand(49152, 65534);

        //	2. 创建docker
        $mysqlHash = self::createMysqlServer($port);
        $websafeHash = self::createWebsafeServer($port);

        // 3. 记录到数据库中
        $db = new SQLiteModel(); //这个数据库文件名字任意

        $data = ['port' => $port, 'mysql_hash' => $mysqlHash, 'websafe_hash' => $websafeHash, 'status' => 1];
        $result = $db->table('list')->add($data);

        // 4. 启动FRP链接服务器
        self::connitFrpServer($port);

        // 5. 输出一个可以访问的链接地址
        $url = "http://{$port}.qsjianzhan.com:8089/";
        echo $url;
    }

    public static function connitFrpServer($port)
    {


        $frpPath = "../tools/frp/";
        //获取配置文件模板
        $confStr = file_get_contents("{$frpPath}frpc_web.ini");

        //字符串替换端口
        $confStr = str_replace('8089', $port, $confStr);
        $confStr = str_replace('video', $port, $confStr);
        $confStr = str_replace('[web]', "[web_{$port}]", $confStr);

        //写入到新版本配置文件中
        $newConfPath = "{$frpPath}configs/web_{$port}.ini";
        file_put_contents($newConfPath, $confStr);

        //启动FRP连接
        $logPath = "./logs/web_{$port}.log";
        $configPath = "./configs/web_{$port}.ini";
        $cmd = "cd {$frpPath} && nohup ./frpc -c {$configPath}  >> $logPath & ";


        echo $cmd . PHP_EOL;
        system($cmd);
    }

    public static function createMysqlServer($port)
    {
        $cmd = "docker run --name mysqlserver_$port -e MYSQL_ROOT_PASSWORD=123 -d -i   mysql:5.6";

        $result = system($cmd);


        return $result;
    }

    public static function createWebsafeServer($port)
    {
        $cmd = "docker run --name permeate_$port --link mysqlserver_$port:db  -d -i  -p {$port}:80  registry.cn-hangzhou.aliyuncs.com/daxia/websafe:laster";
        $hashId = system($cmd);

        //启动服务
        $cmd = "docker exec permeate_$port zsh -c \"cd /root/mycode/permeate && git fetch && git reset origin/master && git checkout . && nginx && /usr/sbin/php-fpm7.2  -R\"";

        system($cmd);
        return $hashId;
    }

    public static function cleanData(int $id)
    {
        //1. 接收参数

        //2. 查询对应hash值
        $result = M('list')->where(['id' => $id])->get();

        //3. 删除对应服务
        $cmd = "docker rm {$result['websafe_hash']} -f";
        system($cmd);
        $cmd = "docker rm {$result['mysql_hash']} -f";
        system($cmd);
        //删除对应配置文件和日志
        unlink("../tools/frp/configs/web_{$result['port']}.ini");
        unlink("../tools/frp/logs/web_{$result['port']}.log");

        //4. 关闭FRP连接
        $cmd = "ps -ef|grep {$result['port']}|grep frp|cut -c 8-15|xargs kill -9";
        system($cmd);

        //4. 修改对应状态
        return M('list')->where(['id' => $id])->update(['status' => 0]);
    }


    public static function addLog($content)
    {
        $data = ['app' => 'autowebsafe', 'content' => $content, 'time' => date('Y-m-d H:i:s')];
        //转换成json存储
        $data = json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        //写入日志
        $date = date('Y-m-d');
        $path = $_SESSION['LOG_PATH'] ?? "/data/logs/autoWebSafe{$date}.txt";
        file_put_contents($path, $data, FILE_APPEND);
    }

}
