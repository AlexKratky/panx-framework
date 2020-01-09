<?php
/**
 * @name FormXElement.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description The form element. Part of panx-framework.
 */

declare(strict_types=1);

class FormXElement {
    /**
     * @var string|null The elements type, e.g. button, input ...
     */
    public $elementType = null;
    /**
     * @var string|null The component name of element. Used in form rendering.
     */
    public $componentName = null;
    /**
     * @var string|null The elements name, used in name="" atrribute.
     */
    public $name = null;
    /**
     * @var string|null The elements file input name.
     */
    public $nameFiles = null;
    /**
     * @var string|null The input type, e.g. 'text', 'password', ...
     */
    public $type = null;
    /**
     * @var string|null The elements ID, used in id="" atrribute.
     */
    public $id = null;
    /**
     * @var string|null The elements default value. Used when the element was not filled.
     */
    public $default = null;
    /**
     * @var string|null The elements placeholder, used in placeholder="" atrribute.
     */
    public $placeholder = null;
    /**
     * @var bool Determines if the element is required to fill or not, used in required atrribute.
     */
    public $required = false;
    /**
     * @var array The elements validator. [0] => Class name, [1] => Function name
     */
    public $validator = null;
    /**
     * @var array The elements regex validator. Used in JS.
     */
    public $validatorRegex = null;
    /**
     * @var string|null The elements additional html code.
     */
    public $html = null;
    /**
     * @var string|null The elements text.
     */
    public $text = null;
    /**
     * @var string|null The elements value, used in value="" atrribute.
     */
    public $value = null;
    /**
     * @var int The file maximum size
     */
    public $fileSize = null;
    /**
     * @var string The allowed file extensions as string: 'jpg, png' ...
     */
    public $fileExtensions = null;
    /**
     * @var int The file maximum count on one upload
     */
    public $fileCount = null;
    /**
     * @var string|null The elements error message when the input is empty.
     */
    public $errorMsgEmpty = null;
    /**
     * @var string|null The elements error message when the input is not valid.
     */
    public $errorMsgNotValid = null;
    private $formName;

    /**
     * @param string $t The element type, e.g. button, input, ...
     * @param string $n The element name.
     * @param string $files The name of file input without [] (files[] => files).
     */
    public function __construct(string $t, string $n, string $files = null, ?string $formName = null) {
        $this->elementType = $t;
        $this->componentName = $t;
        $this->name = $n;
        $this->nameFiles = $files;
        $this->formName = $formName;
    }

    /**
     * @param bool $r If sets to true, then the element is required to fill, otherwise it can be empty.
     * @return FormXElement
     */
    public function required(bool $r): FormXElement {
        $this->required = $r;
        return $this;
    }

    /**
     * @param string $id The element's ID.
     * @return FormXElement
     */
    public function id(string $id): FormXElement {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $t The element's type, e.g. 'text', 'password', ... 
     * @return FormXElement
     */
    public function type(string $t): FormXElement {
        $this->type = $t;
        //if($type == "password") {$this->value = null;}
        return $this;
    }

    /**
     * @param string $d The element's default value.
     * @return FormXElement
     */
    public function default(string $d): FormXElement {
        $this->default = $d;
        return $this;
    }

    /**
     * @param string $ph The element's placeholder.
     * @return FormXElement
     */
    public function placeholder(string $ph): FormXElement {
        $this->placeholder = $ph;
        return $this;
    }

    /**
     * @param string $class The validator class name, e.g. 'Validator'.
     * @param string $fn_name The validator function name, e.g. 'validateUsername'.
     * @return FormXElement
     */
    public function validator(string $class, string $fn_name): FormXElement {
        $this->validator = [$class, $fn_name];
        return $this;
    }

    /**
     * @param string $class The validator regex code.
     * @return FormXElement
     */
    public function validatorRegex(string $regex): FormXElement {
        $this->validatorRegex = $regex;
        return $this;
    }

    /**
     * @param string $h The element's additional HTML code.
     * @return FormXElement
     */
    public function html(string $h): FormXElement {
        $this->html = $h;
        return $this;
    }

    /**
     * @param string $t The element's text.
     * @return FormXElement
     */
    public function text(string $t): FormXElement {
        $this->text = $t;
        return $this;
    }

    /**
     * @param string $v The element's value.
     * @return FormXElement
     */
    public function value(string $v): FormXElement {
        $this->value = $v;
        return $this;
    }

    /**
     * @param string $s The maximum upload size in bytes.
     * @return FormXElement
     */
    public function fileSize(int $s): FormXElement {
        $this->fileSize = $s;
        return $this;
    }

    /**
     * @param string $e The allowed file extensions as string: 'jpg, png' ...
     * @return FormXElement
     */
    public function fileExtensions(string $e): FormXElement {
        $this->fileExtensions = $e;
        return $this;
    }

    /**
     * @param int $c The maximum count of files on one upload.
     * @return FormXElement
     */
    public function fileCount(int $c): FormXElement {
        $this->fileCount = $c;
        return $this;
    }


    /**
     * @param string $m The element's error message, when the required element is empty.
     * @return FormXElement
     */
    public function errorMsgEmpty(string $m): FormXElement {
        $this->errorMsgEmpty = $m;
        return $this;
    }

    /**
     * @param string $m The element's error message, when the element is not valid (If the element have Validator).
     * @return FormXElement
     */
    public function errorMsgNotValid(string $m): FormXElement {
        $this->errorMsgNotValid = $m;
        return $this;
    }

    /**
     * @param string $n The component name, e.g. 'button', 'input', ...
     * @return FormXElement
     */
    public function component(string $n): FormXElement {
        $this->componentName = $n;
        return $this;
    }
}
