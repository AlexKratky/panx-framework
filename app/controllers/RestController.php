<?php
class RestController {
    private static $handler;
    private static $restapi;
    private static $convertColumnToUppercase = true;
    private static $convertColumnToLowercase = false;
    private static $api;
    protected static $isApiKeyRequired = false;

    public static function main($handler)
    {
        self::$handler = $handler;
        self::$api = new API("v1");
        if(self::$isApiKeyRequired && !self::$api->request(new URL())) {
            echo json(self::$api->error());
            exit();
        }
        self::$restapi = new RestAPI(true,
            //get
            array(
                "restapi_test" => array(
                    "columns" => ["ID", "USER_ID", "TASK", "COMPLETED", "CREATED_AT"],
                    "getByColumn" => null,
                    "order" => array(
                        "column" => "ID",
                        "type" => "DESC"
                    ),
                    "permission" => null, //single row permission
                    "select_all_permission" => null, //all rows permissions
                    "select_all_user" => array(
                        "column" => "USER_ID",
                        "user" => "id",
                        "permission" => null
                    ),
                    "user_row" => array(
                        "column" => "USER_ID",
                        "user" => "id",
                        "permission" => null
                    )
                )
            ),
            //create
            array(
                "restapi_test" => array(
                    "columns" => ["TASK"], //required post params
                    "optional" => ["COMPLETED", "CREATED_AT"],
                    "add_user_data" => array(
                        "column" => "USER_ID",
                        "user" => "id"
                    ),
                    "permission" => null,
                )
            ),
            // TODO : update
            //update
            array(
                "restapi_test" => array(
                    "columns" => ["TASK", "COMPLETED"],
                    "optional" => ["CREATED_AT"],
                    "getByColumn" => null,
                    "permission" => "update_table_restapi_test",
                    "user_row" => array(
                        "column" => "USER_ID",
                        "user" => "id",
                        "permission" => null
                    )
                )
            ),
            // TODO : update
            //delete
            array(
                "restapi_test" => array(
                    "columns" => ["ID"], // columns that can be used to indetify the row to delete
                    "getByColumn" => null,
                    "permission" => "delete_table_restapi_test",
                    "user_row" => array(
                        "column" => "USER_ID",
                        "user" => "id",
                        "permission" => null
                    )
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

    public static function get(string $table, $limit_or_column = null, $id = null) {
        if(self::$convertColumnToUppercase) {
            $limit_or_column = strtoupper($limit_or_column);
        } elseif(self::$convertColumnToLowercase) {
            $limit_or_column = strtolower($limit_or_column);
        }
        echo json(
                json_encode(
                    self::$restapi->get($table, $limit_or_column, $id)
                )
            );
    }

    public static function getUser(string $table, $limit_or_column = null, $id = null) {
        if(self::$convertColumnToUppercase) {
            $limit_or_column = strtoupper($limit_or_column);
        } elseif(self::$convertColumnToLowercase) {
            $limit_or_column = strtolower($limit_or_column);
        }
        echo json(
                json_encode(
                    self::$restapi->getUser($table, $limit_or_column, $id)
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