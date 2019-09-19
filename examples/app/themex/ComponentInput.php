<?php
class ComponentInput extends SingleComponent {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function component(): string {
        return "<input ".$this->createStringFromArgs($this->args).">";
    }
}