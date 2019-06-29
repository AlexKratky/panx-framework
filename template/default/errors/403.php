<?php
global $CONFIG;
?>
<!DOCTYPE html>
	<head>
		<title><?=$CONFIG["basic"]["APP_NAME"]?> | Error 403</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		<link rel="shortcut icon" href="<?=$CONFIG["basic"]["APP_URL"]?>favicon.ico" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="<?=$CONFIG["basic"]["APP_URL"]?>res/css/error.css">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
	</head>
	<body>
<?php
http_response_code(403);
die("<div class='error'><div class='error-title'>Error <span class='error-code'>403</span></div><div class='error-msg'>".__('forbidden1')." \"" . $GLOBALS["request"]->getUrl()->getString() . "\" ".__('forbidden2')."</div></div></body></html>");
?>