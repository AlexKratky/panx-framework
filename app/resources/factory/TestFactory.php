<?php
class TestFactory extends Factory {
    public function generate($args = array()) {
        $c = 1;
        if(isset($args[0])) {
            if(is_numeric($args[0])) {
                $c = $args[0];
            }
        }
        for ($i=0; $i < $c; $i++) { 
            echo $this->factory->username . "\n";  
            db::query("INSERT INTO `restapi_test` (`USER_ID`, `TASK`, `COMPLETED`, `CREATED_AT`)
                VALUES (?, ?, ?, ?)",
                array(
                    $this->factory->numberBetween(11, 13),
                    $this->factory->words(3, true),
                    $this->factory->numberBetween(0, 1),
                    $this->factory->dateTimeThisDecade()->format('Y-m-d H:i:s')
                )
            );    
            usleep(100);
        }
    }
}