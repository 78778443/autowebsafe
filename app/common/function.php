<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2019/3/30
 * Time: 2:05 PM
 */

//判断当前是否cli模式
function is_cli()
{
    return preg_match("/cli/i", php_sapi_name()) ? true : false;
}

function M($name)
{
    $db = new \model\SQLiteModel();

    return $db->table($name);
}

function U($path = '', $param = [])
{
    return "/?s={$path}&" . http_build_query($param);
}

function I($name, $default = null)
{
    return $_POST[$name] ?? ($_GET[$name] ?? $default);
}