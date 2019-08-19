<?php
$version;
if (!class_exists('ZipArchive')) {
    error("ZipArchive is not installed.");
}

if (count($ARGS) == 1) {
    $version = file_get_contents("https://panx.eu/api/v1/getlatestversion");
    info_msg("No version passed, using the latest one: $version");
} else {
    $version = $ARGS[1];
    info_msg("Using version $version");
}

info_msg("Do you want to create backup? [Y/n]");
$b = read("");

if (strtolower($b) != "n") {
    if (!file_exists($PATH . "/backup/")) {
        mkdir($PATH . "/backup/") ? info_msg("Created backup folder.") : error("Failed to create backup folder");
    }

    $rootPath = $PATH;

    // Initialize archive object
    $zip = new ZipArchive();
    $t = time();
    $zip->open($PATH . '/backup/backup_' . $t . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($rootPath) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();
    info_msg("Backup saved to /backup/backup_$t.zip");
}

if (!file_exists($PATH . "/temp/")) {
    mkdir($PATH . "/temp");
}
file_put_contents($PATH . "/temp/$version.zip", fopen("https://panx.eu/download/$version.zip", 'r'));

$zip = new ZipArchive;
if ($zip->open($PATH . "/temp/$version.zip") === true) {
    if (!file_exists($PATH . "/temp/$version")) {
        mkdir($PATH . "/temp/$version");
    }

    $zip->extractTo($PATH . "/temp/$version");
    $zip->close();
    //Now we need to move files from temp dir to main dir

    /******************** */
    $path = $PATH . "/";
    $source = $PATH . "/temp/$version/";
    $folders = array($source);
    $index = 0;

    $SKIP = array();
    $ADDITIONAL_FILES = array();
    if (file_exists($PATH . "/update.skip")) {
        $SKIP = file_get_contents($PATH . "/update.skip");
        $SKIP = explode(PHP_EOL, $SKIP);
        for ($s = 0; $s < count($SKIP); $s++) {
            $SKIP[$s] = trim($SKIP[$s]);
            if (isset($SKIP[$s][0]) && $SKIP[$s][0] == "!") {
                //exception
                $SKIP[$s] = substr($SKIP[$s], 1);
                if (is_dir($source . $SKIP[$s])) {
                    info_msg("Adding folder (!): " . $source . $SKIP[$s]);
                    array_push($folders, $source . $SKIP[$s]);
                } else {
                    info_msg("Adding file (!): " . $source . $SKIP[$s]);
                    array_push($ADDITIONAL_FILES, $source . $SKIP[$s]);
                }
            } else if (!isset($SKIP[$s][0])) {
                info_msg("Undefined string offset: " . $SKIP[$s]);
            }
        }
    }

    $INFO_JSON = file_exists($PATH . "/info.json") ? json_decode(file_get_contents($PATH . "/info.json"), true) : array();

    while (count($folders) > $index) {
        $f = scandir($folders[$index]);
        $rel_path = $path . str_replace($source, "", $folders[$index]);
        if (!file_exists($rel_path)) {
            mkdir($rel_path);
        }

        for ($i = 2; $i < count($f); $i++) {
            // #+?\ .+?\n <- Match title
            if (is_dir($folders[$index] . $f[$i])) {
                if (in_array(str_replace($source, "", $folders[$index]) . $f[$i] . "/", $SKIP)) {
                    info_msg("Skipping folder: " . str_replace($source, "", $folders[$index]) . $f[$i] . "/");
                    continue;
                }
                if (!in_array($folders[$index] . $f[$i] . "/", $folders)) {
                    info_msg("Adding folder: " . str_replace($source, "", $folders[$index]) . $f[$i] . "/");
                    array_push($folders, $folders[$index] . $f[$i] . "/");
                } else {
                    info_msg("Skipping folder (duplicity): " . str_replace($source, "", $folders[$index]) . $f[$i] . "/");
                }
                continue;
            }

            if (!file_exists($rel_path . $f[$i])) {
                rename($folders[$index] . $f[$i], $rel_path . $f[$i]);
            } else {
                //Check if file was modified, if no, then overwrite, otherwise prompt for options
                //also check if file should be skipped, by searching in update.skip file
                if (!empty($SKIP)) {
                    //var_dump($SKIP);
                    if (in_array(str_replace($source, "", $folders[$index]) . $f[$i], $SKIP)) {
                        info_msg("Skipping: " . str_replace($source, "", $folders[$index]) . $f[$i]);
                        continue;
                    }
                }

                if (isset($INFO_JSON[str_replace($PATH, "", $rel_path . $f[$i])])) {
                    if (sha1_file($rel_path . $f[$i]) == $INFO_JSON[str_replace($PATH, "", $rel_path . $f[$i])]) {
                        //file is same, overwriting
                        info_msg("Overwriting: " . str_replace($source, "", $folders[$index]) . $f[$i]);
                        rename($folders[$index] . $f[$i], $rel_path . $f[$i]);

                    } else {
                        info_msg("Checking file " . $rel_path . $f[$i] . " (".sha1_file($rel_path . $f[$i]).") and " . str_replace($PATH, "", $rel_path . $f[$i]) . "(".$INFO_JSON[str_replace($PATH, "", $rel_path . $f[$i])].")");
                        info_msg("File: " . str_replace($source, "", $folders[$index]) . $f[$i] . "  was edited. Do you want to overwrite it [Y/n]");
                        $OW = read("");
                        if (strtolower($OW) != "n") {
                            info_msg("Overwriting: " . str_replace($source, "", $folders[$index]) . $f[$i]);
                            rename($folders[$index] . $f[$i], $rel_path . $f[$i]);
                        }
                    }
                } else {
                    var_dump($INFO_JSON[str_replace($PATH, "", $rel_path . $f[$i])]);
                    var_dump(str_replace($PATH, "", $rel_path . $f[$i]));
                    echo "\n";
                    echo "\n";
                    echo "\n";
echo "\n";
                    echo "\n";
                    var_dump($INFO_JSON);
                    //No data in info, need to confirm overwrite
                    info_msg("File: " . str_replace($source, "", $folders[$index]) . $f[$i] . "  already exists. Do you want to overwrite it [Y/n]");
                    $OW = read("");
                    if (strtolower($OW) != "n") {
                        info_msg("Overwriting: " . str_replace($source, "", $folders[$index]) . $f[$i]);
                        rename($folders[$index] . $f[$i], $rel_path . $f[$i]);
                    }

                }

                //echo(str_replace($source, "", $folders[$index]) . $f[$i] . "\n");
            }

            //file_put_contents($rel_path .  basename($f[$i], ".md") . ".php", "<title>".$CONFIG["basic"]["APP_NAME"]." | Documentation | ".$matches[1]." </title>" . $Parsedown->text(file_get_contents($folders[$index].$f[$i])));
        }
        $index++;
    }
    # EDIT
    foreach ($ADDITIONAL_FILES as $ADDITIONAL_FILE) {
        $rel_path = str_replace($source, "", $ADDITIONAL_FILE);
        if (!file_exists($rel_path)) {
            rename($ADDITIONAL_FILE, $rel_path);
        } else {
            if (isset($INFO_JSON[str_replace($PATH, "", $rel_path)])) {
                if (sha1_file($rel_path) == $INFO_JSON[str_replace($PATH, "", $rel_path)]) {
                    //file is same, overwriting
                    info_msg("Overwriting: " . $ADDITIONAL_FILE);
                    rename($ADDITIONAL_FILE, $rel_path);

                } else {
                    info_msg("File: " . $ADDITIONAL_FILE . "  was edited. Do you want to overwrite it [Y/n]");
                    $OW = read("");
                    if (strtolower($OW) != "n") {
                        info_msg("Overwriting: " . $ADDITIONAL_FILE);
                        rename($ADDITIONAL_FILE, $rel_path);
                    }
                }
            } else {
                //No data in info, need to confirm overwrite
                info_msg("File: " . $ADDITIONAL_FILE . "  already exists. Do you want to overwrite it [Y/n]");
                $OW = read("");
                if (strtolower($OW) != "n") {
                    info_msg("Overwriting: " . $ADDITIONAL_FILE);
                    rename($ADDITIONAL_FILE, $rel_path);
                }

            }

            //echo(str_replace($source, "", $folders[$index]) . $f[$i] . "\n");
        }
    }
# /EDIT
    /******************** */
    info_msg("Updated to $version.");
} else {
    info_msg("Failed to install.");
    exit();
}
if (file_exists($PATH . "/temp/$version/changelog")) {
    displayChangelog($PATH . "/temp/$version/changelog");
}

unlink($PATH . "/temp/$version.zip");

rrmdir($PATH . "/temp/$version/");

require $PATH . "/app/classes/Cache.php";
require $PATH . "/app/classes/Logger.php";

Cache::clearAll($PATH);
