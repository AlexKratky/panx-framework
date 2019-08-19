<?php
/**
 * @name Auth.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to authentification. Part of panx-framework.
 */

class Auth {
    private $request;
    private $authModel;
    private $id;
    private $username;
    private $email;
    private $password;
    private $verified;
    private $verify_key;
    private $created_at;
    private $edited_at;
    private $two_auth_enabled;
    private $twoFA;

    public function __construct($logout = false) {
        $this->request = $GLOBALS["request"];
        $this->authModel = new AuthModel();
        require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";
        $this->twoFA = new PragmaRX\Google2FAQRCode\Google2FA();
        if(!$logout) {
            if(!empty($_SESSION["username"]) && !empty($_SESSION["password"])) {
                $this->login($_SESSION["username"], $_SESSION["password"]);
            }
        }
    }

    public function isLogined() {
        if(!empty($this->id)) {
            return true;
        }
        return false;
    }

    public function login($username = null, $password = null, $r = false) {
        if($this->loginFromCookies()) {
            return true;
        }
        $login_from_session = true;
        if($username === null || $password === null) {
            if($this->request->getPost('username') !== null && $this->request->getPost('password') !== null) {
                $username = $this->request->getPost('username');
                $password = $this->request->getPost('password');
                if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
                    $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
                    //$this->captchaFailed();
                    return false;
                }
                $login_from_session = false;
            } else {
                //$this->captchaFailed();
                return false;
            }
        }
        if($this->authModel->verifyLogin($username, $password, $login_from_session)) {

            $data = $this->authModel->loadData($username);
            if(!$this->authModel->isEnabled2FA($data["ID"]) || $this->request->getPost('2fa_code') !== null || (isset($_SESSION["2fa_passed"]) && $_SESSION["2fa_passed"] == true)) {
                $twofacheck = false;
                if($this->request->getPost('2fa_code') !== null) {
                    //validate code
                    if (!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
                        $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
                        //$this->captchaFailed();
                        return false;
                    }

                    $secret = $this->authModel->get2FASecret($data["ID"]);
                    if (!$this->twoFA->verifyKey($secret, $this->request->getPost('2fa_code'))) {
                        $_SESSION["AUTH_ERROR"] = "Invalid 2FA code.";
                        //$this->captchaFailed();
                        return false;
                    } else {
                        $_SESSION["2fa_passed"] = true;
                        $twofacheck = true;

                    }
                }

                $this->id = $data["ID"];
                $this->username = $data["USERNAME"];
                $this->email = $data["EMAIL"];
                $this->verified = $data["VERIFIED"];
                $this->verify_key = $data["VERIFY_KEY"];
                $this->created_at = $data["CREATED_AT"];
                $this->edited_at = $data["EDITED_AT"];
                $this->two_auth_enabled = $this->authModel->isEnabled2FA($data["ID"]);
                if((!$login_from_session && $this->request->getPost('remember') === "on") || ($twofacheck && $r && isset($_SESSION["remember_login"]) && $_SESSION["remember_login"] = true)) {
                    $token = $this->authModel->updateRememberToken($data["ID"]);
                    setcookie("REMEMBER_TOKEN", $token, time() + 86400 * 30, "/", "", false, true);
                    setcookie("USERNAME", $data["USERNAME"], time() + 86400 * 30, "/", "", false, true);
                } else {
                    //dump($_SESSION["remember_login"] == true);
                }
                $_SESSION["username"] = $data["USERNAME"];
                $_SESSION["password"] = $data["PASSWORD"];
                //$this->captchaPassed();
                return true;
            } else {
                $_SESSION["username"] = $data["USERNAME"];
                $_SESSION["remember_login"] = $this->request->getPost('remember') == "on";
                //dump($_SESSION["remember_login"]);
                $_SESSION["password"] = $data["PASSWORD"];
                $_SESSION["2fa_passed"] = false;
                if($GLOBALS["request"]->getUrl()->getLink()[1] != "login-2fa" && $GLOBALS["request"]->getUrl()->getLink()[1] != "logout") {
                    redirect('/login-2fa');
                }
            }
        } else {
            $_SESSION["remember_login"] = null;
            $_SESSION["username"] = null;
            $_SESSION["password"] = null;
            $_SESSION["AUTH_ERROR"] = "Username or password is invalid";
            //$this->captchaFailed();
            return false;
        }
    }

    public function loginFromCookies() {
        //dump($this->authModel->loginFromCookies());
        //return false;
        if($this->authModel->loginFromCookies()) {
            $data = $this->authModel->loadData($_COOKIE["USERNAME"]);
            $this->id = $data["ID"];
            $this->username = $data["USERNAME"];
            $this->email = $data["EMAIL"];
            $this->verified = $data["VERIFIED"];
            $this->verify_key = $data["VERIFY_KEY"];
            $this->created_at = $data["CREATED_AT"];
            $this->edited_at = $data["EDITED_AT"];
            $this->two_auth_enabled = $this->authModel->isEnabled2FA($data["ID"]);
            $_SESSION["username"] = $data["USERNAME"];
            $_SESSION["password"] = $data["PASSWORD"];
            return true;
        } else {
            return false;
        }
    }

    public function twoFactorAuthData() {
        if(empty($_SESSION["2fa_secret"])) {
            $secret = $this->twoFA->generateSecretKey();
            $_SESSION["2fa_secret"] = $secret;
        } else {
            $secret = $_SESSION["2fa_secret"];
        }
        //$this->twoFA->setAllowInsecureCallToGoogleApis(true);

        $url = $this->twoFA->getQRCodeInline(
            $GLOBALS["CONFIG"]["basic"]["APP_NAME"],
            $this->username,
            $secret
        );

        return [$secret, $url];
    }

    public function save2FA() {
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
            //$this->captchaFailed();
            return false;
        }
        $secret = $_SESSION["2fa_secret"];
        if (!$this->twoFA->verifyKey($secret, $this->request->getPost('code'))) {
            $_SESSION["AUTH_ERROR"] = "Invalid 2FA code.";
            //$this->captchaFailed();
            return false;
        } else {
            $this->authModel->setUp2FA($this->id, $secret);
            $_SESSION["2fa_passed"] = true;
            //$this->captchaPassed();
            return true;
        }
    }

    public function disable2FA() {
        $this->authModel->disable2FA($this->id);
    }

    public function register() {
        if(!$this->request->workWith("POST", array("email", "username", "password", "accept"))) {
            //$this->captchaFailed();
            $_SESSION["AUTH_ERROR"] = "Please enter all data.";
            return false;
        }
        if (!filter_var($this->request->getPost('email'), FILTER_VALIDATE_EMAIL)) {
            $_SESSION["AUTH_ERROR"] = "Invalid email";
            //$this->captchaFailed();
            return false;
        }
        if(!ctype_alnum($this->request->getPost('username')) || strlen($this->request->getPost('username')) < 4) {
            $_SESSION["AUTH_ERROR"] = "The username can be only alphanumeric and must be atleast 4 characters long";
            //$this->captchaFailed();
            return false;
        }
        if(strlen($this->request->getPost('password')) < 6) {
            $_SESSION["AUTH_ERROR"] = "Password must be atleast 6 characters long";
            //$this->captchaFailed();
            return false;
        }
        if($this->request->getPost('accept') != "on") {
            $_SESSION["AUTH_ERROR"] = "You must check the agreement";
            //$this->captchaFailed();
            return false;
        }
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
            //$this->captchaFailed();            
            return false;
        }
        if(!$this->authModel->checkName($this->request->getPost('username'))) {
            $_SESSION["AUTH_ERROR"] = "The username is already taken";
            //$this->captchaFailed();
            return false;
        }
        if(!$this->authModel->checkMail($this->request->getPost('email'))) {
            $_SESSION["AUTH_ERROR"] = "The email is already taken";
            //$this->captchaFailed();
            return false;
        }
        $p = $this->authModel->register($this->request->getPost('email'), $this->request->getPost('username'), $this->request->getPost('password'));
        $_SESSION["username"] = strtolower($this->request->getPost('username'));
        $_SESSION["password"] = $p;
        //$this->captchaPassed();
        return true;
    }

    public function edit() {
        $mail = strtolower($this->request->getPost('email'));
        $user = strtolower($this->request->getPost('username'));
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
            //$this->captchaFailed();
            return false;
        }
        if($mail !== $this->email) {
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION["AUTH_ERROR"] = "Invalid email";
                //$this->captchaFailed();
                return false;
            }
            if(!$this->authModel->checkMail($mail)) {
                $_SESSION["AUTH_ERROR"] = "The email is already taken";
                //$this->captchaFailed();
                return false;
            }
        }
        if($user !== $this->username) {
            if(!ctype_alnum($user) || strlen($user) < 4) {
                $_SESSION["AUTH_ERROR"] = "The username can be only alphanumeric and must be atleast 4 characters long";
                //$this->captchaFailed();
                return false;
            }
            if(!$this->authModel->checkName($user)) {
                $_SESSION["AUTH_ERROR"] = "The username is already taken";
                //$this->captchaFailed();
                return false;
            }
        }
        $password = $this->request->getPost('newpassword');
        if(($password !== null && $password != "") && strlen($password) < 6) {
            $_SESSION["AUTH_ERROR"] = "The password must be atleast 6 characters long";
            //$this->captchaFailed();
            return false;
        }
        if($password == "") {
            $password = null;
        }
        if(!password_verify($this->request->getPost('password'), $_SESSION["password"])) {
            $_SESSION["AUTH_ERROR"] = "The entered current password is incorrect";
            //$this->captchaFailed();
            return false;
        }
        $p = $this->authModel->edit($this->id, $mail, $user, $password, ($this->request->getPost('email') !== $this->email));
        $this->username = $user;
        $this->email = $mail;
        if($p !== null) {
            $this->password = $p;
            $_SESSION["password"] = $p;
        }
        $_SESSION["username"] = $user;
        $_SESSION["AUTH_ERROR"] = "Profile has been updated";
        return true;
        //$this->captchaPassed();
    }

    public static function displayError() {
        if(!empty($_SESSION["AUTH_ERROR"])) {
            $e = $_SESSION["AUTH_ERROR"];
            $_SESSION["AUTH_ERROR"] = null;
            return $e;
        }
        return "";
    }

    public function logout() {
        $_SESSION["username"] = null;
        $_SESSION["password"] = null;
        session_destroy();
        setcookie("PHPSESSID", null, -1, "/");
        setcookie("USERNAME", null, -1, "/");
        $t = null;
        if($GLOBALS["request"]->getQuery("soft") == "true")
            $t = $_COOKIE["REMEMBER_TOKEN"] ?? null;
        setcookie("REMEMBER_TOKEN", null, -1, "/");
        $this->authModel->clearTokens($this->id, $t);
        redirect($GLOBALS["CONFIG"]["auth"]["LOGOUT_PAGE"]);
        exit();
    }

    public function verify($token) {
        return $this->authModel->verify($this->id, $token);
    }

    public function forgot() {
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
            //$this->captchaFailed();
            return false;
        }
        $mail = strtolower($this->request->getPost('email'));
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["AUTH_ERROR"] = "Invalid email";
            //$this->captchaFailed();
            return false;
        }
        $this->authModel->forgot($mail);
        $_SESSION["AUTH_ERROR"] = "The email with reset link was send, please check your email in few moments. Also check spam folder.";
        //$this->captchaPassed();
        return true;
    }

    public function forgotSave() {
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = "Invalid recaptcha";
            //$this->captchaFailed();
            return false;
        }
        $password = $this->request->getPost('password');
        if(($password !== null && $password != "") && strlen($password) < 6) {
            $_SESSION["AUTH_ERROR"] = "The password must be atleast 6 characters long";
            //$this->captchaFailed();
            return false;
        }
        $mail = strtolower($this->request->getPost('email'));
        $x = $this->authModel->forgotSave($mail, $password, Route::getValue('TOKEN'));
        if($x) {
            $_SESSION["AUTH_ERROR"] = "The password was reset. You can now login.";
            //$this->captchaPassed();
            return true;
        } else {
            $_SESSION["AUTH_ERROR"] = "The combination of token and email is invalid.";
            //$this->captchaFailed();
            return false;
        }
    }

    public function user($data) {
        $data = strtolower($data);
        switch ($data) {
            case 'name':
            case 'username':
                return $this->username;
                break;
            case 'id': 
                return $this->id;
                break;
            case 'email':
            case 'mail':
                return $this->email;
                break;
            case 'verified':
                return ($this->verified === "0" ? false : true);
                break;
            case 'verify_key':
                return $this->verify_key;
                break;
            case 'created_at':
                return $this->created_at;
                break;
            case 'edited_at':
                return $this->edited_at;
                break;
            case '2fa':
                return $this->two_auth_enabled;
                break;
        }
    }

    public function validRecaptcha($token) {
        if($this->isCaptchaNeeded() === false) {
            return true;
        }
        require $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
        $client = new GuzzleHttp\Client();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params' =>
                [
                    'secret' => $GLOBALS["CONFIG"]["auth"]["GOOGLE_RECAPTCHA_SECRET"],
                    'response' => $token,
                ],
            ]
        );

        $body = json_decode((string) $response->getBody());
        return $body->success;
    }

    public function captchaFailed() {
        $this->authModel->captchaFailed($_SERVER['REMOTE_ADDR']);
    }

    public function isCaptchaNeeded() {
        return $this->authModel->isCaptchaNeeded($_SERVER['REMOTE_ADDR']);
    }

    public function captchaPassed() {
        $this->authModel->captchaPassed($_SERVER['REMOTE_ADDR']);
    }
}