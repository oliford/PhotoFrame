<html>
<head>
<title>Photoframe setup - makebin</title>
</head>
<body>
<?php

include 'inc.php';

$nImages = trim(file_get_contents($dataPath . '/nImages.txt'));
$config = trim(file_get_contents($dataPath . '/config.txt'));
$lastUpdate = trim(file_get_contents($dataPath . '/lastUpdate.txt'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageNum = $_POST['imageNum'];
}else{
    $imageNum = $_GET['imageNum'];
}

$imageFile = $imagePath . "/converted/image" . $imageNum . ".png";
$binFile = $imagePath . "/bin/image" . $imageNum . ".bin";

echo "<p>Making binary for image " . $imageNum . "</p>";

$cmd = "/usr/bin/python3 makeBin.py ". $imageFile . " " . $binFile . " " . $width . " " . $height;
//echo "cmd = ". $cmd . "<BR>";
exec($cmd, $output, $retVal);

if ($retVal != 0 ){
   echo "<p>Making the binary blob failed! The command executed was:" . $cmd;
}else{
   echo "<p>The binary blob was created for image number " . $imageNum . ". It will appear on the photo frame (one day)</p>";
   
   if ($imageNum >= $nImages){
       file_put_contents($dataPath . "/nImages.txt", ($imageNum+1));
      
   }
}

echo "<p><a href='/cgi-bin/photoFrame/setup.php?frameID=" . $frameID . "'>Back to main page</a></p>";
?>
</body>
