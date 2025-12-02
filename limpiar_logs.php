<?php
$log = ini_get("error_log");

if ($log && file_exists($log)) {
    file_put_contents($log, "");
    echo "ok";
} else {
    echo "no_log";
}
?>
