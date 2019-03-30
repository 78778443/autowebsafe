<?php

/***
 * //应用举例
 * require_once('cls_sqlite.php');
 * //创建实例
 * $DB=new SQLite('blog.db'); //这个数据库文件名字任意
 * //创建数据库表。
 * $DB->query("create table test(id integer primary key,title varchar(50))");
 * //接下来添加数据
 * $DB->query("insert into test(title) values('泡菜')");
 * $DB->query("insert into test(title) values('蓝雨')");
 * $DB->query("insert into test(title) values('Ajan')");
 * $DB->query("insert into test(title) values('傲雪蓝天')");
 * //读取数据
 * print_r($DB->getlist('select * from test order by id desc'));
 * //更新数据
 * $DB->query('update test set title = "三大" where id = 9');
 ***/
class SQLiteModel
{

    public $tableStr = 'list';
    public $orderStr = '';
    public $whereStr = '';
    public $limitStr = '';

    function __construct($file = './databases/websafe')
    {
        try {
            $this->connection = new PDO('sqlite:' . $file);
        } catch (PDOException $e) {
            try {
                $this->connection = new PDO('sqlite2:' . $file);
            } catch (PDOException $e) {
                exit('error!');
            }
        }
    }

    public function limit($limit)
    {
        $this->limitStr = "LIMIT $limit";
    }

    public function table($table)
    {
        $this->tableStr = $table;

        return $this;
    }

    function __destruct()
    {
        $this->connection = null;
    }


    function query($sql) //直接运行SQL，可用于更新、删除数据
    {

        return $this->connection->query($sql);
    }

    public function order($order)
    {
        $this->orderStr = empty($this->orderStr) ? 'ORDER BY ' : $this->orderStr;

        $this->orderStr .= $order;

        return $this;
    }

    public function select()
    {
        $sql = "select * from {$this->tableStr} {$this->whereStr} {$this->orderStr} {$this->limitStr}";

        $recordlist = array();

        foreach ($this->query($sql) as $rstmp) {
            foreach ($rstmp as $key => $value) {
                if (!is_numeric($key)) {
                    $temp[$key] = $value;
                }
            }

            $recordlist[] = $temp;
        }
        return $recordlist;
    }

    /**
     * 生成查询条件
     * @param $where
     * @return $this
     */
    public function where($where)
    {
        $this->whereStr = empty($this->whereStr) ? 'WHERE' : $this->whereStr;
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                $this->whereStr .= " $key=$val AND ";
            }
            $this->whereStr = rtrim($this->whereStr, 'AND ');
        } elseif (is_string($where)) {
            $this->whereStr .= $where;
        }

        return $this;
    }

    public function get()
    {
        $this->limit(1);

        return $this->select()[0] ?? false;
    }

    function Execute($sql)
    {
        return $this->query($sql)->fetch();
    }

    function RecordArray($sql)
    {
        return $this->query($sql)->fetchAll();
    }

    function RecordCount($sql)
    {
        return count($this->RecordArray($sql));
    }

    function RecordLastID()
    {
        return $this->connection->lastInsertId();
    }


    public function add(array $data)
    {
        $fieldStr = '';
        $dataStr = '';
        foreach ($data as $key => $value) {
            $key = addslashes($key);
            $value = addslashes($value);
            $fieldStr .= "$key,";
            $dataStr .= "'$value',";
        }
        $fieldStr = rtrim($fieldStr, ',');
        $dataStr = rtrim($dataStr, ',');

        $baseSql = "insert into {$this->tableStr}($fieldStr) values($dataStr)";


        return $this->query($baseSql);
    }


    public function update(array $data)
    {
        $updateStr = '';

        foreach ($data as $key => $value) {
            $key = addslashes($key);
            $value = addslashes($value);

            $value = is_numeric($value) ? $value : "'$value'";

            $updateStr .= " {$key} = {$value} , ";
        }
        $updateStr = rtrim($updateStr, ', ');

        $sql = "update {$this->tableStr} set {$updateStr} {$this->whereStr}";

        return $this->query($sql);
    }
}

function M($name)
{
    $db = new SQLiteModel();

    return $db->table($name);
}