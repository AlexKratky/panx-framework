<?php
class User {
    private $usersModel;
    private $uid;
    private $columns = array();

    public function __construct($id) {
        $this->uid = $id;
        $this->usersModel = new UsersModel();
        $user = $this->usersModel->findRowById($id);
        foreach ($user as $key => $value) {
            if(!is_int($key)) {
                if(strtolower($key) == "password" || strtolower($key) == "pass") continue;
                array_push($this->columns, $key);
                $key = strtolower($key);
                
                $this->$key = $value;
            }
        }
    }

    public function save() {
        if(empty($this->uid)) return;
        $newData = "";
        $arr = array();
        foreach ($this->columns as $column) {
            $x = strtolower($column);
            if($newData != "") $newData .= ", ";
            $newData .= "`{$column}`" . '=?';
            array_push($arr, $this->$x);
        }
        array_push($arr, $this->id);

        db::query("UPDATE `users` SET {$newData} WHERE ID=?", $arr);
    
    }
}