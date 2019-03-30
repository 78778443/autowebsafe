<?php


namespace action;


use model\SQLiteModel;
use model\ToolsModel;

class IndexAction extends BaseAction
{
    public function index()
    {
        //1. 查询数据列表
        $db = new SQLiteModel();
        $list = $db->table('list')->select();


        $this->show('index/index', ['list' => $list]);
    }

    public function create()
    {
        ToolsModel::init();
    }

    public function del()
    {
        $id = I('id');
        ToolsModel::cleanData($id);
    }
}