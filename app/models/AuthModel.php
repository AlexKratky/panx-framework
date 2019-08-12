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
    public function __construct() {

    }
    
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

    public function clearTokens($id) {
        db::query("DELETE FROM users_tokens WHERE `USER_ID`=?", array($id));
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
}
