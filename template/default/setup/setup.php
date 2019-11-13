<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>panx-framework setup</title>
    <style>
        * {
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
        }
        h3 {
            font-weight: 500;
        }
        input {
            width: 500px;
            max-width: 500px;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <?php
    if(isset($_GET["error"])) {
        echo "<h3 style='color: red;'>Failed to save your config!</h3>";
    }
    else if(isset($_GET["success"])) {
        echo "<h3 style='color: #00e800;'>Your config have been saved!</h3>";
    }
    ?>
    If you do not know how to fill this form, look to .config.example.
    <form method="POST" action="/_setup/save" id="form">
    <?php
    //config editor avaible on localhost/_setup
    //also you can test permissions on localhost/_setup/test
    global $CONFIG;
    foreach ($CONFIG as $key => $value) {
        echo "<h3>$key</h3>";
        foreach ($value as $k => $v) {
            if(!is_array($v)) {
                echo "".$k . ":<br><input value='".($v == "1" ? "true" : $v)."' name='$key:$k'><br><br>";
            } else {
                echo "".$k . ":<br><input value='[".(implode(", ", $v))."]' name='$key:$k'><br><br>";

            }
        }
    }
    ?>
    <button id="save" type="button">Save</button>
    </form>
    <h3>Add value to custom</h3>
    <input id="index" placeholder="Index name"><br><button id="add">Add</button>
    <script>
        document.getElementById("add").addEventListener("click", () => {
            if(document.getElementById("index").value.length > 0) {
                document.getElementById("save").insertAdjacentHTML('beforebegin', document.getElementById("index").value + ':<br><input name="custom:'+document.getElementById("index").value+'"><br><br>');
                document.getElementById("index").value = "";
            }
        });
        document.getElementById("save").addEventListener("click", () => {
            if(confirm("Submit form? WARNING: If you press yes, your current config will be overwritten!")) {
                document.getElementById("form").submit();
            }
        });
    </script>
    <h3>Permission test</h3>
    <a href="/_setup/test">
        <button>Test</button>
    </a>
</body>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">
</html>