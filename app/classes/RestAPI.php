<?php
class RestAPI {
    private $auth;
    private $needToBeLogined = true;
    private $get = array();
    private $create = array();
    private $update = array();
    private $delete = array();

    public function __construct($needToBeLogined = true, $get = array(), $create = array(), $update = array(), $delete = array()) {
        $this->auth = $GLOBALS["auth"];
        $this->needToBeLogined = $needToBeLogined;
        $this->get = $get;
        $this->create = $create;
        $this->update = $update;
        $this->delete = $delete;
    }

    public function login() {
        $request = $GLOBALS["request"];
        $model = new AuthModel();
        if($request->getPost('username') != null && $request->getPost('password') != null) {
            if($model->verifyLogin($request->getPost('username'), $request->getPost('password'))) {
                $user = $model->loadData($request->getPost('username'));
                if($model->isEnabled2FA($user["ID"]) && $request->getPost('2fa_code') == null) {
                    return array("success" => false, "error" => "enter_2fa_code");
                } elseif($model->isEnabled2FA($user["ID"]) && $request->getPost('2fa_code') != null) {
                    //verify 2fa code
                    $twoFA = new PragmaRX\Google2FAQRCode\Google2FA();
                    $secret = $model->get2FASecret($user["ID"]);
                    if (!$twoFA->verifyKey($secret, $request->getPost('2fa_code'))) {
                        return array("success" => false, "error" => "invalid_2fa_code");
                    }
                }
                //create login token
                $token = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(64))), 0, 63);
                if(db::count("SELECT COUNT(*) FROM `login_tokens` WHERE `USER_ID`=?", array($user["ID"])) > 0) {
                    while(db::count("SELECT COUNT(*) FROM `login_tokens` WHERE `TOKEN`=?", array($token)) > 0) {
                        $token = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(64))), 0, 63);
                    }
                    db::query("UPDATE `login_tokens` SET `TOKEN`=? WHERE `USER_ID`=?", array($token,$user["ID"]));
                } else {
                    while(db::count("SELECT COUNT(*) FROM `login_tokens` WHERE `TOKEN`=?", array($token)) > 0) {
                        $token = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(64))), 0, 63);
                    }
                    db::query("INSERT INTO `login_tokens` (`USER_ID`, `TOKEN`) VALUES (?,?)", array($user["ID"], $token));
                }

                //return login token
                return array("success" => true, "token" => "$token");
            
            } else {
                return array("success" => false, "error" => "invalid_login");
            }
        } else {
            return array("success" => false, "error" => "missing_username_or_password");
        }
    }

    public function get($table, $column, $id) {
        if(!$this->auth->isLogined()) {
            if($GLOBALS["request"]->getPost("login_token") != null) {
                $m = new AuthModel();
                if(!$m->verifyLoginToken($GLOBALS["request"]->getPost("login_token"))) {
                    return array("success" => false, "error" => "invalid_login_token");
                    //TODO: add this to all function, also create option to create Auth instance of this
                }
                $this->auth->loginUserFromToken($GLOBALS["request"]->getPost("login_token"));
            } else {
                return array("success" => false, "error" => "not_logined");
            }
        }
        if(!isset($this->get[$table])) return array("success" => false, "error" => "invalid_table");
        if(($this->get[$table]["getByColumn"] == null)) {      
            $c = array();
            foreach ($this->get[$table]["columns"] as $key => $value) {array_push($c, $value);}
            if(!(in_array($column, $c))) return array("success" => false, "error" => "invalid_column");
        } else {
            $column = $this->get[$table]["getByColumn"];
        }
        if($this->get[$table]["permission"] != null) {
            if(!$this->auth->isUserPermittedTo($this->get[$table]["permission"])) {
                // CHECK OWN ROW
                if($this->get[$table]["user_row"] != null) {
                    if($this->get[$table]["user_row"]["permission"] != null) {
                        if(!$this->auth->isUserPermittedTo($this->get[$table]["user_row"]["permission"])) {
                            return array("success" => false, "error" => "user_permission_denied");
                        }
                    }
                    if(isset($this->get[$table]["user_row"]["column"])) $column = $this->get[$table]["user_row"]["column"];
                    $r = db::select("SELECT * FROM `$table` WHERE `$column`=?", array($id));
                    if($r !== false) {
                        if(isset($r[$column]) && $r[$column] == $this->auth->user($this->get[$table]["user_row"]["user"])) {
                            $data = array();
                            foreach ($this->get[$table]["columns"] as $key => $value) {
                                if(isset($r[$value])) {
                                    $data[$value] = $r[$value];
                                }
                            }
                            return array("success" => true, "data" => $data);
                        }
                    } else {
                        return array("success" => false, "error" => "id_not_found");
                    }
                }
                return array("success" => false, "error" => "permission_denied");
            }
        }
        if(isset($GLOBALS["request"]->getUrl()->getLink()[7]) && $GLOBALS["request"]->getUrl()->getLink()[7] == "all") {
            // TODO: FIX ALL
            if($this->get[$table]["select_all_permission"] != null) {
                if(!$this->auth->isUserPermittedTo($this->get[$table]["select_all_permission"])) {
                    return array("success" => false, "error" => "permission_denied");
                }
            }
            $r = db::multipleSelect("SELECT * FROM `$table`", array($id));
            if(count($r > 0)) {
                $data = array();
                foreach($r as $k => $v) {
                    //if(is_numeric($k)) continue;
                    foreach ($this->get[$table]["columns"] as $key => $value) {
                        if(isset($k[$value])) {
                            $data[$value] = $v[$value];
                        }
                    }
                }
                return array("success" => true, "data" => $data);
            } else {
                return array("success" => false, "error" => "id_not_found");
            }
        } else {
            $r = db::select("SELECT * FROM `$table` WHERE `$column`=?", array($id));
            if($r !== false) {
                $data = array();
                foreach ($this->get[$table]["columns"] as $key => $value) {
                    if(isset($r[$value])) {
                        $data[$value] = $r[$value];
                    }
                }
                return array("success" => true, "data" => $data);
            } else {
                return array("success" => false, "error" => "id_not_found");
            }
        }
    }

    public function create($table) {
        if(!$this->auth->isLogined()) {
            if($GLOBALS["request"]->getPost("login_token") != null) {
                $m = new AuthModel();
                if(!$m->verifyLoginToken($GLOBALS["request"]->getPost("login_token"))) {
                    return array("success" => false, "error" => "invalid_login_token");
                    //TODO: add this to all function, also create option to create Auth instance of this
                }
                $this->auth->loginUserFromToken($GLOBALS["request"]->getPost("login_token"));
            } else {
                return array("success" => false, "error" => "not_logined");
            }
        }
        if(!isset($this->create[$table])) return array("success" => false, "error" => "invalid_table");
        if($this->create[$table]["permission"] != null) {
            if(!$this->auth->isUserPermittedTo($this->create[$table]["permission"])) {
                return array("success" => false, "error" => "permission_denied");
            }
        }

        $request = $GLOBALS["request"];
        $d = array();
        foreach ($this->create[$table]["columns"] as $key => $value) {array_push($d, $value);}
        if($request->workWith("POST", $d)) {
            $c = "";
            $q = "";
            $v = array();
            foreach ($this->create[$table]["columns"] as $key => $value) {
                if($c != "") {
                    $c .= ", ";
                    $q .= ", ";
                }
                $c .= "`{$value}`";
                $q .= "?";
                array_push($request->getPost($value));
            }
            db::query("INSERT INTO `{$table}` ({$c}) VALUES ({$q})", array($v));
            return array("success" => true, "inserted_id" => db::$id);
        } else {
            return array("success" => false, "error" => "missing_values");
        }
    }

    public function update($table, $column, $id) {
        if(!$this->auth->isLogined()) {
            if($GLOBALS["request"]->getPost("login_token") != null) {
                $m = new AuthModel();
                if(!$m->verifyLoginToken($GLOBALS["request"]->getPost("login_token"))) {
                    return array("success" => false, "error" => "invalid_login_token");
                    //TODO: add this to all function, also create option to create Auth instance of this
                }
                $this->auth->loginUserFromToken($GLOBALS["request"]->getPost("login_token"));
            } else {
                return array("success" => false, "error" => "not_logined");
            }
        }
        if(!isset($this->update[$table])) return array("success" => false, "error" => "invalid_table");
        if(($this->update[$table]["getByColumn"] == null)) {      
            $c = array();
            foreach ($this->update[$table]["columns"] as $key => $value) {array_push($c, $value);}
            if(!(in_array($column, $c))) return array("success" => false, "error" => "invalid_column");
        } else {
            $column = $this->update[$table]["getByColumn"];
        }
        
        $request = $GLOBALS["request"];
        $d = array();
        foreach ($this->update[$table]["columns"] as $key => $value) {array_push($d, $value);}
        if($request->workWith("POST", $d)) {
            $c = "";
            $v = array();
            foreach ($this->update[$table]["columns"] as $key => $value) {
                if($c != "") {
                    $c .= ", ";
                }
                $c .= "`{$value}`=?";
                array_push($request->getPost($value));
            }
            array_push($v, $id);
            if($this->update[$table]["permission"] != null) {
                if(!$this->auth->isUserPermittedTo($this->update[$table]["permission"])) {
                    if($this->update[$table]["user_row"] != null) {
                        if($this->update[$table]["user_row"]["permission"] != null) {
                            if(!$this->auth->isUserPermittedTo($this->update[$table]["user_row"]["permission"])) {
                                return array("success" => false, "error" => "user_permission_denied");
                            }
                        }
                        if(isset($this->update[$table]["user_row"]["column"])) $column = $this->update[$table]["user_row"]["column"];
                        $r = db::select("SELECT * FROM `$table` WHERE `$column`=?", array($id));
                        if($r !== false) {
                            if(isset($r[$column]) && $r[$column] == $this->auth->user($this->update[$table]["user_row"]["user"])) {
                                $data = array();
                                foreach ($this->update[$table]["columns"] as $key => $value) {
                                    if(isset($r[$value])) {
                                        $data[$value] = $r[$value];
                                    }
                                }
                                //TODO: UPDATE
                                if(db::count("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}`=?", array($id)) > 0) {
                                    db::query("UPDATE `{$table}` SET {$c} WHERE `{$column}`=?", array($v));
                                    return array("success" => true);

                                } else {
                                    return array("success" => false, "error" => "id_not_found");

                                }
                                return array("success" => true, "data" => $data);
                            }
                        } else {
                            return array("success" => false, "error" => "id_not_found");
                        }
                    }
                    return array("success" => false, "error" => "permission_denied");
                }
            }

            if(db::count("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}`=?", array($id)) > 0) {
                db::query("UPDATE `{$table}` SET {$c} WHERE `{$column}`=?", array($v));
                return array("success" => true);

            } else {
                return array("success" => false, "error" => "id_not_found");

            }
        } else {
            return array("success" => false, "error" => "missing_values");
        }
    }

    public function delete($table, $column, $id) {
        if(!$this->auth->isLogined()) {
            if($GLOBALS["request"]->getPost("login_token") != null) {
                $m = new AuthModel();
                if(!$m->verifyLoginToken($GLOBALS["request"]->getPost("login_token"))) {
                    return array("success" => false, "error" => "invalid_login_token");
                    //TODO: add this to all function, also create option to create Auth instance of this
                }
                $this->auth->loginUserFromToken($GLOBALS["request"]->getPost("login_token"));
            } else {
                return array("success" => false, "error" => "not_logined");
            }
        }
        if(!isset($this->delete[$table])) return array("success" => false, "error" => "invalid_table");
        if($this->delete[$table]["permission"] != null) {
            if(!$this->auth->isUserPermittedTo($this->delete[$table]["permission"])) {
                return array("success" => false, "error" => "permission_denied");
            }
        }

        if(($this->delete[$table]["getByColumn"] == null)) {      
            // TODO FIX
            $c = array();
            foreach ($this->delete[$table]["columns"] as $key => $value) {array_push($c, $value);}
            if(!(in_array($column, $c))) return array("success" => false, "error" => "invalid_column");
        } else {
            $column = $this->delete[$table]["getByColumn"];
        }
        if(db::count("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}`=?", array($id)) > 0) {
            db::query("DELETE FROM `{$table}` WHERE `{$column}`=?", array($id));
            return array("success" => true);

        } else {
            return array("success" => false, "error" => "id_not_found");

        }
    }
}