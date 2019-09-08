<?php
abstract class Form {
    public static $themeX;

    public $formName;
    public $dir = null;
    protected $form;

    abstract public function __construct($formName, $dir = null);

    public function render() {
        $latte = new Latte\Engine;
        $set = new Latte\Macros\MacroSet($latte->getCompiler());

        $set->addMacro('link', null, null,
            'if ($_l->tmp = array_filter(%node.array))
            echo \' href="\' . Form::convert($_l->tmp) . \'"\'
        ');
        $set->addMacro(
            "component",
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('echo Form::component("'.$param.'")'); 
            },
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('echo Form::componentEnd("'.$param.'")'); 
            }, 
            null
        );
        $set->addMacro(
            "singleComponent",
            function($node, $writer) {
                $param = $node->args;
                return $writer->write('echo Form::singleComponent("'.$param.'")'); 
            },
            null, 
            null
        );
        $set->addMacro(
            "csrf",
            function($node, $writer) {
                $i = "<input name=".$this->form->csrf_token->name." type=".$this->form->csrf_token->type." required value=".$this->form->csrf_token->value.">";
                $param = $node->args;
                return $writer->write('echo \''.$i.'\''); 
            },
            null, 
            null
        );
        $set->addMacro(
            "input",
            function($node, $writer) {
                $param = $node->args;
                $component = explode(",", $param, 2)[0];
                $el = $this->form->$component;
                
                $p = get_parent_class("Component".ucfirst($el->componentName));
                if($p === false)
                    return $writer->write('echo "not"');
                $args = $el->componentName;
                $attr = array("name", "type", "id", "default", "placeholder", "required", "html", "text", "value", "errorMsgEmpty", "errorMsgNotValid", "validator");
                foreach($attr as $a) {
                    if(empty($el->$a))  continue;
                    $x = $el->$a;
                    if($a !== "validator" || $x === null) {
                        if($a == "errorMsgEmpty" || $a == "errorMsgNotValid" || $a == "html") {
                            $args .= ", $a => '".str_replace("'", '\\"', $x)."'";   
                        } else {
                            $args .= ", $a => $x";
                        }
                    } else {
                        $args .= ", $a => '{$x[0]}, {$x[1]}'";
                    }
                }
                if($p === "Component") {
                    return $writer->write('echo Form::component("'.$args.'") . Form::componentEnd("'.$args.'")'); 
                } else {
                    //SingleComponent
                    return $writer->write('echo Form::singleComponent("'.$args.'")'); 
                }
            },
            null, 
            null
        );
        $dir = $this->dir ?? $_SERVER['DOCUMENT_ROOT']."/../app/forms/";

        // check for errors, so $form->error will have some value.
        $this->form->checkForErrors();
        
        $latte->setTempDirectory($_SERVER['DOCUMENT_ROOT']."/../temp/");
        $latte->render("{$dir}$this->formName.latte", array("form" => $this->form));
    }

    public function validate() {
        return $this->form->validate();
    }

    public function getValues() {
        return $this->form->getValues();
    }

    public function error() {
        return $this->form->error;
    }


    public static function convert($x) {
        $alias = $x[0];
        $params = $x[1] ?? null;
        $get = $x[2] ?? null;
        return Route::alias($alias, $params, $get);
    }

    public static function component($node = null) {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        self::$themeX = new ThemeX($component, $args);
        return self::$themeX->componentStart();
    }

    public static function componentEnd($node = null) {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        return self::$themeX->componentEnd();
    }

    public static function singleComponent($node = null) {

        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        self::$themeX = new ThemeX($component, $args);
        return self::$themeX->component();
    }


    private static function convertNodeToArray($node) {
        $m = preg_split("/'[^']*'(*SKIP)(*F)|(,\s)+/", $node);
        $args = array();
        foreach ($m as $v) {
            if(strpos($v, " => ")) {
                $v = explode(" => ", $v, 2);
                $t = array();
                $t = explode(", ", $v[1]);
                $t[0] = ltrim($t[0], "'");
                $t[count($t) - 1] = rtrim($t[count($t) - 1], "'");
                if(count($t) == 1) {
                    $t = $t[0];
                }
                //dump($t);
                $args[$v[0]] = $t;

            }
        }
        return $args;
    }
}