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
            return array("success" => false, "error" => "missing_username_or_password".$request->getPost('username'));
        }
    }

    public function checkLogin() {
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
        return true;
    }

    public function get($table, $limit_or_column, $id) {
        if(!$this->checkLogin()) return $this->checkLogin();

        if(!isset($this->get[$table])) return array("success" => false, "error" => "invalid_table");

        $limit  = ($id == null ? $limit_or_column : null);
        $column = ($id == null ? null : $limit_or_column);

        if(($this->get[$table]["getByColumn"] == null) && $column != null) {      
            $c = array();
            foreach ($this->get[$table]["columns"] as $key => $value) {array_push($c, $value);}
            if(!(in_array($column, $c))) return array("success" => false, "error" => "invalid_column");
        } else {
            $column = $this->get[$table]["getByColumn"];
        }

        if($id == null) { // select all permission
            if($this->get[$table]["select_all_permission"] != null) {
                if(!$this->auth->isUserPermittedTo($this->get[$table]["select_all_permission"])) {
                    return $this->getUser($table, $limit_or_column, $id);
                }
            }
            $r = db::multipleSelect("SELECT * FROM `$table`" . ($this->get[$table]["order"] != null ? " ORDER BY `{$this->get[$table]['order']['column']}` {$this->get[$table]['order']['type']}" : "") . ($limit != null ? " LIMIT {$limit}" : ""));
            if(count($r) > 0) {
                $data = array();
                foreach($r as $k) {
                    $t = array();
                    foreach ($this->get[$table]["columns"] as $key => $value) {
                        if(isset($k[$value])) {
                            $t[$value] = $k[$value];
                        }
                    }
                    array_push($data, $t);
                }
                return array("success" => true, "data" => $data);
            } else {
                return array("success" => false, "error" => "id_not_found");
            }
        }

        if($this->get[$table]["permission"] != null) {
            if(!$this->auth->isUserPermittedTo($this->get[$table]["permission"])) {
                return $this->getUser($table, $limit_or_column, $id);
            }
        }

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

    public function getUser($table, $limit_or_column, $id) {
        if(!$this->checkLogin()) return $this->checkLogin();

        if(!isset($this->get[$table])) return array("success" => false, "error" => "invalid_table");

        $limit  = ($id == null ? $limit_or_column : null);
        $column = ($id == null ? null : $limit_or_column);

        if(($this->get[$table]["getByColumn"] == null) && $column != null) {      
            $c = array();
            foreach ($this->get[$table]["columns"] as $key => $value) {array_push($c, $value);}
            if(!(in_array($column, $c))) return array("success" => false, "error" => "invalid_column");
        } else {
            $column = $this->get[$table]["getByColumn"];
        }

        if($id == null) { // select all permission
            if($this->get[$table]["select_all_user"]["permission"] != null) {
                if(!$this->auth->isUserPermittedTo($this->get[$table]["select_all_user"]["permission"])) {
                    return array("success" => false, "error" => "user_permission_denied");
                }
            }
            $r = db::multipleSelect("SELECT * FROM `$table` WHERE `{$this->get[$table]['select_all_user']['column']}`=?" . ($this->get[$table]["order"] != null ? " ORDER BY `{$this->get[$table]['order']['column']}` {$this->get[$table]['order']['type']}" : "") . ($limit != null ? " LIMIT {$limit}" : ""), array($this->auth->user($this->get[$table]['select_all_user']['user'])));
            if(count($r) > 0) {
                $data = array();
                foreach($r as $k) {
                    $t = array();
                    foreach ($this->get[$table]["columns"] as $key => $value) {
                        if(isset($k[$value])) {
                            $t[$value] = $k[$value];
                        }
                    }
                    array_push($data, $t);
                }
                return array("success" => true, "data" => $data);
            } else {
                return array("success" => false, "error" => "id_not_found");
            }
        }

        
        if($this->get[$table]["user_row"]["permission"] != null) {
            if(!$this->auth->isUserPermittedTo($this->get[$table]["user_row"]["permission"])) {
                return array("success" => false, "error" => "user_permission_denied");
            }
        }

       
        $r = db::select("SELECT * FROM `$table` WHERE `$column`=? AND `{$this->get[$table]['user_row']['column']}`=?", array($id, $this->auth->user($this->get[$table]['user_row']['user'])));
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

    public function create($table) {
        if(!$this->checkLogin()) return $this->checkLogin();

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
            foreach ($this->create[$table]["columns"] as $key) {
                if($c != "") {
                    $c .= ", ";
                    $q .= ", ";
                }
                $c .= "`{$key}`";
                $q .= "?";
                array_push($v, $request->getPost($key));
            }
            foreach ($this->create[$table]["optional"] as $key) {
                if($request->getPost($key) == null) continue;
                if($c != "") {
                    $c .= ", ";
                    $q .= ", ";
                }
                $c .= "`{$key}`";
                $q .= "?";
                array_push($v, $request->getPost($key));
            }
            if($this->create[$table]["add_user_data"] != null) {
                $c .= ", `{$this->create[$table]['add_user_data']['column']}`";
                $q .= ", ?";
                array_push($v, $this->auth->user($this->create[$table]['add_user_data']['user']));
            }
            db::query("INSERT INTO `{$table}` ({$c}) VALUES ({$q})", $v);
            return array("success" => true, "inserted_id" => db::$id);
        } else {
            return array("success" => false, "error" => "missing_values");
        }
    }

    public function update($table, $column, $id) {
        if(!$this->checkLogin()) return $this->checkLogin();
        
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
        if(!$this->checkLogin()) return $this->checkLogin();

        if(!isset($this->delete[$table])) return array("success" => false, "error" => "invalid_table");
        
        if($this->delete[$table]["permission"] != null) {
            if(!$this->auth->isUserPermittedTo($this->delete[$table]["permission"])) {
                if($this->delete[$table]["user_row"] != null) {
                    if($this->delete[$table]["user_row"]["permission"] != null) {
                        if(!$this->auth->isUserPermittedTo($this->delete[$table]["user_row"]["permission"])) {
                            return array("success" => false, "error" => "user_permission_denied");
                        }
                    }
                    if(isset($this->delete[$table]["user_row"]["column"])) $column = $this->delete[$table]["user_row"]["column"];

                    if(db::count("SELECT COUNT(*) FROM `{$table}` WHERE `{$column}`=?", array($id)) > 0) {
                        db::query("DELETE FROM `{$table}` WHERE `{$column}`=?", array($id));
                        return array("success" => true);

                    } else {
                        return array("success" => false, "error" => "id_not_found");

                    }
                }
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