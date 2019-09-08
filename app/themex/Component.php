<?php
abstract class Component {
    private $args;

    abstract public function componentStart();

    abstract public function componentEnd();

    public function createStringFromArgs($args) {
        $s = "name=\"".$args["name"]."\"";
        $s .= (!isset($args["type"])) ? "" : (empty($args["type"]) ? "" : " type=\"" . $args["type"] . "\"");
        $s .= (!isset($args["id"])) ? "" : (empty($args["id"]) ? "" : " id=\"" . $args["id"] . "\"");
        $s .= (!isset($args["value"])) ? "" : (empty($args["value"]) ? "" : " value=\"" . $args["value"] . "\"");
        $s .= (!isset($args["placeholder"])) ? "" : (empty($args["placeholder"]) ? "" : " placeholder=\"" . $args["placeholder"] . "\"");
        $s .= (!isset($args["html"])) ? "" : (empty($args["html"]) ? "" : " " . $args["html"]);
        $s .= (!isset($args["required"])) ? "" : (($args["required"] == "1") ? " required" : "");

        return $s;


    }
}