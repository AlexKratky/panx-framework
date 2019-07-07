<?php
/**
 * @name MainModel.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Example model.
 */

class MainModel {
    public function __construct() {

    }

    public function selectFromDb() {
        //example
        return 
        [
            'items' => ['model', 'use', 'example'],
        ];
    }
}