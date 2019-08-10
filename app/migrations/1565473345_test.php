<?php
class table_test_migration {
    public function create() {
        return TableSchema::create("test")
            ->int("ID", 11, [
                "primary" => true,
                "AI" => true,
                "unsigned" => true
            ])
            ->timestamp("CREATED_AT", TableSchema::DEFAULT_LENGHT)
            ->timestamp("EDITED_AT", TableSchema::DEFAULT_LENGHT, [
                "default" => TableSchema::CURRENT_TIMESTAMP,
                "on_update" => TableSchema::CURRENT_TIMESTAMP,
            ])
            ->save();
    }

    public function delete() {
        return TableSchema::drop("test");
    }
}