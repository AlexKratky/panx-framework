<?php
abstract class Model {
    public $TABLE_NAME;
    private $columns = array();

    public function __construct($id = null) {
        if($id != null) {
            $x = $this->findRowById($id);
            if(count($x) > 0) {
                foreach ($x as $key => $value) {
                    if(!is_int($key)) {
                        if(strtolower($key) == "pass" || strtolower($key) == "password") continue;
                        array_push($this->columns, $key);                    
                        $key = strtolower($key);
                        $this->$key = $value;
                    }
                }
            }
        }
    }

    public function findColumnById($id, $column, $uppercase = false) {
        if(!$uppercase)
            return db::select("SELECT * FROM `{$this->TABLE_NAME}` WHERE id=?", array($id))[$column];
        return db::select("SELECT * FROM `{$this->TABLE_NAME}` WHERE ID=?", array($id))[$column];
    }

    public function findRowById($id, $uppercase = false) {
        if(!$uppercase)
            return db::select("SELECT * FROM `{$this->TABLE_NAME}` WHERE id=?", array($id));
        return db::select("SELECT * FROM `{$this->TABLE_NAME}` WHERE ID=?", array($id));
    }

    public function save() {
        if(empty($this->id)) return;
        $newData = "";
        $arr = array();
        foreach ($this->columns as $column) {
            $x = strtolower($column);
            if($newData != "") $newData .= ", ";
            $newData .= "`{$column}`" . '=?';
            array_push($arr, $this->$x);
        }
        array_push($arr, $this->id);

        db::query("UPDATE `{$this->TABLE_NAME}` SET {$newData} WHERE ID=?", $arr);
    }
}
