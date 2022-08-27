<?php

include 'inc.php';
require 'convert.php';

$base_dir = $imagePath;
$target_dir = $base_dir . "/source/";

$imageNum = $_POST['imageNum'];
$inputFile = basename($_FILES["fileToUpload"]["name"]);
$imageFileType = strtolower(pathinfo($inputFile,PATHINFO_EXTENSION));
$target_file = $target_dir . "image" . $imageNum . "." . $imageFileType;
$uploadOk = 1;

$message = $_POST['message'];

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    echo "<p>File is an image - " . $check["mime"] . ".</p>";
    $uploadOk = 1;
  } else {
    echo "<p>File is not an image.</p>";
    $uploadOk = 0;
  }
}

// Check if file already exists
//if (file_exists($target_file)) {
//  echo "Sorry, file already exists.";
//  $uploadOk = 0;
//}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
  echo "<p>The file is too large.</p>";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  echo "<p>Only JPG, JPEG, PNG & GIF files are allowed.</p>";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "<p>The file was not uploaded.</p>";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    echo "<p>The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded to " . $target_file . "</p>";
    echo "<p>Will now convert to " . $width . " x " . $height . "</p>";
    convert($base_dir, $frameID, $target_file, $imageNum, $message, $width, $height);
  } else {
    echo "<p>There was an error uploading your file.</p>";
  }
}

?>

