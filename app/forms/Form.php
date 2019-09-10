<?php
/**
 * @name Form.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Abstract form class from which every Form should inherit. Part of panx-framework.
 */

declare(strict_types=1);

abstract class Form {
    /**
     * @var ThemeX
     */
    public static $themeX;
    /**
     * @var string The form's name. Used to rendering Latte file (It will render the $formName.latte).
     */
    public $formName;
    /**
     * @var string The Latte file directory, by default $_SERVER['DOCUMENT_ROOT']."/../app/forms/"
     */
    public $dir = null;
    /**
     * @var FormX The form reference
     */
    protected $form;

    /**
     * @param string $formName The form's name. Used to rendering Latte file (It will render the $formName.latte).
     * @param string $dir The Latte file directory, by default $_SERVER['DOCUMENT_ROOT']."/../app/forms/"
     */
    abstract public function __construct(string $formName, ?string $dir = null);

    /**
     * Renders the form.
     */
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
                $param = $node->args;
                return $writer->write('echo Form::getCsrf("'.$param.'")'); 
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

    /**
     * Validates the form.
     * @return bool Returns true if the form is valid, false otherwise.
     */
    public function validate(): bool {
        return $this->form->validate();
    }

    /**
     * Obtain the values from form.
     * @return array The array containing all filled form elements.
     */
    public function getValues(): array {
        return $this->form->getValues();
    }

    /**
     * Obtain the form's error.
     * @return array [0] err_type (1 -empty, 2 -validator), [1] element, [2] msg
     */
    public function error(): ?array {
        return $this->form->error;
    }

    public static function getCsrf() {
        return '<input name="csrf_token" type="hidden" required value="'.$_SESSION["csrf_token"].'">';
    }

    /**
     * Converts the alias to the URL.
     * @param array $x Alias array. [0] => name; [1] => params; [2] => get
     * @return string URL.
     */
    public static function convert(array $x): string {
        $alias = $x[0];
        $params = $x[1] ?? null;
        $get = $x[2] ?? null;
        return Route::alias($alias, $params, $get);
    }

    /**
     * Obtain the first part of component. Creates new ThemeX instance with $args parsed from $node.
     * @param string|null $node Node from latte.
     * @return string Returns the first part of component.
     */
    public static function component(?string $node = null): string {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        self::$themeX = new ThemeX($component, $args);
        return self::$themeX->componentStart();
    }

    /**
     * Obtain the second part of component. Must be called after component(), because it works with instance of ThemeX created in component.
     * @param string|null $node Node from latte.
     * @return string Returns the first part of component.
     */
    public static function componentEnd(?string $node = null): string {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        return self::$themeX->componentEnd();
    }

    /**
     * Obtain the html of component. Creates new ThemeX instance with $args parsed from $node.
     * @param string|null $node Node from latte.
     * @return string Returns the first part of component.
     */
    public static function singleComponent(?string $node = null): string {
        $component = explode(",", $node, 2)[0];
        $args = self::convertNodeToArray($node);
        self::$themeX = new ThemeX($component, $args);
        return self::$themeX->component();
    }

    /**
     * Converts $node to array.
     * @param string $node The node string from Latte.
     * @return array The newly created array from $node.
     */
    private static function convertNodeToArray(string $node): array {
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
