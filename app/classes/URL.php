<?php
/**
 * @name URL.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with URLs. Part of panx-framework.
 */

class URL implements Iterator {
    /**
     * @var string The string representing URL.
     */
    private $URL_STRING;
    /**
     * @var array The array containing URL elements (URL splited by '/').
     */
    private $URL_LINK = array();
    /**
     * @var int The count of URL elements.
     */
    private $ELEMENTS = 0;

    /**
     * Calls urlString() method with passed parameters.
     * @param string|null $URL The URI to work with, if its sets to null, it will use the current URI. 
     * @param boolean $DECODE If its sets to true, the URI will be decoded using urldecode().
     */
    public function __construct($URL = null, $DECODE = true) {
        $this->urlString($URL, $DECODE);
    }

    /**
     * Splits the URI to elements (by '/' character). It will delete any get parameter (Everything behind '?' character). It will delete '//'. It will also remove '/' from end
     * @param string $URL_TO_CHECK The URI to work with, if its sets to null, it will use the current URI.
     * @param boolean $DECODE If its sets to true, the URI will be decoded using urldecode().
     */
    public function urlString($URL_TO_CHECK = null, $DECODE = true) {
        if($URL_TO_CHECK == null)
            $URL_TO_CHECK = $_SERVER['REQUEST_URI'];
        if($DECODE)
            $this->URL_STRING = urldecode($URL_TO_CHECK);
        else 
            $this->URL_STRING = $URL_TO_CHECK;
        $this->URL_STRING = explode("?", $this->URL_STRING);

        $this->URL_STRING = $this->URL_STRING[0];
        while (strpos($this->URL_STRING, "//") !== false) {
            $this->URL_STRING = str_replace("//", "/", $this->URL_STRING);
        }
        $this->URL_STRING = rtrim($this->URL_STRING, "/");

        $this->URL_LINK = explode("/", $this->URL_STRING);
        if($this->URL_STRING == "") {
            $this->URL_STRING = "/";
        }
        for ($x = 0; $x < count($this->URL_LINK); $x++) {
            if ($this->URL_LINK[$x] != "") {
                $this->ELEMENTS++;
            }
        }
    }

    /**
     * @return string Returns the string representing URL.
     */
    public function getString() {
        return $this->URL_STRING;
    }

    /**
     * @return array Returns the array containing URL elements (URL splited by '/'). First element [0] is empty.
     */
    public function getLink() {
        return $this->URL_LINK;
    }

    /**
     * @return int Returns the count of URL elements.
     */
    public function getCount() {
        return $this->ELEMENTS;
    }

    //ITERATION
    public function rewind()
    {
        reset($this->URL_LINK);
    }
  
    public function current()
    {
        return current($this->URL_LINK);
    }
  
    public function key() 
    {
        return key($this->URL_LINK);
    }
  
    public function next() 
    {
        return next($this->URL_LINK);
    }
  
    public function valid()
    {
        $key = key($this->URL_LINK);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }

}