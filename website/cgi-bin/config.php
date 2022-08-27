<?php
include 'inc.php';

$file = $dataPath . '/pollPeriod.txt';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;

}else{
    header('Content-Type: text/html');
    header("HTTP/1.1 500 Internal Server Error");
    echo 'No file config.txt';
    exit;
}
?>
