<html>
<head>
<title>Photoframe setup</title>
</head>
<body>
<?php

include 'inc.php';
require 'convert.php';

$nImages = trim(file_get_contents($dataPath . '/nImages.txt'));


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $op = $_POST['op'];
   $imageNum = $_POST['imageNum'];
   $message = $_POST['message'];
}else{
   $op = $_GET['op'];
   $imageNum = $_GET['imageNum'];
   $message = $_GET['message'];
}

if ($op == "setNext"){
    file_put_contents($dataPath . "/nextImage.txt", $imageNum);
}elseif($op == "delete"){
  //??
   echo "Would delete image" . $imageNum;
}elseif($op == "addText"){
   $sourceFiles = glob($imagePath . "/source/image" . $imageNum . ".*", 0);
   if(count($sourceFiles) <= 0){
      echo "<p>ERROR: There is no source image for image". $imageNum . ". Please upload the original</p>";
      return;
   }else {
      echo "<p>Message added.</p>";
      convert($imagePath, $frameID, $sourceFiles[0], $imageNum, $message);
   }
   return;
}
?>

<h1>Photo Frame Setup - Manage image <?php echo $imageNum ?></h1>
<p><a href='/cgi-bin/photoFrame/setup.php'>Back to main page</a></p>

<table><tr><td><img src='/photoFrame/<?php echo $frameID; ?>/converted/image<?php echo $imageNum; ?>.png'></td><td valign='top'>
<?php
echo "<p><a href='/cgi-bin/photoFrame/manage.php?frameID=" . $frameID . "&imageNum=" . $imageNum . "&op=setNext'>Set next</a> - Set this to be the next image</p>";
echo "<p><a href='/cgi-bin/photoFrame/manage.php?frameID=" . $frameID . "&imageNum=" . $imageNum . "&op=delete'>Delete</a></p>";
echo "<p><a href='/cgi-bin/photoFrame/makeBin.php?frameID=" . $frameID . "&imageNum=" . $imageNum . "'>Recreate binary</a></p>";

?>
<form action="/cgi-bin/photoFrame/upload.php" method="post" enctype="multipart/form-data">
  <p>Replace with new image:
  <input type="hidden" name="frameID" id="frameID" value="<?php echo $frameID ?>">
  <input type="hidden" name="imageNum" id="imageNum" value="<?php echo $imageNum ?>">
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload" name="submit">
  </p>
</form>

<form action="/cgi-bin/photoFrame/manage.php" method="post" enctype="multipart/form-data">
  <p>Add/change text message:
  <input type="hidden" name="frameID" id="frameID" value="<?php echo $frameID ?>">
  <input type="hidden" name="op" id="op" value="addText">
  <input type="hidden" name="imageNum" id="imageNum" value="<?php echo $imageNum ?>">
  <textarea cols=50 rows=10 name="message" id="message"></textarea>
  <input type="submit" value="View" name="submit">
  </p>
</form>
</td></tr>


</body>
