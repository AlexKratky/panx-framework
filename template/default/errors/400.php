<?php
if (!isset($CONFIG)) {
    $CONFIG = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../.config", true);
}
?>
<!DOCTYPE html>
	<head>
		<title><?=$CONFIG["basic"]["APP_NAME"]?> | Error 400</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link rel="shortcut icon" href="<?=$CONFIG["basic"]["APP_URL"]?>favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="<?=$CONFIG["basic"]["APP_URL"]?>res/css/error.css">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
	</head>
	<body>
<?php
http_response_code(400);
die("<div class='error'><div class='error-title'>Error <span class='error-code'>400</span></div><div class='error-msg'>Bad request</div></div></body></html>");
?>