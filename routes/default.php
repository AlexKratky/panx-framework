<?php
Route::setError(Route::ERROR_MIDDLEWARE, "default/errors/1.php");
Route::setError(Route::ERROR_BAD_REQUEST, "default/errors/400.php");
Route::setError(Route::ERROR_FORBIDDEN, "default/errors/403.php");
Route::setError(Route::ERROR_NOT_FOUND, "default/errors/404.php");
Route::setError(Route::ERROR_NOT_LOGGED_IN, "default/errors/not_logged_in.php");
Route::set('/git-deploy', function() {
    //if($GLOBALS["request"]->getHeader("x-hub-signature") == "sha1=4bed6d143679f8a71db502ad22585a1d1530a597") {
    /*$fp = fopen($_SERVER["DOCUMENT_ROOT"] . '/../temp/test.zip', 'w+');
    echo sha1_file($_SERVER["DOCUMENT_ROOT"] . '/../temp/test.zip');
    $giturl = 'https://api.github.com/repos/AlexKratky/panx-framework/zipball/develop?access_token=37c3467f661e2a44034bec9acb8c779bb19757ab';
    $ch = curl_init($giturl);
    //set file to write to
    curl_setopt($ch, CURLOPT_FILE, $fp);
    //the API will not allow you to download without a user agent so CURLOPT_USERAGENT is important.
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP/' . phpversion('tidy'));
    //The API URL redirects so the following line is very important
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $output = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    //Get the HTTP status code.
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //Close the cURL handler.
    curl_close($ch);

    //Output result
    if ($statusCode == 200) {
        echo 'Downloaded Successfully';
    } else {
        echo "Failed downloading - Status Code: " . $statusCode;
    }

    Logger::log(shell_exec("ssh-agent bash -c 'ssh-add /var/www/panx-framework; git pull git develop'"), "git.log");
    Logger::log("Pulled.", "git.log");
    //} else {
    Logger::log($GLOBALS["request"]->getHeader("x-hub-signature"), "git.log");
    //}
    Logger::log(json_encode(file_get_contents('php://input')), "git.log");
    Logger::log(json_encode($GLOBALS["request"]->getHeaders()), "git.log");
    dump(file_get_contents('php://input'));*/
  
/**
 * GIT DEPLOYMENT SCRIPT
 *
 * Used for automatically deploying websites via github or bitbucket, more deets here:
 * https://gist.github.com/riodw/71f6e2244534deae652962b32b7454e2
 * How To Use:
 * https://medium.com/riow/deploy-to-production-server-with-git-using-php-ab69b13f78ad
 */
// The commands
$commands = array(
    'echo $PWD',
    'whoami',
    'git reset --hard HEAD',
    'git pull origin develop',
    'git status',
    'git submodule sync',
    'git submodule update',
    'git submodule status',
);
// Run the commands for output
$output = '';
foreach ($commands as $command) {
    // Run it
    $tmp = shell_exec($command);
    // Output
    $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";
}
// Make it pretty for manual user access (and why not?)
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>GIT DEPLOYMENT SCRIPT</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>


<?php echo $output; ?>
</pre>
</body>
</html>
<?php
//test of auto deploy
});