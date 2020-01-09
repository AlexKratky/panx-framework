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

declare(strict_types=1);

class Auth {
    /**
     * @var Request
     */
    private $request;
    /**
     * @var AuthModel
     */
    private $authModel;
    /**
     * @var int The user's ID.
     */
    private $id;
    /**
     * @var string The user's username.
     */
    private $username;
    /**
     * @var string The user's email.
     */
    private $email;
    /**
     * @var string The user's password as hash.
     */
    private $password;
    /**
     * @var bool Determines if the user has verified email or not.
     */
    private $verified;
    /**
     * @var string The user's verify key.
     */
    private $verify_key;
    /**
     * @var int The user's creation timestamp.
     */
    private $created_at;
    /**
     * @var int The user's editiation timestamp.
     */
    private $edited_at;
    /**
     * @var int Determines if the user has 2FA or not.
     */
    private $two_auth_enabled;
    /**
     * @var PragmaRX\Google2FAQRCode\Google2FA
     */
    private $twoFA;
    /**
     * @var int The user's role.
     */
    private $role;
    /**
     * @var string The user's role name.
     */
    private $role_name;
    /**
     * @var string The user's permissions.
     */
    private $permissions;


    /**
     * Create a instance of Auth.
     * @param bool $logout If sets to false, it will try to login the user.
     */
    public function __construct(bool $logout = false) {
        $this->request = $GLOBALS["request"];
        $this->authModel = new AuthModel();
        $this->twoFA = new PragmaRX\Google2FAQRCode\Google2FA();
        if(!$logout) {
            if(!empty($_SESSION["username"]) && !empty($_SESSION["password"])) {
                $this->login($_SESSION["username"], $_SESSION["password"]);
            }
        }
    }

    /**
     * @return bool Returns true if the user is logined (and if he has 2FA enabled, then he will need to enter the 2FA code to be 'logined'), false otherwose.
     */
    public function isLogined(): bool {
        if(!empty($this->id)) {
            return true;
        }
        return false;
    }

    /**
     * Tries to login user, if the user have 2fa enabled, redirects to alias 'login-2fa'.
     * @param string|null $username The username from session or null,
     * @param string|null $password The password from session or null,
     * @param bool $r Determines if the user want to remember login.
     * @return bool Returns true if the user was logined, false otherise.
     */
    public function login(?string $username = null, ?string $password = null, bool $r = false) {
        if($this->loginFromCookies()) {
            return true;
        }
        $login_from_session = true;
        if($username === null || $password === null) {
            if($this->request->getPost('username') !== null && $this->request->getPost('password') !== null) {
                $username = $this->request->getPost('username');
                $password = $this->request->getPost('password');
                if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
                    $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
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
                    // TODO on invalid code, the error is invalid recaptcha, so its disabled until fix
                    /*if (!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
                        $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
                        //$this->captchaFailed();
                        return false;
                    }*/

                    $secret = $this->authModel->get2FASecret($data["ID"]);
                    if (!$this->twoFA->verifyKey($secret, $this->request->getPost('2fa_code'))) {
                        $_SESSION["AUTH_ERROR"] = __("auth.invalid2FA", true);
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
                $this->role = $data["ROLE"];
                $this->role_name = $this->authModel->getRoleName($data["ROLE"]);
                $this->permissions = $data["PERMISSIONS"];
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
                    aliasredirect("login-2fa");
                }
            }
        } else {
            $_SESSION["remember_login"] = null;
            $_SESSION["username"] = null;
            $_SESSION["password"] = null;
            $_SESSION["AUTH_ERROR"] = __("auth.invalidUsernameOrPass", true);
            //$this->captchaFailed();
            return false;
        }
    }

    /**
     * Tries to login user from cookies.
     * @return bool Returns true if the user was successfully logined from cookies, false otherwise.
     */
    public function loginFromCookies(): bool {
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
            $this->role = $data["ROLE"];
            $this->role_name = $this->authModel->getRoleName($data["ROLE"]);
            $this->permissions = $data["PERMISSIONS"];
            $this->two_auth_enabled = $this->authModel->isEnabled2FA($data["ID"]);
            $_SESSION["username"] = $data["USERNAME"];
            $_SESSION["password"] = $data["PASSWORD"];
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array Returns the array containing the data about 2FA. [0] => SECRET; [1] => URL of QR code.
     */
    public function twoFactorAuthData(): array {
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

    /**
     * Setup the 2FA for user, if he entered a valid code.
     * @return bool Returns false if the 2FA wasnt set up, true otherwise.
     */
    public function save2FA(): bool {
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
            //$this->captchaFailed();
            return false;
        }
        $secret = $_SESSION["2fa_secret"];
        if (!$this->twoFA->verifyKey($secret, $this->request->getPost('code'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalid2FA", true);
            //$this->captchaFailed();
            return false;
        } else {
            $this->authModel->setUp2FA($this->id, $secret);
            $_SESSION["2fa_passed"] = true;
            //$this->captchaPassed();
            return true;
        }
    }

    /**
     * Disable the 2FA for user.
     */
    public function disable2FA() {
        $this->authModel->disable2FA($this->id);
    }

    /**
     * Tries to register the user. Work with POST - email; username; password; accept
     * @return bool Returns true, if the user is registered, false otherwise.
     */
    public function register(): bool {
        if(!$this->request->workWith("POST", array("email", "username", "password", "accept"))) {
            //$this->captchaFailed();
            $_SESSION["AUTH_ERROR"] = __("auth.fillAllData", true);
            return false;
        }
        if (!filter_var($this->request->getPost('email'), FILTER_VALIDATE_EMAIL)) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidMail", true);
            //$this->captchaFailed();
            return false;
        }
        if(!ctype_alnum($this->request->getPost('username')) || strlen($this->request->getPost('username')) < 4) {
            $_SESSION["AUTH_ERROR"] = __("auth.usernameError", true);
            //$this->captchaFailed();
            return false;
        }
        if(strlen($this->request->getPost('password')) < 6) {
            $_SESSION["AUTH_ERROR"] = __("auth.passwordError", true);
            //$this->captchaFailed();
            return false;
        }
        if($this->request->getPost('accept') != "on") {
            $_SESSION["AUTH_ERROR"] = __("auth.agreementError", true);
            //$this->captchaFailed();
            return false;
        }
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
            //$this->captchaFailed();            
            return false;
        }
        if(!$this->authModel->checkName($this->request->getPost('username'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.usernameTaken", true);
            //$this->captchaFailed();
            return false;
        }
        if(!$this->authModel->checkMail($this->request->getPost('email'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.mailTaken", true);
            //$this->captchaFailed();
            return false;
        }
        $p = $this->authModel->register($this->request->getPost('email'), $this->request->getPost('username'), $this->request->getPost('password'));
        $_SESSION["username"] = strtolower($this->request->getPost('username'));
        $_SESSION["password"] = $p;
        //$this->captchaPassed();
        return true;
    }

    /**
     * Tries to saves the new data of user.
     * @return bool Returns true if the data was saved, false otherwise (e.g. wrong password, email is already taken etc.)
     */
    public function edit(): bool {
        $mail = strtolower($this->request->getPost('email'));
        $user = strtolower($this->request->getPost('username'));
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
            //$this->captchaFailed();
            return false;
        }
        if($mail !== $this->email) {
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $_SESSION["AUTH_ERROR"] = __("auth.invalidMail", true);
                //$this->captchaFailed();
                return false;
            }
            if(!$this->authModel->checkMail($mail)) {
                $_SESSION["AUTH_ERROR"] = __("auth.mailTaken", true);
                //$this->captchaFailed();
                return false;
            }
        }
        if($user !== $this->username) {
            if(!ctype_alnum($user) || strlen($user) < 4) {
                $_SESSION["AUTH_ERROR"] = __("auth.usernameError", true);
                //$this->captchaFailed();
                return false;
            }
            if(!$this->authModel->checkName($user)) {
                $_SESSION["AUTH_ERROR"] = __("auth.usernameTaken", true);
                //$this->captchaFailed();
                return false;
            }
        }
        $password = $this->request->getPost('newpassword');
        if(($password !== null && $password != "") && strlen($password) < 6) {
            $_SESSION["AUTH_ERROR"] = __("auth.passwordError", true);
            //$this->captchaFailed();
            return false;
        }
        if($password == "") {
            $password = null;
        }
        if(!password_verify($this->request->getPost('password'), $_SESSION["password"])) {
            $_SESSION["AUTH_ERROR"] = __("auth.currentPasswordError", true);
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
        $_SESSION["AUTH_ERROR"] = __("auth.profileUpdated", true);
        return true;
        //$this->captchaPassed();
    }

    /**
     * @return string Returns error string or empty string.
     */
    public static function displayError(): string {
        if(!empty($_SESSION["AUTH_ERROR"])) {
            $e = $_SESSION["AUTH_ERROR"];
            $_SESSION["AUTH_ERROR"] = null;
            return $e;
        }
        return "";
    }

    /**
     * Logout user and redirect to $GLOBALS["CONFIG"]["auth"]["LOGOUT_PAGE"] and exit() executing.
     */
    public function logout() {
        $_SESSION["username"] = null;
        $_SESSION["password"] = null;
        session_destroy();
        setcookie("PHPSESSID", "", -1, "/");
        setcookie("USERNAME", "", -1, "/");
        $t = null;
        if($GLOBALS["request"]->getQuery("soft") == "true")
            $t = $_COOKIE["REMEMBER_TOKEN"] ?? null;
        setcookie("REMEMBER_TOKEN", "", -1, "/");
        $this->authModel->clearTokens($this->id, $t);
        redirect($GLOBALS["CONFIG"]["auth"]["LOGOUT_PAGE"]);
        exit();
    }

    /**
     * Verify the user email by token.
     * @param string $token.
     * @return bool Returns true if the mail was verified, false otherwise.
     */
    public function verify(string $token): bool {
        return $this->authModel->verify($this->id, $token);
    }

    /**
     * Sends the email with reset link.
     * @return bool Returns true if the mail was sent, false otherwise.
     */
    public function forgot(): bool {
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
            //$this->captchaFailed();
            return false;
        }
        $mail = strtolower($this->request->getPost('email'));
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidMail", true);
            //$this->captchaFailed();
            return false;
        }
        $this->authModel->forgot($mail);
        $_SESSION["AUTH_ERROR"] = __("auth.resetPassword", true);
        //$this->captchaPassed();
        return true;
    }

    /**
     * Saves the new password.
     * @return bool Returns true if the password was reset, false otherwise.
     */
    public function forgotSave(): bool {
        if(!$this->validRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidRecaptcha", true);
            //$this->captchaFailed();
            return false;
        }
        $password = $this->request->getPost('password');
        if(($password !== null && $password != "") && strlen($password) < 6) {
            $_SESSION["AUTH_ERROR"] = __("auth.passwordError", true);
            //$this->captchaFailed();
            return false;
        }
        $mail = strtolower($this->request->getPost('email'));
        $x = $this->authModel->forgotSave($mail, $password, Route::getValue('TOKEN'));
        if($x) {
            $_SESSION["AUTH_ERROR"] = __("auth.passwordWasReseted", true);
            //$this->captchaPassed();
            return true;
        } else {
            $_SESSION["AUTH_ERROR"] = __("auth.invalidCombination", true);
            //$this->captchaFailed();
            return false;
        }
    }

    /**
     * Returns the user specified data.
     * @param string $data The data column name, e.g. 'name', 'id', 'email', ...
     */
    public function user(string $data) {
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
            case 'role':
                return $this->role;
                break;
            case 'role_name':
                return $this->role_name;
                break;
            case 'permissions':
                return $this->permissions;
                break;
        }
    }

    /**
     * Check if user have the passed permission.
     * @param string $permission The permission name that will be checked.
     * @return bool Returns true if the user is permitted, false otherwise.
    */
    public function isUserPermittedTo(string $permission): bool {
        return (strpos($this->permissions, $permission) !== false);
    }

    /**
     * Validates recaptcha.
     * @param string $token The recaptcha code.
     * @return bool Returns true if the recaptcha is valid, false otherwise.
     */
    public function validRecaptcha(?string $token): bool {
        if($this->isCaptchaNeeded() === false) {
            return true;
        }
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

    /**
     * The captcha failed, inserts the row into `recaptcha_fails`, so the user needs to fill the recaptcha.
     */
    public function captchaFailed() {
        $this->authModel->captchaFailed($_SERVER['REMOTE_ADDR']);
    }

    /**
     * @return bool Returns true if recaptcha is needed (is collumn in `recaptcha_fails`), false otherwise.
     */
    public function isCaptchaNeeded(): bool {
        return $this->authModel->isCaptchaNeeded($_SERVER['REMOTE_ADDR']);
    }

    /**
     * The captcha passed, deletes the row from `recaptcha_fails`.
     */
    public function captchaPassed() {
        $this->authModel->captchaPassed($_SERVER['REMOTE_ADDR']);
    }
}
