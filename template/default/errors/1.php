<!DOCTYPE html>
	<head>
		<title><?=$GLOBALS["CONFIG"]["basic"]["APP_NAME"]?> | Error middleware</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="/res/css/error.css">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
	</head>
	<body>
<?php
http_response_code(400);
die("<div class='error'><div class='error-title'>Error <span class='error-code'>Middleware</span></div><div class='error-msg'>".__('middleware', true)."</div></div></body></html>");
?>