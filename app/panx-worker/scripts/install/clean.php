<?php
// Extract zip to temp folder, move all files that are not in update.skip of THAT version (so /temp/xxx/update.skip file)
if (!file_exists($PATH . "/temp/$version/")) {
    mkdir($PATH . "/temp/$version");
}
$zip = new ZipArchive;
if ($zip->open($PATH . "/temp/$version.zip") === true) {
    $zip->extractTo($PATH . "/temp/$version/");
    $zip->close();
    $path = $PATH . "/";
    $source = $PATH . "/temp/$version/";
    $folders = array($source);
    $index = 0;

    $SKIP;
    $ADDITIONAL_FILES = array();
    if (file_exists($PATH . "/temp/$version/update.skip")) {
        $SKIP = file_get_contents($PATH . "/temp/$version/update.skip");
        $SKIP = explode(PHP_EOL, $SKIP);
        for ($s = 0; $s < count($SKIP); $s++) {
            $SKIP[$s] = trim($SKIP[$s]);
            if (isset($SKIP[$s][0]) && $SKIP[$s][0] == "!") {
                //exception
                $SKIP[$s] = substr($SKIP[$s], 1);
                if (is_dir($source . $SKIP[$s])) {
                    echo ("Adding folder (!): " . $source . $SKIP[$s] . "\n");
                    array_push($folders, $source . $SKIP[$s]);
                } else {
                    echo ("Adding file (!): " . $source . $SKIP[$s] . "\n");
                    array_push($ADDITIONAL_FILES, $source . $SKIP[$s]);
                }
            }
        }
    }

    while (count($folders) > $index) {
        $f = scandir($folders[$index]);
        $rel_path = $path . str_replace($source, "", $folders[$index]);
        if (!file_exists($rel_path)) {
            mkdir($rel_path, 0775, true);
        }

        for ($i = 2; $i < count($f); $i++) {
            // #+?\ .+?\n <- Match title
            if (is_dir($folders[$index] . $f[$i])) {
                if (in_array(str_replace($source, "", $folders[$index]) . $f[$i] . "/", $SKIP)) {
                    echo ("Skipping folder: " . str_replace($source, "", $folders[$index]) . $f[$i] . "/\n");
                    continue;
                }
                if (!in_array($folders[$index] . $f[$i] . "/", $folders)) {
                    echo ("Adding folder: " . str_replace($source, "", $folders[$index]) . $f[$i] . "/\n");
                    array_push($folders, $folders[$index] . $f[$i] . "/");
                } else {
                    echo ("Skipping folder (duplicity): " . str_replace($source, "", $folders[$index]) . $f[$i] . "/\n");
                }
                continue;
            }

            if (!empty($SKIP)) {
                //var_dump($SKIP);
                if (in_array(str_replace($source, "", $folders[$index]) . $f[$i], $SKIP)) {
                    echo ("Skipping: " . str_replace($source, "", $folders[$index]) . $f[$i] . "\n");
                    continue;
                } else {
                    rename($folders[$index] . $f[$i], $rel_path . $f[$i]);

                }
            }

        }
        $index++;
    }

    foreach ($ADDITIONAL_FILES as $ADDITIONAL_FILE) {
        $rel_path = str_replace($source, "", $ADDITIONAL_FILE);
        if (!file_exists(pathinfo($rel_path)['dirname'] . "/")) {
            echo ("Error: Folder doesnt exists " . pathinfo($rel_path)['dirname'] . "/" . "\n");
            if (mkdir(pathinfo($rel_path)['dirname'] . "/", 0775, true)) {
                echo ("folder created " . pathinfo($rel_path)['dirname'] . "/" . "\n");

            } else {
                echo ("error: folder cant be created " . pathinfo($rel_path)['dirname'] . "/" . "\n");
            }
        }
        if (!file_exists($ADDITIONAL_FILE)) {
            echo ($ADDITIONAL_FILE . " doesnt exists.");
            continue;
        }
        if (is_writable($PATH . "/" . $rel_path)) {
            echo ($PATH . "/" . $rel_path . " : true\n");
        } else {
            echo ($PATH . "/" . $rel_path . " : false\n");

        }
        try {
            if (!file_exists(pathinfo($rel_path)['dirname'] . "/")) {
                echo ("Error: Folder doesnt exists\n");
            }
            usleep(20);
            file_put_contents($PATH . "/" . $rel_path, "test");
            usleep(20);

            rename($ADDITIONAL_FILE, $PATH . "/" . $rel_path);
        } catch (Exception $e) {
            echo ("Exception:\n$e\n");
        }
    }
    if (!file_exists($PATH . "/routes/route.php")) {
        file_put_contents($PATH . "/routes/route.php", "<?php\r\n");
    }

    if (file_exists($PATH . "/temp/$version/changelog")) {
        displayChangelog($PATH . "/temp/$version/changelog");
    }
    echo ("$version was installed successfuly.\n");

} else {
    echo ("Failed to install.\n");
    exit();
}
