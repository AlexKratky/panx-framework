<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Permission test</title>
    <style>
        * {
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
        }
        h3 {
            font-weight: 500;
        }
    </style>
</head>
<body>
<?php
/*
Need to check owner, group, chmod and try to write something
and also all files inside
*/
$dirs = array(
    'temp', 
    'cache', 
    'logs', 
    'template/posts', 
    'app/controllers', 
    'app/middlewares', 
    'app/models', 
    'app/migrations', 
    'crons'
);
//all files there should be writable 
$writable = array(
    'temp',
    'cache',
    'logs'
);

$error_count = 0;
$error_msgs = [];
foreach ($dirs as $dir => $value) {
    e("Checking if $value exits.");
    if(!file_exists($_SERVER["DOCUMENT_ROOT"] . "/../" . $value)) {
        e("Directory $value does not exists", true);
        e("Creating $value");
        if(!mkdir($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/', 0777, true)) {
            e("Failed to create: " . $value, true);
            $error_count++;
            array_push($error_msgs, "Failed to create: " . $value);
        }
    }

    e("Checking chmod of $value ");
    if(substr(sprintf('%o', fileperms($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/')), -4) != "0777") {
        e("$value does not have chmod 0777", true);
        e("Changing chmod for $value");
        if(!chmod($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/', 0777)) {
            e("Failed to change chmod to 0777 on $value", true);
            $error_count++;
            array_push($error_msgs, "Failed to change chmod to 0777 on $value");
        }
    }

/*
! not working
    if((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
        //no need to check group
    } else {
        e("Changing group of $value");
        if(!chgrp( __DIR__.'/'.$dir.'/', 'www-data')) {
            e("Failed to change group to www-data on $value (Maybe the dir already have that group, but this script can't check it)", true);
            $error_count++;
            array_push($error_msgs, "Failed to change group to www-data on $value (Maybe the dir already have that group, but this script can't check it, so you can ignore this error)");
        }
    }
*/

    $t = time();
    e("Test write to $value");
    if(!file_put_contents($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/'.$t.".testfile", time() . "")) {
        e("Failed to write to $value", true);
        $error_count++;
        array_push($error_msgs, "Failed to write to $value");
    }


    e("Deleting from $value");
    if(!unlink($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/'.$t.".testfile")) {
        e("Failed to delete from $value", true);
        $error_count++;
        array_push($error_msgs, "Failed to delete from $value");
    }

    
    if(in_array($value, $writable)) {
        e("Checking all files in $value if have chmod 0777");
        $f = scandir($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/');
        foreach ($f as $file) {
            if($file == "." || $file == "..")
                continue;
            if(substr(sprintf('%o', fileperms($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/'.$file)), -4) != "0777") {
                e("$value/$file does not have chmod 0777", true);
                e("Changing chmod for $value/$file");
                if(!chmod($_SERVER["DOCUMENT_ROOT"].'/../'.$value.'/'.$file, 0777)) {
                    e("Failed to change chmod to 0777 on $value/$file", true);
                    $error_count++;
                    array_push($error_msgs, "Failed to change chmod to 0777 on $value/$file");
                }
            }
        }
    }
}


echo "<h3>Test completed</h3>";
echo "Error that can not be fixed: $error_count <br>";
if($error_count > 0) {
    echo "Errors that were not fixed: <br>";
    foreach ($error_msgs as $error_msg) {
        e($error_msg);
    }
}
echo "<br><a href='/_setup'><button>Go back</button></a>";

function e($msg, $error = false) {
    if($error) {
        echo "<span style='color:red;'>$msg</span><br>";
    } else {
        echo $msg . "<br>";
    }
}

?>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">

</body>
</html>