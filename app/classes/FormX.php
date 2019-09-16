<?php
/**
 * @name FormX.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to creates and validates forms. Part of panx-framework.
 */

declare(strict_types=1);

class FormX {
    /**
     * @var string The form method: GET / POST. 
     */
    public $method;
    /**
     * @var string The form action. 
     */
    public $action;
    /**
     * @var string The form CSRF token.
     */
    public $csrf;
    // array [0] err_type (0 -empty, 1 -validator), [1] element, [2] msg
    /**
     * @var array If the form is not valid, then the invalid input will appear here.
     */
    public $error;
    /**
     * @var string|bool If the form is not valid, then the message will appear here.
     */
    public $errorMsg = false;
    /**
     * @var array The array of all elements (inputs) in form.
     */
    private $el = array();

    public const ELEMENT_EMPTY = 1;
    public const ELEMENT_NOT_VALID = 2;

    public const ERROR_REQUIRED = "Please, fill the %s.";
    public const ERROR_NOT_VALID = "Please, enter a valid value to the %s.";

    /**
     * @param string $method The form method: GET / POST.
     * @param string $action The form action.
     */
    public function __construct(string $method, string $action) {
        $this->method = $method;
        $this->action = $action;

        $e = new FormXElement('input', 'csrf_token');
        $this->csrf_token = $e;
        if(empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = substr(base_convert(sha1(uniqid((string)mt_rand())), 16, 36), 0, 32);
        }
        $this->csrf = $_SESSION["csrf_token"];
        $e->type("hidden")
            ->required(true)
            ->value($this->csrf);
        
        array_push($this->el, $e);
    }

    /**
     * Add element to the form.
     * @param string $type The element type, e.g. button, input...
     * @param string $name The name of the element. Used in name="" atrribute.
     * @return FormXElement The form element.
     */
    public function add(string $type, string $name, string $files=null): FormXElement {
        $e = new FormXElement($type, $name, $files);
        array_push($this->el, $e);
        $this->$name = $e;
        return $e;
    }

    /**
     * This method will check for errors, if the form was submitted.
     */
    public function checkForErrors() {
        if($this->method == "GET")
            if(count($_GET) === 0)
                return;
        if($this->method == "POST")
            if(count($_POST) === 0)
                return;
        $this->validate();
    }

    /**
     * Validates the form. All required elements must be filled, and if the element have Validator, then the element's value must be valid.
     * @return bool Returns true if the form is valid, false otherwise.
     */
    public function validate(): bool {
        foreach ($this->el as $el) {
            $n = $el->nameFiles ?? $el->name;
            if($el->required) {
                if($this->method == "POST") {
                    if(empty($_POST[$n])) {
                        $this->setError($el, self::ELEMENT_EMPTY);
                        return false;
                    }
                    if($n === "csrf_token") {
                        if($_POST[$n] != $_SESSION['csrf_token']) {
                            $this->setError($el, self::ELEMENT_NOT_VALID);
                            return false;
                        }
                    }
                } else {
                    if(empty($_GET[$n])) {
                        $this->setError($el, self::ELEMENT_EMPTY);
                        return false;
                    }
                    if($n === "csrf_token") {
                        if($_GET[$n] != $_SESSION['csrf_token']) {
                            $this->setError($el, self::ELEMENT_NOT_VALID);
                            return false;
                        }
                    }
                }
                if(isset($el->nameFiles)) {
                    //file arr
                    $v = $_POST[$n] ?? $_GET[$n];
                    if(count($v) == 1 && $v[0] == "") {
                        $this->setError($el, self::ELEMENT_EMPTY);
                        return false;

                    }
                }
            }

            if($el->validator !== null) {
                if($this->method == "POST") {
                    //The element is not required, so if it is empty, then just continue.
                    if(empty($_POST[$n])) {
                        continue;
                    }
                    $x = forward_static_call_array(array($el->validator[0], $el->validator[1]), array($_POST[$n]));
                    if(!$x) {
                        $this->setError($el, self::ELEMENT_NOT_VALID);
                        return false;
                    }
                } else {
                    //The element is not required, so if it is empty, then just continue.
                    if(empty($_GET[$n])) {
                        continue;
                    }
                    $x = forward_static_call_array(array($el->validator[0], $el->validator[1]), array($_GET[$n]));
                    if(!$x) {
                        $this->setError($el, self::ELEMENT_NOT_VALID);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Sets the $errorMsg and $error.
     * @param FormXElement $el The element that is not valid.
     * @param int $type The Error type, e.g. FormX::ELEMENT_EMPTY or FormX::ELEMENT_NOT_VALID.
     */
    public function setError(FormXElement $el, int $type) {
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

    /**
     * Obtains the values from form.
     * @return array Returns the array containing all filled data from form.
     */
    public function getValues(): array {
        $arr = [];
        foreach ($this->el as $el) {
            $n = $el->nameFiles ?? $el->name;
            if($n === "csrf_token") continue;
            //if isset default, then sets the value to the default and if the value was filled in form, then overwrite with it.
            if(!empty($el->default)) {
                $arr[$n] = $el->default;
            }
            if($this->method == "POST") {
                if(!empty($_POST[$n])) {
                    $arr[$n] = $_POST[$n];
                }
            } else {
                if(!empty($_GET[$n])) {
                    $arr[$n] = $_GET[$n];
                }
            }
        }
        return $arr;
    }

    /**
     * @return array Returns the array containing all elements names.
     */
    public function getElementsNames(): array {
        $arr = array();
        foreach ($this->el as $el) {
            array_push($arr, $el->name);
        }
        return $arr;
    }
}
