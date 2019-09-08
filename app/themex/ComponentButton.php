<?php
class ComponentButton extends Component {
    private $args;

    public function __construct($args) {
        $this->args = $args;
    }

    public function componentStart() {
        return "<button class='".join(" ", $this->args["class"])."' id='".$this->args["id"]."'>";
    }

    public function componentEnd() {
        return "</button>";
    }
}