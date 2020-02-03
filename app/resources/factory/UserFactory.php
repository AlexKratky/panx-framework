<?php
class UserFactory extends Factory {
    public function generate($args = array()) {
        $c = 1;
        if(isset($args[0])) {
            if(is_numeric($args[0])) {
                $c = $args[0];
            }
        }
        for ($i=0; $i < $c; $i++) { 
            echo $this->factory->username . "\n";  
            db::query("INSERT INTO `users` (`USERNAME`, `EMAIL`, `PASSWORD`, `VERIFIED`, `ROLE`, `BALANCE`, `CREATED_AT`, `FULLNAME`, `SCHOOL`, `TEAM`, `RATING`)
                VALUES (?, ?, ?, 1, 49, ?, CURRENT_TIMESTAMP(), ?, ?, ?, ?, '1'",
                array(
                    str_replace('.', '', $this->factory->username),
                    $this->factory->email,
                    '$2y$10$Cpu4EVWxI8r8H3gMdYfp2e5af12UH0ZMxINw1ihCULOJFmfaRA5Wa',
                    $this->factory->numberBetween(0, 2000),
                    $this->factory->name,
                    $this->factory->numberBetween(0, 30),
                    $this->factory->numberBetween(0, 50)
                )
            );    
            usleep(100);
        }
    }
}