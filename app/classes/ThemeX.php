<?php
class ThemeX {
    private $component_name;
    private $component;

    public function __construct($component, $args) {
        $this->component_name = $component;
        $n = "Component".ucfirst($component);
        $this->component = new $n($args);
    }

    public function componentStart() {
        return $this->component->componentStart();
    }

    public function componentEnd() {
        return $this->component->componentEnd();        
    }

    //single component
    public function component() {
        return $this->component->component();
    }

}