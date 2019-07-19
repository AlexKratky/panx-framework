<?php
/**
 * @name APIModel.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description API model.
 */

class APIModel {
    public function generateKey($count = 1) {
        $key;
        for($i = 0; $i < $count; $i++) {
            while(true) {
                $key = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32);
                if(db::count("SELECT COUNT(*) FROM `api_keys` WHERE `API_KEY`=?", array($key)) == 0) {
                    db::query("INSERT INTO `api_keys` (`API_KEY`) VALUES (?)", array($key));
                    break;
                }
            }
        }
        return $key;
    }

    public function validate($key) {
        if(db::count("SELECT COUNT(*) FROM `api_keys` WHERE `API_KEY`=?", array($key)) == 1) {
            $rates = db::select("SELECT * FROM `api_keys` WHERE `API_KEY`=?", array($key));
            if($rates["RATE_LIMIT"] < $rates["RATE_LIMIT_MONTHLY"]) {
                if($rates["RATE_LIMIT_DAILY_CURRENT"] < $rates["RATE_LIMIT_DAILY"]) {
                    if($rates["RATE_LIMIT_WEEKLY_CURRENT"] < $rates["RATE_LIMIT_WEEKLY"]) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function updateRate($key, $cost = 1) {
        db::query("UPDATE `api_keys` SET `RATE_LIMIT`=`RATE_LIMIT`+?, `RATE_LIMIT_DAILY_CURRENT`=`RATE_LIMIT_DAILY_CURRENT`+?, `RATE_LIMIT_WEEKLY_CURRENT`=`RATE_LIMIT_WEEKLY_CURRENT`+?, `RATE_LIMIT_TOTAL`=`RATE_LIMIT_TOTAL`+? WHERE `API_KEY`=?",array($cost, $cost, $cost, $cost, $key));
    }

    public function resetRateDaily() {
        db::query("UPDATE `api_keys` SET `RATE_LIMIT_DAILY_CURRENT`=0", array());
    }

    public function resetRateWeekly() {
        db::query("UPDATE `api_keys` SET `RATE_LIMIT_WEEKLY_CURRENT`=0", array());
    }

    public function resetRateMonthly() {
        db::query("UPDATE `api_keys` SET `RATE_LIMIT`=0", array());
    }
}
