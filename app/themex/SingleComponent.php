<?php
/**
 * @name SingleComponent.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description The abstact class of Component.
 */

abstract class SingleComponent {
    /**
     * @var array
     */
    private $args;
    private $fn;
    private $formx;


    abstract public function __construct($args, $fn, $formx);

    /**
     * @return string The HTML code of component.
     */
    abstract public function component(): string;

    /**
     * Create HTML string with values from array.
     * @param array $args The array of argument.
     * @return string Returns html string with attributes, e.g. 'name="x" value="y" placeholder="z"' ...
     */
    public function createStringFromArgs($args, $fn, $formx) {
        if(!isset($args["type"]) || strtolower($args["type"]) != "password") {
            $c = ($formx == null) ? null : $formx->getValues();
            dump($c);
            if(isset($c[$args["name"]])) {
                dump($c[$args["name"]]);
            } else {
                $x = FormX::getFromSession($fn, $args["name"]);
            }
        } else 
            $x = null;
        $s = "name=\"".$args["name"]."\"";
        $s .= (!isset($args["type"])) ? "" : (empty($args["type"]) ? "" : " type=\"".$args["type"]."\"");
        $s .= (!isset($args["id"])) ? "" : (empty($args["id"]) ? "" : " id=\"".$args["id"]."\"");
        if($x == null)
            $s .= (!isset($args["value"])) ? "" : (empty($args["value"]) ? "" : " value=\"".$args["value"]."\"");
        else
            $s .= " value=\"".$x."\"";
        $s .= (!isset($args["placeholder"])) ? "" : (empty($args["placeholder"]) ? "" : " placeholder=\"".$args["placeholder"]."\"");
        $s .= (!isset($args["html"])) ? "" : (empty($args["html"]) ? "" : " ".$args["html"]);
        $s .= (!isset($args["required"])) ? "" : (($args["required"] == "1") ? " required data-formx-required=\"true\"" : "");
        $s .= (!isset($args["validatorRegex"])) ? "" : "data-formx-validator=\"".$args["validatorRegex"]."\"";
        $s .= " data-formx-invalid-msg=\"".($args["errorMsgNotValid"] ?? (__("form_not_valid", true, array($args["name"]), false) === false ? FormX::ERROR_NOT_VALID : __("form_not_valid", true, array($args["name"])))) . "\"";
        $s .= " data-formx-empty-msg=\"".($args["errorMsgEmpty"] ?? (__("form_error_empty", true, array($args["name"]), false) === false ? FormX::ERROR_REQUIRED : __("form_error_empty", true, array($args["name"]))))."\"";
        $s .= (!isset($args["fileSize"])) ? "" : "data-formx-file-size=\"".$args["fileSize"]."\"";
        $s .= (!isset($args["fileExtensions"])) ? "" : "data-formx-file-extensions=\"".$args["fileExtensions"]."\"";
        $s .= (!isset($args["fileCount"])) ? "" : "data-formx-file-count=\"".$args["fileCount"]."\"";

        return $s;

    }
}