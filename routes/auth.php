<?php
Route::set('/login', 'auth/login.latte')->setController("AuthController");
Route::set('/login/verify', function() {
    $auth = $GLOBALS["auth"];
    ($auth->login()) ? redirect($GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"]) : redirect('/login');
}, ["POST"]);
Route::set('/login/forgot-password', 'auth/forgot.latte');
Route::set('/login/forgot-password/submit', function() {
    $auth = $GLOBALS["auth"];
    ($auth->forgot()) ? redirect("/login") : redirect('/login/forgot-password');

}, ["POST"]);
Route::set('/login/forgot-password/{TOKEN}', 'auth/forgot-new.latte');
Route::set('/login/forgot-password/{TOKEN}/save', function () {
    $auth = $GLOBALS["auth"];
    ($auth->forgotSave()) ? redirect("/login") : redirect('/login/forgot-password/' . Route::getValue('TOKEN'));
}, ["POST"]);

Route::set('/register', 'auth/register.latte')->setController("AuthController");
Route::set('/register/submit', function() {
    $auth = $GLOBALS["auth"];
    ($auth->register()) ? redirect('/login') : redirect('/register');
}, ["POST"]);
Route::set('/edit', 'auth/edit.latte')->setController("AuthController");
Route::set('/edit/save', function() {
    $auth = $GLOBALS["auth"];
    $auth->edit();
    redirect('/edit');
}, ["POST"]);
Route::set('/logout', function () {
    $auth = $GLOBALS["auth"];
    $auth->logout();
});
Route::set('/verifymail/{TOKEN}', function() {
    $auth = $GLOBALS["auth"];
    $v = $auth->verify(Route::getValue("TOKEN"));
    ($v === true) ? redirect($GLOBALS["CONFIG"]["auth"]["LANDING_PAGE"]) : die('Invalid token');
})->setMiddleware(['AuthMiddleware']);
Route::set('/session', function() {
    dump($_SESSION);
});
Route::set('/cookies', function() {
    dump($_COOKIE);
});
