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
        
    }
}