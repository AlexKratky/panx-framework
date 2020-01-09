<?php
abstract class Model {
    public $TABLE_NAME;

    public function findColumnById($id, $column, $uppercase = false) {
        if(!$uppercase)
            return db::select("SELECT * FROM {$this->TABLE_NAME} WHERE id=?", array($id))[$column];
        return db::select("SELECT * FROM {$this->TABLE_NAME} WHERE ID=?", array($id))[$column];
    }

    public function findRowById($id, $uppercase = false) {
        if(!$uppercase)
            return db::select("SELECT * FROM {$this->TABLE_NAME} WHERE id=?", array($id));
        return db::select("SELECT * FROM {$this->TABLE_NAME} WHERE ID=?", array($id));
    }
}
