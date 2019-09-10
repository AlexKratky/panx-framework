<?php
class ComponentImage extends SingleComponent {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function component(): string {
        return "<img class='".join(" ", $this->args["class"])."' id='".$this->args["id"]."' src='".$this->args["src"]."' />";
    }
}