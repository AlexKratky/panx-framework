<a <?php href("test", "action-test:alex:10:[no,no,no]", "debug=false")?>>test</a>
<a href="/error/404?debug">404</a>

<?php
//require $_SERVER["DOCUMENT_ROOT"] . "/../app/forms/Form.php";
//require $_SERVER["DOCUMENT_ROOT"] . "/../app/forms/LoginForm.php";
$f = new LoginForm();
dump($f);
$f->render();
dump($_POST);
dump($f->validate());
dump($f->getValues());
dump($f->error());

?>

<?php
if(true) {
    //error(Route::ERROR_NOT_LOGGED_IN);
}
