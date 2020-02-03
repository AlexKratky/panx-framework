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
        if (Route::getAlias() !== null) {
            // NEED TO ADD SUPPORT FOR ALIASES ( LOAD ALIAS, THEN GET URL OF THAT ROUTE AND COMPARE THE THE LINK WITH CURRENT LINK )
            switch(Route::getAlias()) {
                case 'login':
                    self::login();  
                    break;
                case 'register':
                    self::register();
                    break;
                case 'edit':
                    self::edit();
                    break;
                case '2fa-setup':
                    self::twoFA();
                    break;
                case 'login-2fa':
                    self::twoFAForm();
                    break;
                default:
                    self::def();

            }
        }
    }

    public static function login() {
        if(self::$auth->isLogined()) {
            $am = new AuthModel();
            $l = $am->getLandingPage(self::$auth->user("role"));
            $l = ($l === null ? $GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"] : $l);
            redirect($l);
        }
        if(self::$auth->loginFromCookies()) {
            $am = new AuthModel();
            $l = $am->getLandingPage(self::$auth->user("role"));
            $l = ($l === null ? $GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"] : $l);
            redirect($l);
        }
        $form = new LoginForm();
        self::$handler::setParameters([
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
            'form' => $form->getForm()
        ]);

    }

    public static function register() {
        if(self::$auth->isLogined()) {
            $am = new AuthModel();
            $l = $am->getLandingPage(self::$auth->user("role"));
            $l = ($l === null ? $GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"] : $l);
            redirect($l);
        }
        self::$handler::setParameters([
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
        ]);
    }

    public static function edit() {
        //self::$handler::setParameters(self::$authModel->selectFromDb());
        if(!self::$auth->isLogined()) {
            aliasredirect("login");
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
            aliasredirect("login");
        }
        if(self::$auth->user("2fa")) {
            aliasredirect('edit');
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
            aliasredirect('login');
        }
    }

    public static function def() {
        self::$handler::setParameters([
            'recaptcha_needed'=>self::$auth->isCaptchaNeeded(),
        ]);
    }
}
