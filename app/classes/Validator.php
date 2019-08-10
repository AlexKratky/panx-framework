<?php
/**
 * @name Validator.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to validate the user inputs. Part of panx-framework.
 */

class Validator {
    public const RULE_CUSTOM = 0;
    public const RULE_MAIL = 1;
    public const RULE_USERNAME = 2;
    public const RULE_PASSWORD = 3;
    public const RULE_CHECKBOX = 4;

    /**
     * $inputs = [
     *      [
     *          "example@example.com",
     *          1
     *      ]
     * ]
     */
    public function multipleValidate($inputs) {
        foreach ($inputs as $input) {
            if(!validate($input[0], $input[1])) {
                return $input;
            }
        }
        return true;
    }

    public function validate($input, $rule = 0, $min_length = 0, $max_length = 0, $chars = '/[^A-Za-z0-9]/') {
        if(empty($input)) {
            return false;
        }
        switch($rule) {
            case self::RULE_MAIL:
                return $this->validateMail($input);
                break;
            case self::RULE_USERNAME:
                return $this->validateUsername($input);
                break;
            case self::RULE_PASSWORD:
                return $this->validatePassword($input);
                break;
            case self::RULE_CHECKBOX:
                return $this->validateCheckBox($input);
                break;
            default:
                if(strlen($input) >= $min_length && strlen($input) <= $max_length && !preg_match($chars, $input)) {
                    return true;
                } else {
                    return false;
                }
                break;
            
        }
    }

    public function validateMail($input) {
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    public function validateUsername($input) {
        if(!ctype_alnum($input) || strlen($input) < 4) {
            return false;
        }
        return true;
    }

    public function validatePassword($input) {
        if(strlen($input) < 6) {
            return false;
        }
        return true;
    }

    public function validateCheckBox($input) {
        if(strtolower($input) != "on") {
            return false;
        }
        return true;
    }


}