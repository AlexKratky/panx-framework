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
        if($isApiKeyRequired && !self::$api->request(new URL())) {
            echo json(self::$api->error());
            exit();
        }
        Logger::log(json_encode(array(
                "restapi_test" => array(
                    "columns" => ["ID", "USER_ID", "TASK", "COMPLETED", "CREATED_AT"],
                    "getByColumn" => null,
                    "permission" => "get_all_todos",
                    "select_all_permission" => null,
                    "select_all_permission_user" => array(
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
            // TODO: TEST CREATE
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
            // TODO : TEST update
            //update
            array(
                "users" => array(
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
            //delete
            array(
                "api_keys" => array(
                    "columns" => ["ID"], // columns that can be used to indetify the row to delete
                    "getByColumn" => null,
                    "permission" => "delete_table_api_keys",
                    "user_row" => array(
                        "column" => "USER_ID",
                        "user" => "id",
                        "permission" => null
                    )
                )
                    )));
        self::$restapi = new RestAPI(true,
            //get
            array(
                "restapi_test" => array(
                    "columns" => ["ID", "USER_ID", "TASK", "COMPLETED", "CREATED_AT"],
                    "getByColumn" => null,
                    "permission" => "get_all_todos",
                    "select_all_permission" => null,
                    "select_all_permission_user" => array(
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
            // TODO: TEST CREATE
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
            // TODO : TEST update
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