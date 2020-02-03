<?php
Route::set('/', "home.php")->setTitle("php micro framework")->setAlias('home');
Route::set('/panx/download', "download.latte")->setTitle("Download files");
Route::set('/panx/download/extensions', "download_extensions.latte")->setTitle("Download files");


Route::set('/test', function() {
    $f = new LoginForm();
    dump($f);
    $f->render();
    dump($_POST);
    dump($f->validate());
    dump($f->getValues());
    dump($f->error());
    /*dump(__("test"));
    $mailer = new Mail();
    $mailer->subject('Verify your email');
    $mailer->message('Verify your email on address adress. You need to be logged in to verify your email.');
    $mailer->send("ak@example.com");
    $mailer->subject('Verify your email2');
    $mailer->send("ak@example.com");*/

    echo "<form method='post' action='/test/submit/form'><input name='name'><input name='pass' type='password'><button>ok</button></form>";
    Logger::log('testtt');
});

Route::set('/test/{ID}', function() {});
Route::set('/test/{ID}/{ACTIP}', function() {
    redirect("/test");
});


Route::set('/test/submit', function() {
    $f = new LoginForm();
    if($f->validate()) {
        dump($f->getValues());
    } else {
        redirect('/test');
    }
});