<?php
class ComponentInput extends SingleComponent {
    private $args;
    private $fn;
    public function __construct($args, $fn) {
        $this->args = $args;
        $this->fn = $fn;
    }
    public function component(): string {
        return "<input ".$this->createStringFromArgs($this->args, $this->fn).">";
    }
}