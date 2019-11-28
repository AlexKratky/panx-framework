<?php
Route::apiGroup("v1", array(
    array("getlatestversion", function() {
        echo "0.3.2";
    }),
));