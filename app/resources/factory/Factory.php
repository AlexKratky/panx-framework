<?php
abstract class Factory {
    protected $factory;

    public function __construct() {
        require_once __DIR__ . '/../../../vendor/autoload.php';

        $this->factory = Faker\Factory::create();
    }

    abstract public function generate($args = array());
}