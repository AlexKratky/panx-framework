<?php
/**
 * @name AuthModel.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Authentification model.
 */

class AuthModel
{
    public function verifyLogin($username, $password, $login_from_session = false) {
        $username = strtolower($username);
        $p = db::select("SELECT `PASSWORD` FROM users WHERE `USERNAME`=?", array($username));
        if(!$login_from_session) {
            return password_verify($password, $p["PASSWORD"]);
        } else {
            return ($password == $p["PASSWORD"]);
        }
    }

    public function loadData($username) {
        $username = strtolower($username);
        return db::select("SELECT * FROM users WHERE `USERNAME`=?", array($username));
    }

    public function getRoleName($roleID) {
        return db::select("SELECT NAME_OF_ROLE FROM roles WHERE `VALUE`=?", array($roleID))["NAME_OF_ROLE"];
    }

    public function checkName($username) {
        $username = strtolower($username);
        return (db::count("SELECT COUNT(*) FROM `users` WHERE `USERNAME`=?", array($username)) == 0 ? true : false);
    }

    public function checkMail($mail) {
        $mail = strtolower($mail);
        return (db::count("SELECT COUNT(*) FROM `users` WHERE `EMAIL`=?", array($mail)) == 0 ? true : false);
    }

    public function register($mail, $user, $pass) {
        $mail = strtolower($mail);
        $user = strtolower($user);
        $pass = password_hash($pass, PASSWORD_BCRYPT);
        $verify_key = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 8);
        db::query("INSERT INTO `users` (`USERNAME`, `EMAIL`, `PASSWORD`, `VERIFY_KEY`, `CREATED_AT`) VALUES (?, ?, ?, ?, ?)", array($user, $mail, $pass, $verify_key, date("Y-m-d H:i:s",time())));
        $mailer = new Mail();
        $mailer->subject('Verify your email');
        $address = $GLOBALS["CONFIG"]["basic"]["APP_URL"]."verifymail/$verify_key";
        $mailer->message('Verify your email on address <a href="'.$address.'">'.$address.'</a>. You need to be logged in to verify your email.');
        $mailer->send($mail);
        return $pass;
    }

    public function updateRememberToken($id) {
        $token = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(64))), 0, 63);
        db::query("INSERT INTO users_tokens (`USER_ID`, `TOKEN`) VALUES (?, ?)", array($id, $token));
        return $token;
    }

    public function loginFromCookies() {
        if(empty($_COOKIE["USERNAME"]) || empty($_COOKIE["REMEMBER_TOKEN"])) {
            return false;
        }
        return (db::count("SELECT COUNT(*) FROM `users_tokens` WHERE `USER_ID`=(SELECT ID FROM users WHERE `USERNAME`=?) AND `TOKEN`=?", array($_COOKIE["USERNAME"], $_COOKIE["REMEMBER_TOKEN"])) == 0 ? false : true);
    }

    public function clearTokens($id, $t = null) {
        if($t === null) {
            db::query("DELETE FROM users_tokens WHERE `USER_ID`=?", array($id));
        } else {
            db::query("DELETE FROM users_tokens WHERE `USER_ID`=? AND `TOKEN`=?", array($id, $t));            
        }
    }

    public function edit($id, $mail,$user,$newpass,$was_email_changed) {
        if($was_email_changed) {
            $verify_key = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 8);
            if($newpass !== null) {
                $newpass = password_hash($newpass, PASSWORD_BCRYPT);
                db::query("UPDATE `users` SET `EMAIL`=?, `USERNAME`=?, `PASSWORD`=?, `VERIFY_KEY`=?, `VERIFIED`=0 WHERE ID=?", array($mail, $user, $newpass, $verify_key, $id));
            } else {
                db::query("UPDATE `users` SET `EMAIL`=?, `USERNAME`=?, `VERIFY_KEY`=?, `VERIFIED`=0 WHERE ID=?", array($mail, $user, $verify_key, $id));
            }
            $mailer = new Mail();
            $mailer->subject('Verify your email');
            $address = $GLOBALS["CONFIG"]["basic"]["APP_URL"]."verifymail/$verify_key";
            $mailer->message('Verify your email on address <a href="'.$address.'">'.$address.'</a>. You need to be logged in to verify your email.');
            $mailer->send($mail);
        } else {
            if($newpass !== null) {
                $newpass = password_hash($newpass, PASSWORD_BCRYPT);
                db::query("UPDATE `users` SET `EMAIL`=?, `USERNAME`=?, `PASSWORD`=? WHERE ID=?", array($mail, $user, $newpass, $id));
            } else {
                db::query("UPDATE `users` SET `EMAIL`=?, `USERNAME`=? WHERE ID=?", array($mail, $user, $id));
            }
        }
        return $newpass;
    }

    public function addPermission($user_id, $permission) {
        if(!$this->havePermission($user_id, $permission)) {
            if(strlen(db::select("SELECT `PERMISSIONS` FROM `users` WHERE ID=?", array($user_id))["PERMISSIONS"]) < 1) {
                db::query("UPDATE `users` SET `PERMISSIONS`=CONCAT(`PERMISSIONS`, ?) WHERE ID=?", array($permission, $user_id));
            } else {
                db::query("UPDATE `users` SET `PERMISSIONS`=CONCAT(`PERMISSIONS`, ?) WHERE ID=?", array("|".$permission, $user_id));
            }
            return true;
        }
        return false;
    }

    public function removePermission($user_id, $permission) {
        db::query("UPDATE `users` SET `PERMISSIONS`=replace(replace(`PERMISSIONS`, ?, ''), ?, '')  WHERE ID=?", array($permission."|", "|".$permission, $user_id));
    }

    public function havePermission($user_id, $permission) {
        return (strpos(db::select("SELECT `PERMISSIONS` FROM `users` WHERE ID=?", array($user_id))["PERMISSIONS"], $permission) !== false);
    }

    public function createPermission($permission) {
        db::query("INSERT INTO `permissions` (`PERMISSION_NAME`) VALUES (?)", array($permission));
    }

    public function deletePermission($permission) {
        db::query("DELETE FROM `permissions` WHERE ID=?", array($permission));
    }

    public function setPermissions($user_id, $permissions) {
        db::query("UPDATE `users` SET `PERMISSIONS`=? WHERE ID=?", array($permissions, $user_id));
    }

    public function setRole($user_id, $role_name) {
        db::query("UPDATE `users` SET `ROLE`=? WHERE ID=?", array($role_name, $user_id));
    }

    public function setRoleByName($user_name, $role_name) {
        $user_name = strtolower($user_name);
        if(!$this->checkName($user_name)) {
            // username exists
            db::query("UPDATE `users` SET `ROLE`=? WHERE USERNAME=?", array($role_name, $user_name));
            return true;
        }
        return false;
    }

    public function createRole($role_name, $value, $landing) {
        $landing = (strlen($landing) < 1 ? null : $landing);
        db::query("INSERT INTO roles (`NAME_OF_ROLE`, `VALUE`, `LANDING_PAGE`) VALUES (?, ?, ?)", array($role_name, $value, $landing));
    }

    public function deleteRole($value) {
        db::query("DELETE FROM `roles` WHERE `VALUE`=?", array($value));
    }

    public function isMainAdminSet() {
        return (db::count("SELECT COUNT(*) FROM `users` WHERE `ROLE`=1") > 0);
    }

    public function getLandingPage($role) {
        return (db::select("SELECT `LANDING_PAGE` FROM `roles` WHERE `VALUE`=?", array($role))["LANDING_PAGE"]);
    }

    public function verify($id, $token) {
        $t = db::select("SELECT `VERIFY_KEY` FROM `users` WHERE ID=?", array($id));
        if($t["VERIFY_KEY"] === $token) {
            db::query("UPDATE `users` SET `VERIFIED`=1 WHERE ID=?", array($id));
            return true;
        } else {
            return false;
        }
    }

    public function isEnabled2FA($id) {
        return (db::count("SELECT COUNT(*) from `users_2fa` WHERE `USER_ID`=?", array($id)) > 0 ? true : false);
    }

    public function setUp2FA($id, $secret) {
        db::query("INSERT INTO users_2fa (`USER_ID`, `SECRET`) VALUES (?, ?)", array($id, $secret));
    }

    public function disable2FA($id) {
        db::query("DELETE FROM users_2fa WHERE `USER_ID`=?", array($id));
    }

    public function get2FASecret($id) {
        $x = db::select("SELECT `SECRET` FROM `users_2fa` WHERE `USER_ID`=?", array($id));
        return $x["SECRET"];
    }

    public function forgot($mail) {
        if(db::count("SELECT COUNT(*) FROM `users` WHERE EMAIL=?", array($mail)) > 0) {
            $token = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(64))), 0, 63);
            db::query("UPDATE `users` SET `FORGOT_TOKEN`=? WHERE EMAIL=?", array($token, $mail));
            $mailer = new Mail();
            $mailer->subject('Reset your password');
            $address = $GLOBALS["CONFIG"]["basic"]["APP_URL"] . "login/forgot-password/$token/";
            $mailer->message('Reset your password at <a href="' . $address . '">' . $address . '</a>. If you didn\'t requested the reset of password, just ignore the email.');
            $mailer->send($mail);

        }
    }

    public function forgotSave($mail, $pass, $token) {
        if(db::count("SELECT COUNT(*) FROM `users` WHERE EMAIL=? AND `FORGOT_TOKEN`=?", array($mail, $token)) > 0) {
            $pass = password_hash($pass, PASSWORD_BCRYPT);
            db::query("UPDATE `users` SET `FORGOT_TOKEN`=NULL, `PASSWORD`=? WHERE EMAIL=?", array($pass, $mail));
            return true;
        } else {
            return false;
        }
    }

    public function captchaFailed($ip) {
        if(db::count("SELECT COUNT(*) FROM `recaptcha_fails` WHERE `IP`=?", array($ip)) < 1) {
            db::query("INSERT INTO `recaptcha_fails` (`IP`) VALUES (?)", array($ip));
        }
    }

    public function isCaptchaNeeded($ip) {
        return (db::count("SELECT COUNT(*) FROM `recaptcha_fails` WHERE `IP`=?", array($ip)) > 0 ? true : false);
    }

    public function captchaPassed($ip) {
        db::query("DELETE FROM recaptcha_fails WHERE `IP`=?", array($ip));
    }

    public function verifyLoginToken($token, $time = 300) {
        if(db::count("SELECT COUNT(*) FROM `login_tokens` WHERE `TOKEN`=?", array($token)) > 0) {
            $t = db::select("SELECT * FROM `login_tokens` WHERE `TOKEN`=?", array($token));
            //check time
            if(time() < (strtotime($t["CREATED_AT"]) + $time)) {
                return true;
            }
        }
        return false;
    }

    public function loadDataFromLoginToken($token) {
        if(db::count("SELECT COUNT(*) FROM `login_tokens` WHERE `TOKEN`=?", array($token)) > 0) {
            $x = db::select("SELECT * FROM `login_tokens` WHERE `TOKEN`=?", array($token));
            db::query("UPDATE `login_tokens` SET `CREATED_AT`=CURRENT_TIMESTAMP() WHERE `TOKEN`=?", array($token));
            return db::select("SELECT * FROM `users` WHERE `ID`=?", array($x["USER_ID"]));
        }
        return false;
    }
}
