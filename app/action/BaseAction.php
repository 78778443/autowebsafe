<?php


namespace action;


class BaseAction
{
    /**
     * 加载模板文件
     *
     * @param $tplPath
     */
    public function show($tplPath, $data = [])
    {
        $filePath = "./views/{$tplPath}.php";
        if (!is_readable($filePath)) {
            echo '模板文件' . $filePath . '不存在!';
            die;
        }

        foreach ($data as $key => $val) {
            $$key = $val;
        }

        include_once $filePath;
    }
}