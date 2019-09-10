<?php
/**
 * @name AuthMiddleware.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description The base handler, from which should every handler inherit.
 */

class Handler {
    /**
     * @var array The array of parameters, usefull for passing them into template files.
     */
    protected static $parameters = array();

    /**
     * This function will be called when the file ends with the extension which coresponding to this controller.
     * @param string $file The file path to template file. This is not full path, (for example, only 'home.latte' or 'default/exaple.latte').
     */
    public static function handle($file) {
        
    }

    /**
     * Sets the self::$parameters value to the value passed with function.
     * @param array $parameters
     */
    public static function setParameters($parameters) {
        self::$parameters = $parameters;
    }

    /**
     * Push the value to the self::$parameters attribute.
     * @param mixed $parameters
     */
    public static function addParameters($parameters) {
        array_push(self::$parameters, $parameters);
    }

    /**
     * Add the value to the self::$parameters attribute.
     * @param mixed $parameters
     */
    public static function addParameter($parameter_name, $parameter_value) {
        self::$parameters[$parameter_name] = $parameter_value;
    }
}
