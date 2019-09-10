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

    /**
     * @return string The HTML code of component.
     */
    abstract public function component(): string;

    /**
     * Create HTML string with values from array.
     * @param array $args The array of argument.
     * @return string Returns html string with attributes, e.g. 'name="x" value="y" placeholder="z"' ...
     */
    public function createStringFromArgs($args) {
        $s = "name=\"".$args["name"]."\"";
        $s .= (!isset($args["type"])) ? "" : (empty($args["type"]) ? "" : " type=\"".$args["type"]."\"");
        $s .= (!isset($args["id"])) ? "" : (empty($args["id"]) ? "" : " id=\"".$args["id"]."\"");
        $s .= (!isset($args["value"])) ? "" : (empty($args["value"]) ? "" : " value=\"".$args["value"]."\"");
        $s .= (!isset($args["placeholder"])) ? "" : (empty($args["placeholder"]) ? "" : " placeholder=\"".$args["placeholder"]."\"");
        $s .= (!isset($args["html"])) ? "" : (empty($args["html"]) ? "" : " ".$args["html"]);
        $s .= (!isset($args["required"])) ? "" : (($args["required"] == "1") ? " required" : "");
        return $s;

    }
}