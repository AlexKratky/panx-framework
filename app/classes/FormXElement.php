<?php
class FormXElement {
    public $elementType = null;
    public $componentName = null;
    public $name = null;
    public $type = null;
    public $id = null;
    public $default = null;
    public $placeholder = null;
    public $required = false;
    public $validator = null;
    public $html = null;
    public $text = null;
    public $value = null;
    public $errorMsgEmpty = null;
    public $errorMsgNotValid = null;

    public function __construct($t, $n) {
        $this->elementType = $t;
        $this->componentName = $t;
        $this->name = $n;
    }

    public function required($r) {
        $this->required = $r;
        return $this;
    }

    public function id($id) {
        $this->id = $id;
        return $this;
    }

    public function type($t) {
        $this->type = $t;
        return $this;
    }

    public function default($d) {
        $this->default = $d;
        return $this;
    }

    public function placeholder($ph) {
        $this->placeholder = $ph;
        return $this;
    }

    public function validator($class, $fn_name) {
        $this->validator = [$class, $fn_name];
        return $this;
    }

    public function html($h) {
        $this->html = $h;
        return $this;
    }

    public function text($t) {
        $this->text = $t;
        return $this;
    }

    public function value($v) {
        $this->value = $v;
        return $this;
    }

    public function errorMsgEmpty($m) {
        $this->errorMsgEmpty = $m;
        return $this;
    }

    public function errorMsgNotValid($m) {
        $this->errorMsgNotValid = $m;
        return $this;
    }

    public function component($n) {
        $this->componentName = $n;
        return $this;
    }
}