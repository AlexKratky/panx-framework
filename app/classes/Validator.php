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

class Validator extends ValidatorFunctions {
    public const RULE_CUSTOM = 0;
    public const RULE_MAIL = 1;
    public const RULE_USERNAME = 2;
    public const RULE_PASSWORD = 3;
    public const RULE_CHECKBOX = 4;

    /**
     * Validates multiple inputs. The hierachy is:
     * $inputs = [
     *      [
     *          "example@example.com", // value
     *          1 // rule (1 = RULE_MAIL)
     *              // min length 
     *              // max length
     *              // chars
     *      ]
     * ]
     * 
     * @param array $inputs
     * @return bool Returns true if all inputs are valid, otherwise return the first $input array, that is not valid. 
     */
    public static function multipleValidate($inputs) {
        foreach ($inputs as $input) {
            if(!validate($input[0], $input[1], $input[2] ?? 0, $input[3] ?? 0, $input[4] ?? '/[^A-Za-z0-9]/')) {
                return $input;
            }
        }
        return true;
    }

    /**
     * Validates input by rule.
     * @param mixed $input The text(data) that will be validated.
     * @param int $rule Sets the rule for validating. If the rule is not valid, then it will use following parameters:
     * @param int $min_length The minimum length of $input.
     * @param int $max_length The maximum length of $input.
     * @param string The character mask of $input. Regex.
     * @return bool Returns true if the input is valid, otherwise false.
     */
    public static function validate($input, $rule = 0, $min_length = 0, $max_length = 0, $chars = '/[^A-Za-z0-9]/') {
        if(empty($input)) {
            return false;
        }
        switch($rule) {
            case self::RULE_MAIL:
                return self::validateMail($input);
                break;
            case self::RULE_USERNAME:
                return self::validateUsername($input);
                break;
            case self::RULE_PASSWORD:
                return self::validatePassword($input);
                break;
            case self::RULE_CHECKBOX:
                return self::validateCheckBox($input);
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

    /**
     * Validates $input as email.
     * @return bool Returns true if the $input is valid, false otherwise.
     */
    public static function validateMail($input) {
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    /**
     * Validates $input as username (only alphanumeric chars and minimum 4 chars length).
     * @return bool Returns true if the $input is valid, false otherwise.
     */
    public static function validateUsername($input) {
        if(!ctype_alnum($input) || strlen($input) < 4) {
            return false;
        }
        return true;
    }

    /**
     * Validates $input as password (minimum 6 chars length).
     * @return bool Returns true if the $input is valid, false otherwise.
     */
    public static function validatePassword($input) {
        if(strlen($input) < 6) {
            return false;
        }
        return true;
    }

    /**
     * Validates $input as checkbox (If equals to "on", returns true).
     * @return bool Returns true if the $input is valid, false otherwise.
     */
    public static function validateCheckBox($input) {
        if(strtolower($input) != "on") {
            return false;
        }
        return true;
    }


}