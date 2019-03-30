<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 9:17 AM
 */

include_once "./model/sqliteModel.php";


function init()
{
    //	1. 分配端口，避免重复
    $port = rand(49152, 65534);

    //	2. 创建docker
    $mysqlHash = createMysqlServer($port);
    $websafeHash = createWebsafeServer($port);


    // 3. 记录到数据库中
    $db = new SQLiteModel('./databases/websafe'); //这个数据库文件名字任意

    $data = ['port' => $port, 'mysql_hash' => $mysqlHash, 'websafe_hash' => $websafeHash, 'status' => 1];
    $result = $db->table('list')->add($data);

    // 4. 启动FRP链接服务器
    connitFrpServer($port);

    // 5. 输出一个可以访问的链接地址
    $url = "http://{$port}.qsjianzhan.com:8089/";
    echo $url;
}

function connitFrpServer($port)
{
    $frpPath = "/Users/song/files/frp/";
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
    $logPath = "{$frpPath}logs/web_{$port}.log";
    $cmd = "nohup {$frpPath}frpc -c {$newConfPath}  >> $logPath & ";

    system($cmd);
}

function createMysqlServer($port)
{
    $cmd = "docker run --name mysqlserver_$port -e MYSQL_ROOT_PASSWORD=123 -d -i   mysql:5.6";

    $result = system($cmd);


    return $result;
}

function createWebsafeServer($port)
{
    $cmd = "docker run --name permeate_$port --link mysqlserver_$port:db  -d -i  -p {$port}:80  registry.cn-hangzhou.aliyuncs.com/daxia/websafe:laster";
    $hashId = system($cmd);

    //启动服务
    $cmd = "docker exec permeate_$port zsh -c \"cd /root/mycode/permeate && git fetch && git reset origin/master && git checkout . && nginx && /usr/sbin/php-fpm7.2  -R\"";

    system($cmd);
    return $hashId;
}

function cleanData(int $id)
{
    //1. 接收参数

    //2. 查询对应hash值
    $result = M('list')->where(['id' => $id])->get();


    //3. 删除对应服务
    $cmd = "docker rm {$result['websafe_hash']} -f";
    system($cmd);
    $cmd = "docker rm {$result['mysql_hash']} -f";
    system($cmd);

    //4. 关闭FRP连接
    $cmd = "ps -ef|grep {$result['port']}|grep frp|cut -c 8-15|xargs kill -9";
    system($cmd);

    //4. 修改对应状态
    return M('list')->where(['id' => $id])->update(['status' => 0]);

}