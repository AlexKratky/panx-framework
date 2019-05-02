<?php
class db {
	private static $conn;

    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    );

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

    public static function query($sql, $params = array())
    {
        $query = self::$conn->prepare($sql);
        $query->execute($params);
        return $query;
    }

    public static function select($sql, $params = array()) {
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetch();
        return $data;
    }

    public static function multipleSelect($sql, $params = array()) {
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetchAll();
        return $data;
    }

    public static function count($sql, $params = array()) {
        $q = self::$conn->prepare($sql);
        $q->execute($params);
        $data = $q->fetch();
        return $data[0];
    }
}