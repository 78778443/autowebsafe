<?php
include "model/sqliteModel.php";
//1. 查询数据列表
$db = new SQLiteModel('./databases/websafe');
$list = $db->table('list')->select();

?>
<html>
<head>
    <title>列表</title>
    <link href="./static/css/bootstrap.min.css" rel="stylesheet">
    <link href="./static/css/bootstrap-theme.min.css" rel="stylesheet">
    <style>
        .maxHidden {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
    </style>
</head>
<body>
<div>
    <button class="btn btn-success" id="create">创建容器</button>
</div>
<div>
    <table class="table" style="table-layout: fixed;width: 100%">

        <tr>
            <td>序号</td>
            <td>端口</td>
            <td>数据库hash</td>
            <td>环境hash</td>
            <td>运行状态</td>
            <td>操作</td>
        </tr>
        <?php foreach ($list as $key => $value) { ?>
            <tr>
                <td><?= $value['id'] ?></td>
                <td><?= $value['port'] ?></td>
                <td class="maxHidden"><?= $value['mysql_hash'] ?></td>
                <td class="maxHidden"><?= $value['websafe_hash'] ?></td>
                <td><?= (empty($value['status']) ? '已结束' : '正在运行') ?></td>
                <td>
                    <a class="btn btn-info btn-sm" href="http://<?= $value['port'] ?>.qsjianzhan.com:8089/"
                       target="_blank">访问网址</a>
                    <a class="btn btn-danger btn-sm del" dataid="<?= $value['id'] ?>">删除服务</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<div></div>
<script src="./static/js/jquery.min.js"></script>
<script src="./static/js/bootstrap.min.js"></script>

<script>
    $(".del").click(function () {
        $id = $(this).attr('dataid');
        $.getJSON("./delete.php?id=" + $id);
        selfReload();
    });

    $("#create").click(function () {
        $.getJSON("./create.php");
        selfReload();
    });

    function selfReload() {
        setTimeout(function () {  //使用  setTimeout（）方法设定定时2000毫秒
            window.location.reload();//页面刷新
        }, 2000);
    }
</script>
</body>
</html>