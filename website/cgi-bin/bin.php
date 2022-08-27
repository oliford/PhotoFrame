<?php
include 'inc.php';

$nextImage = trim(file_get_contents($dataPath . '/nextImage.txt'));
$imageNum = trim(file_get_contents($dataPath . '/currentImage.txt'));
$nImages = trim(file_get_contents($dataPath . '/nImages.txt'));


$imageNum = $nextImage;
$nextImage = $imageNum + 1;
if ($nextImage >= $nImages)
    $nextImage = 0;

file_put_contents($dataPath . "/nextImage.txt", $nextImage);

$file = $imagePath . '/bin/image' . $imageNum . '.bin';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);

    file_put_contents($dataPath . "/currentImage.txt", $imageNum);
    file_put_contents($dataPath . "/lastUpdate.txt", time());
    exit;

}else{
    header('Content-Type: text/html');
    header("HTTP/1.1 500 Internal Server Error");
    echo 'No file image' . $imageNum . '.bin';
    exit;
}
?>
