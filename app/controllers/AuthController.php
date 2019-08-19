<?php
/**
 * @name AuthController.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Authentification controller.
 */

class AuthController
{
    private static $handler;
    private static $authModel;
    private static $auth;

    public static function main($handler) {
        self::$handler = $handler;
        self::$authModel = new AuthModel();
        self::$auth = $GLOBALS["auth"];
        if (isset($GLOBALS["request"]->getUrl()->getLink()[1])) {
            switch($GLOBALS["request"]->getUrl()->getLink()[1]) {
                case 'login':
                    self::login();  
                    break;
                case 'register':
                    self::register();
                    break;
                case 'edit':
                    self::edit();
                    break;
                case '2fa':
                    self::twoFA();
                    break;
                case 'login-2fa':
                    self::twoFAForm();
                    break;
            }
        }
    }

    public static function login() {
        if(self::$auth->isLogined()) {
            redirect($GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"]);
        }
        if(self::$auth->loginFromCookies()) {
            redirect($GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"]);
        }
        self::$handler::setParameters([
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
        ]);

    }

    public static function register() {
        if(self::$auth->isLogined()) {
            redirect($GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"]);
        }
        self::$handler::setParameters([
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
        ]);
    }

    public static function edit() {
        //self::$handler::setParameters(self::$authModel->selectFromDb());
        if(!self::$auth->isLogined()) {
            redirect("/login");
        }
        self::$handler::setParameters([
            'name'=>self::$auth->user('name'),
            'mail'=>self::$auth->user('mail'),
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
            'twofa'=>self::$auth->user("2fa")
        ]);
    }

    public static function twoFA() {
        if(!self::$auth->isLogined()) {
            redirect("/login");
        }
        if(self::$auth->user("2fa")) {
            redirect('/edit');
        }
        $x = self::$auth->twoFactorAuthData();
        self::$handler::setParameters([
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
            'url_code'=>$x[1],
            'secret'=>$x[0]
        ]);
    }

    public static function twoFAForm() {
        if(!empty($_SESSION["username"]) && !empty($_SESSION["password"])) {
            self::$handler::setParameters([
                'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
            ]);
        } else {
            redirect('/login');
        }
    }
}
