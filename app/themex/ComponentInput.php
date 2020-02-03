<?php
class ComponentInput extends SingleComponent {
    private $args;
    private $fn;
    private $formx;
    public function __construct($args, $fn, $formx) {
        $this->args = $args;
        $this->fn = $fn;
        $this->formx = $formx;
    }
    public function component(): string {
        return "<input ".$this->createStringFromArgs($this->args, $this->fn, $this->formx).">";
    }
}