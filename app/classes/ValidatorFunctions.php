<?php
/**
 * @name ValidatorFunctions.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class contains custom validator functions. Part of panx-framework.
 */

declare (strict_types = 1);

class ValidatorFunctions {

    public static function isEqualToAlex(string $input): bool {
        if (strtolower($input) != "alex") {
            return false;
        }
        return true;
    }

}
