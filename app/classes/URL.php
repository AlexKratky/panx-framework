<?php
class URL {
    private $URL_STRING;
    private $URL_LINK = array();
    private $ELEMENTS = 0;

    public function __construct($URL = null, $DECODE = true) {
        $this->urlString($URL, $DECODE);
    }

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

    public function getString() {
        return $this->URL_STRING;
    }

    public function getLink() {
        return $this->URL_LINK;
    }

    public function getCount() {
        return $this->ELEMENTS;
    }
}