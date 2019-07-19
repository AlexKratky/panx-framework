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
    }

    public static function register() {
        if(self::$auth->isLogined()) {
            redirect($GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"]);
        }
    }

    public static function edit() {
        //self::$handler::setParameters(self::$authModel->selectFromDb());
        if(!self::$auth->isLogined()) {
            redirect("/login");
        }
        self::$handler::setParameters([
            'name'=>self::$auth->user('name'),
            'mail'=>self::$auth->user('mail')
        ]);

    }
}
