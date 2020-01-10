<?php
Route::set('/', "home.php")->setTitle("php micro framework");

Route::set('/test', function() {
    $f = new LoginForm();
    dump($f);
    $f->render();
    dump($_POST);
    dump($f->validate());
    dump($f->getValues());
    dump($f->error());
});


Route::set('/test/submit', function() {
    $f = new LoginForm();
    if($f->validate()) {
        dump($f->getValues());
    } else {
        redirect('/test');
    }
});