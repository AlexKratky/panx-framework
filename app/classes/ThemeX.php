<?php
/**
 * @name ThemeX.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Components and themes. Part of panx-framework.
 */

declare(strict_types=1);

class ThemeX {
    /**
     * @var string The name of the component. 
     */
    private $component_name;
    /**
     * @var mixed The component instance reference. 
     */
    private $component;

    /**
     * Creates a new instance of ThemeX
     * @param string $component The name of the component, e.g. Button, Input etc.
     * @param mixed $args The arguments for component.
     */
    public function __construct(string $component, $args, $fn = null, $fx = null) {
        $this->component_name = $component;
        $n = "Component".ucfirst($component);
        $this->component = new $n($args, $fn, $fx);
    }

    /**
     * @return string Returns the string containing the HTML code of component (the first part of component).
     */
    public function componentStart(): string {
        return $this->component->componentStart();
    }

    /**
     * @return string Returns the string containing the HTML code of component (the second part of component).
     */
    public function componentEnd(): string {
        return $this->component->componentEnd();        
    }

    /**
     * @return string Returns the string containing the HTML code of component.
     */
    public function component(): string {
        return $this->component->component();
    }

}
