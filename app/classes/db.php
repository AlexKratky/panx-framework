<?php
/**
 * @name db.php
 * @link https://alexkratky.cz                          Author website
 * @link https://panx.eu/docs/                          Documentation
 * @link https://github.com/AlexKratky/panx-framework/  Github Repository
 * @author Alex Kratky <info@alexkratky.cz>
 * @copyright Copyright (c) 2019 Alex Kratky
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @description Class to work with database. Part of panx-framework.
 */

class db {
    /**
     * @var PDO $conn The connection to db.
     */
    private static $conn;
    public static $id;

    /**
     * @var array $settings The connection's settings.
     */
    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );

    /**
     * Create connection to database.
     * @param string $host The hostname of DB server.
     * @param string $user The username of DB.
     * @param string $pass The password of DB user.
     * @param string $db The database name.
     * @return PDO The new connection.
     */
    public static function connect($host, $user, $pass, $db)
    {
        if (!isset(self::$conn))
        {
            self::$conn = @new PDO(
                "mysql:host=$host;dbname=$db",
                $user,
                $pass,
                self::$settings
            );
        }
        return self::$conn;
    }

    /**
     * Execute query on DB.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function query($sql, $params = array())
    {
        if($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"])
            array_push($GLOBALS["database_queries"], array(
                $sql,
                $params
            ));
        $query = self::$conn->prepare($sql);
        $query->execute($params);
        self::$id = self::$conn->lastInsertId();
        return $query;
    }

    /**
     * Execute query on DB and fetch the result.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function select($sql, $params = array()) {
        if($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"])
            array_push($GLOBALS["database_queries"], array(
                $sql,
                $params
            ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetch();
        return $data;
    }

    /**
     * Execute query on DB and fetch all rows of result.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function multipleSelect($sql, $params = array()) {
        if($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"])
            array_push($GLOBALS["database_queries"], array(
                $sql,
                $params
            ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetchAll();
        return $data;
    }

    /**
     * Execute query on DB and fetch all rows of result as assoc array.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function multipleSelectAssoc($sql, $params = array()) {
        if($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"])
            array_push($GLOBALS["database_queries"], array(
                $sql,
                $params
            ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Returns the number provided by COUNT query.
     * @param string $sql The query. Use ? for parameters.
     * @param array $params The array of parameters.
     */
    public static function count($sql, $params = array()) {
        if($GLOBALS["CONFIG"]["basic"]["APP_DEBUG"])
            array_push($GLOBALS["database_queries"], array(
                $sql,
                $params
            ));
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetch();
        return $data[0];
    }
}