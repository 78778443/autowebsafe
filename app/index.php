<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/9/11
 * Time: 下午2:29
 */
define('APP', true);
define('PATH', $_SERVER['DOCUMENT_ROOT']);
date_default_timezone_set('PRC'); //设置中国时区
require_once "./common/common.php";

if (is_cli()) {
    unset($argv[0]);
    $arr = array_values($argv);
    $action = isset($arr[0]) ? ucfirst(addslashes($arr[0])) : 'Index';
    $method = isset($arr[1]) ? addslashes($arr[1]) : 'index';
} else {
    //路由规则
    $arr = array_values(array_filter(explode('/', $_GET['s'] ?? '')));
    $action = isset($arr[0]) ? ucfirst(addslashes($arr[0])) : 'Index';
    $method = isset($arr[1]) ? addslashes($arr[1]) : 'index';
}

$_SERVER['APP_ACTION'] = $action;
$_SERVER['APP_METHOD'] = $method;

//记录请求日志
$type = $_SERVER['REQUEST_METHOD'] ?? 'cli';
\model\ToolsModel::addLog($_REQUEST);

//加载对应控制器
require_once "./action/BaseAction.php";
require_once "./action/{$action}Action.php";
//实例化
$actionFull = "\action\\{$action}Action";
$actionApi = new $actionFull;

$actionApi->$method();