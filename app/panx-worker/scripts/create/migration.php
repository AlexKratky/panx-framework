<?php
if (!isset($ARGS[2])) {
    $name = read("Enter name of the table");
    if ($name == "") {
        error("You need to entertable name.");
    }
} else {
    $name = $ARGS[2];
}

$dir = $PATH . "/app/migrations/";
$code = '<?php
class table_'.$name.'_migration {
    public function create() {
        return TableSchema::create("'.$name.'")
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
        return TableSchema::drop("'.$name.'");
    }
}';

file_put_contents($dir . time() . "_" . $name . ".php", $code);
