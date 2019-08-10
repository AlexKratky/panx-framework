<?php
/**
 * @name TableSchema.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Creates DB Table. Part of panx-framework.
 */

class TableSchema {
    private $table;
    private $columns = array();
    private $primary_key_column_name;
    private $unique_keys_column_name = array();
    private $foreign_keys = "";
    private $indexed_keys_column_name = array();

    public const DEFAULT_LENGHT = -1;
    public const PRIMARY_KEY = 1;
    public const UNIQUE_KEY = 2;
    public const AUTO_INCREMENT = 3;
    public const UNSIGNED = 4;
    public const CURRENT_TIMESTAMP = 5;

    public const TYPE_INT = 20;
    public const TYPE_TINY_INT = 21;
    public const TYPE_STRING = 22;
    public const TYPE_TIMESTAMP = 23;
    public const TYPE_TEXT = 24;
    public const TYPE_ENUM = 25;


    /*
    TODO:
     * text
     * enum
     */

    /**
     * Creates TableSchema with specified table name.
     * @param string $table_name The name of table to create.
     */
    public function __construct($table_name) {
        $this->table = $table_name;
    }

    /**
     * Adds collumn of int type to specified table.
     * @param string $name The column name.
     * @param int $lenth The column length.
     * @param array $options Custom options, see documentation to view all available options.
     */
    public function int($name, $length = -1, $options = array()) {
        array_push($this->columns, array($name, self::TYPE_INT, $length, $options));
        if(isset($options["primary"]) && ($options["primary"] == true)) {
            $this->primary_key_column_name = $name;
        }
        elseif(isset($options["unique"]) && ($options["unique"] == true)) {
            array_push($this->unique_keys_column_name, $name);
        } elseif(isset($options["index"]) && ($options["index"] == true)) {
            array_push($this->indexed_keys_column_name, $name);            
        }
        return $this;
    }

    /**
     * Adds collumn of tinyint type to specified table.
     * @param string $name The column name.
     * @param int $lenth The column length.
     * @param array $options Custom options, see documentation to view all available options.
     */
    public function tinyInt($name, $length = -1, $options = array()) {
        array_push($this->columns, array($name, self::TYPE_TINY_INT, $length, $options));
        if(isset($options["primary"]) && ($options["primary"] == true)) {
            $this->primary_key_column_name = $name;
        }
        elseif(isset($options["unique"]) && ($options["unique"] == true)) {
            array_push($this->unique_keys_column_name, $name);
        } elseif(isset($options["index"]) && ($options["index"] == true)) {
            array_push($this->indexed_keys_column_name, $name);            
        }
        return $this;

    }

    /**
     * Adds collumn of varchar type to specified table.
     * @param string $name The column name.
     * @param int $lenth The column length.
     * @param array $options Custom options, see documentation to view all available options.
     */
    public function string($name, $length = -1, $options = array()) {
        array_push($this->columns, array($name, self::TYPE_STRING, $length, $options));
        if(isset($options["primary"]) && ($options["primary"] == true)) {
            $this->primary_key_column_name = $name;
        }
        elseif(isset($options["unique"]) && ($options["unique"] == true)) {
            array_push($this->unique_keys_column_name, $name);
        } elseif(isset($options["index"]) && ($options["index"] == true)) {
            array_push($this->indexed_keys_column_name, $name);            
        }
        return $this;

    }

    /**
     * Adds collumn of timestamp type to specified table.
     * @param string $name The column name.
     * @param int $lenth The column length.
     * @param array $options Custom options, see documentation to view all available options.
     */
    public function timestamp($name, $length = -1, $options = array()) {
        array_push($this->columns, array($name, self::TYPE_TIMESTAMP, $length, $options));
        if(isset($options["primary"]) && ($options["primary"] == true)) {
            $this->primary_key_column_name = $name;
        }
        elseif(isset($options["unique"]) && ($options["unique"] == true)) {
            array_push($this->unique_keys_column_name, $name);
        } elseif(isset($options["index"]) && ($options["index"] == true)) {
            array_push($this->indexed_keys_column_name, $name);            
        }
        return $this;

    }

    /**
     * Adds collumn of text type to specified table.
     * @param string $name The column name.
     * @param array $options Custom options, see documentation to view all available options.
     */
    public function text($name, $options = array()) {
        array_push($this->columns, array($name, self::TYPE_TEXT, self::DEFAULT_LENGHT, $options));
        return $this;
    }

    /**
     * Adds collumn of enum type to specified table.
     * @param string $name The column name.
     * @param array $options Custom options, see documentation to view all available options.
     */
    public function enum($name, $options = array()) {
        array_push($this->columns, array($name, self::TYPE_ENUM, self::DEFAULT_LENGHT, $options));
        return $this;
    }

    /**
     * Creates a foreign key reference.
     * @param string $column_name The name of column in current table that will be foreign name.
     * @param string $foreign_table The name of table where is the foreign key source.
     * @param string $foreign_column The name of column which is the source for foreign key.
     */
    public function foreignKey($column_name, $foreign_table, $foreign_column) {
        $this->foreign_keys .= ",\r\n\tCONSTRAINT `$column_name` FOREIGN KEY (`$column_name`) REFERENCES `$foreign_table` (`$foreign_column`)";
        return $this;
    }

    /**
     * Saves the table to DB.
     */
    public function save() {
        $unique = "";
        foreach ($this->unique_keys_column_name as $column) {
            $unique .= ",\r\n\tUNIQUE INDEX `$column` (`$column`)";
        }
        $indexed = "";
        foreach ($this->indexed_keys_column_name as $column) {
            $indexed .= ",\r\n\tINDEX `$column` (`$column`)";
        }
        //$unique = rtrim(rtrim($unique), ",");
        $columns = "";
        foreach ($this->columns as $column) {
            $default = null;
            if(isset($column[3]["default"])) {
                if($column[3]["default"] !== TableSchema::CURRENT_TIMESTAMP) {
                    $default = "DEFAULT '{$column[3]["default"]}'";
                } else {
                    $default = "DEFAULT CURRENT_TIMESTAMP";
                }
            } else {
                if(isset($column[3]["AI"]) && $column[3]["AI"] == true) {
                    $default = "AUTO_INCREMENT";
                }
            }
            //var_dump($column);
            $type;
            switch ($column[1]) {
                case self::TYPE_INT:
                    $type = "INT(".($column[2] === self::DEFAULT_LENGHT ? 11 : $column[2]).")" . (isset($column[3]["unsigned"]) && ($column[3]["unsigned"] == true) ? " UNSIGNED" : "");
                    if($default === null) {
                        if(!in_array($column[0], $this->unique_keys_column_name) && ($column[0] !== $this->primary_key_column_name) && !in_array($column[0], $this->indexed_keys_column_name)) {
                            $default = "DEFAULT '0'";
                        }
                    }
                    break;
                case self::TYPE_TINY_INT:
                    $type = "TINYINT(".($column[2] === self::DEFAULT_LENGHT ? 4 : $column[2]).")" . (isset($column[3]["unsigned"]) && ($column[3]["unsigned"] == true) ? " UNSIGNED" : "");
                    if($default === null) {
                        if(!in_array($column[0], $this->unique_keys_column_name) && ($column[0] !== $this->primary_key_column_name) && !in_array($column[0], $this->indexed_keys_column_name)) {
                            $default = "DEFAULT '0'";
                        }
                    }
                    break;
                case self::TYPE_STRING:
                    $type = "VARCHAR(".($column[2] === self::DEFAULT_LENGHT ? 128 : $column [2]).")";
                    if($default === null) {
                        if(!in_array($column[0], $this->unique_keys_column_name) && ($column[0] !== $this->primary_key_column_name) && !in_array($column[0], $this->indexed_keys_column_name)) {
                            $default = "DEFAULT NULL";
                        }
                    }
                    break;
                case self::TYPE_TIMESTAMP:
                    $type = "TIMESTAMP";
                    if($default === null) {
                        if(!in_array($column[0], $this->unique_keys_column_name) && ($column[0] !== $this->primary_key_column_name) && !in_array($column[0], $this->indexed_keys_column_name)) {
                            $default = "DEFAULT NULL";
                        }
                    }
                    break;
                case self::TYPE_TEXT:
                    $type = "TEXT";
                    break;
                case self::TYPE_ENUM:
                    $enums = "";
                    foreach($column[3]["values"] as $value) {
                        $enums .= "'$value',";
                    }
                    $enums = rtrim($enums, ",");
                    $type = "ENUM($enums)";
                    if($default === null) {
                        if(!in_array($column[0], $this->unique_keys_column_name) && ($column[0] !== $this->primary_key_column_name) && !in_array($column[0], $this->indexed_keys_column_name)) {
                            $default = "DEFAULT NULL";
                        }
                    }
                    break;
            }
            $columns .= "\t`{$column[0]}` " . ($type) . (in_array($column[0], $this->unique_keys_column_name) || ($column[0] === $this->primary_key_column_name) || in_array($column[0], $this->indexed_keys_column_name) ? " NOT" : "") . " NULL" . ($default === null ? "" : " " . $default) . (isset($column[3]["on_update"]) ? " ON UPDATE " . ($column[3]["on_update"] === self::CURRENT_TIMESTAMP ? "CURRENT_TIMESTAMP" : "{$column[3]["on_update"]}") : "") . ($column[1] === self::TYPE_STRING || $column[1] === self::TYPE_TEXT || $column[1] === self::TYPE_ENUM ? " COLLATE 'utf8mb4_bin'" : "") .  ",\r\n";
        }
        $query = "CREATE TABLE `$this->table` (
    {$columns}\tPRIMARY KEY (`$this->primary_key_column_name`)".($unique).($indexed).($this->foreign_keys)."
)
COLLATE='utf8mb4_bin'
ENGINE=InnoDB
AUTO_INCREMENT=1;";
        db::query($query);
        return "Table '{$this->table}' created.";
    }
        
    /**
     * The shorthand for creating tables.
     */
    public static function create($table) {
        return new TableSchema($table);
    }

    public static function drop($table) {
        db::query("DROP TABLE $table");
        return "Table '$table' deleted.";
    }
}