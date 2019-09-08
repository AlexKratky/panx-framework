<?php
class FormX {
    public $method;
    public $action;
    public $csrf;
    // array [0] err_type (0 -empty, 1 -validator), [1] element, [2] msg
    public $error;
    public $errorMsg = false;
    private $el = array();

    public const ELEMENT_EMPTY = 1;
    public const ELEMENT_NOT_VALID = 2;

    public const ERROR_REQUIRED = "Please, fill the %s.";
    public const ERROR_NOT_VALID = "Please, enter a valid value to the %s.";

    public function __construct($method, $action) {
        $this->method = $method;
        $this->action = $action;

        $e = new FormXElement('input', 'csrf_token');
        $this->csrf_token = $e;
        if(empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 32);
        }
        $this->csrf = $_SESSION["csrf_token"];
        $e->type("hidden")
            ->required(true)
            ->value($this->csrf);
        
        array_push($this->el, $e);
    }

    public function add($type, $name) {
        $e = new FormXElement($type, $name);
        array_push($this->el, $e);
        $this->$name = $e;
        return $e;
    }

    public function checkForErrors() {
        if($this->method == "GET")
            if(count($_GET) === 0)
                return;
        if($this->method == "POST")
            if(count($_POST) === 0)
                return;
        $this->validate();
    }

    public function validate() {
        foreach ($this->el as $el) {
            if($el->required) {
                if($this->method == "POST") {
                    if(empty($_POST[$el->name])) {
                        $this->setError($el, self::ELEMENT_EMPTY);
                        return false;
                    }
                    if($el->name === "csrf_token") {
                        if($_POST[$el->name] !== $_SESSION['csrf_token']) {
                            $this->setError($el, self::ELEMENT_NOT_VALID);
                            return false;
                        }
                    }
                } else {
                    if(empty($_GET[$el->name])) {
                        $this->setError($el, self::ELEMENT_EMPTY);
                        return false;
                    }
                    if($el->name === "csrf_token") {
                        if($_GET[$el->name] !== $_SESSION['csrf_token']) {
                            $this->setError($el, self::ELEMENT_NOT_VALID);
                            return false;
                        }
                    }
                }
            }

            if($el->validator !== null) {
                if($this->method == "POST") {
                    $x = forward_static_call_array(array($el->validator[0], $el->validator[1]), array($_POST[$el->name]));
                    if(!$x) {
                        $this->setError($el, self::ELEMENT_NOT_VALID);
                        return false;
                    }
                } else {
                    $x = forward_static_call_array(array($el->validator[0], $el->validator[1]), array($_GET[$el->name]));
                    if(!$x) {
                        $this->setError($el, self::ELEMENT_NOT_VALID);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function setError($el, $type) {
        if($type === self::ELEMENT_EMPTY) {
            $this->error = array(self::ELEMENT_EMPTY, $el->name);
            $m = $el->errorMsgEmpty ?? (__("form_error_empty", true, array($el->name), false) === false ? self::ERROR_REQUIRED : __("form_error_empty", true, array($el->name)));
            $m = str_replace("%s", $el->name, $m);
            $this->errorMsg = $m;
        } else if($type === self::ELEMENT_NOT_VALID) {
            $this->error = array(self::ELEMENT_NOT_VALID, $el->name);
            $m = $el->errorMsgNotValid ?? (__("form_not_valid", true, array($el->name), false) === false ? self::ERROR_NOT_VALID : __("form_not_valid", true, array($el->name)));
            $m = str_replace("%s", $el->name, $m);
            $this->errorMsg = $m;
        }
    }

    public function getValues() {
        $arr = [];
        foreach ($this->el as $el) {
            if($el->name === "csrf_token") continue;
            if($this->method == "POST") {
                if(!empty($_POST[$el->name])) {
                    $arr[$el->name] = $_POST[$el->name];
                }
            } else {
                if(!empty($_GET[$el->name])) {
                    $arr[$el->name] = $_GET[$el->name];
                }
            }
        }
        return $arr;
    }

    public function getElementsNames() {
        $arr = array();
        foreach ($this->el as $el) {
            array_push($arr, $el->name);
        }
        return $arr;
    }
}