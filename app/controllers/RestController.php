<?php
class RestController {
    private static $handler;
    private static $restapi;
    private static $convertColumnToUppercase = true;
    private static $convertColumnToLowercase = false;
    private static $api;

    public static function main($handler)
    {
        self::$handler = $handler;
        self::$api = new API("v1");
        if(!self::$api->request(new URL())) {
            echo json(self::$api->error());
            exit();
        }
        self::$restapi = new RestAPI(true,
            //get
            array(
                "users" => array(
                    "columns" => ["ID", "USERNAME", "EMAIL", "VERIFIED", "ROLE", "PERMISSIONS", "CREATED_AT", "EDITED_AT"],
                    "getByColumn" => null,
                    "permission" => "get_table_users",
                    "select_all_permission" => "get_all_users",
                    "user_row" => array(
                        "column" => "ID",
                        "user" => "id",
                        "permission" => null
                    )
                )
            ),
            // TODO: TEST CREATE
            //create
            array(
                "api_keys" => array(
                    "columns" => ["API_KEY", "RATE_LIMIT", "RATE_LIMIT_MONTHLY", "RATE_LIMIT_DAILY_CURRENT", "RATE_LIMIT_DAILY", "RATE_LIMIT_WEEKLY_CURRENT", "RATE_LIMIT_WEEKLY", "RATE_LIMIT_TOTAL"], //required post params
                    "permission" => "insert_table_api_keys",
                )
            ),
            // TODO : TEST update
            //update
            array(
                "users" => array(
                    "columns" => ["ID", "USERNAME", "EMAIL", "VERIFIED", "ROLE", "PERMISSIONS"],
                    "getByColumn" => null,
                    "permission" => "update_table_users",
                    "user_row" => array(
                        "column" => "ID",
                        "user" => "id",
                        "permission" => null
                    )
                )
            ),
            //delete
            array(
                "api_keys" => array(
                    "columns" => ["ID", "API_KEY"], // columns that can be used to indetify the row to delete
                    "getByColumn" => null,
                    "permission" => "delete_table_api_keys"
                )
            )
        );
    }

    public static function login() {
            echo json(
                    json_encode(
                        self::$restapi->login()
                    )
                );
        
       
    }

    public static function get(string $table, string $column, $id) {
        if(self::$convertColumnToUppercase) {
            $column = strtoupper($column);
        } elseif(self::$convertColumnToLowercase) {
            $column = strtolower($column);
        }
        echo json(
                json_encode(
                    self::$restapi->get($table, $column, $id)
                )
            );
    }

    public static function create(string $table) {
        echo json(
                json_encode(
                    self::$restapi->create($table)
                )
            );
    }

    public static function update(string $table, string $column, $id) {
        if(self::$convertColumnToUppercase) {
            $column = strtoupper($column);
        } elseif(self::$convertColumnToLowercase) {
            $column = strtolower($column);
        }
        echo json(
                json_encode(
                    self::$restapi->update($table, $column, $id)
                )
            );
    }

    public static function delete(string $table, string $column, $id) {
        if(self::$convertColumnToUppercase) {
            $column = strtoupper($column);
        } elseif(self::$convertColumnToLowercase) {
            $column = strtolower($column);
        }
        echo json(
                json_encode(
                    self::$restapi->delete($table, $column, $id)
                )
            );
    }
}