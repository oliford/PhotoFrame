<html>
<head>
<title>Photoframe setup</title>
</head>
<body>
<?php
include 'inc.php';

$currentImage = trim(file_get_contents($dataPath . '/currentImage.txt'));
$nextImage = trim(file_get_contents($dataPath . '/nextImage.txt'));
$nImages = trim(file_get_contents($dataPath . '/nImages.txt'));
$pollPeriod = trim(file_get_contents($dataPath . '/pollPeriod.txt'));
$lastUpdate = trim(file_get_contents($dataPath . '/lastUpdate.txt'));


if ($_POST['op'] == "set"){
    $pollPeriod = $_POST['pollPeriod'];
    file_put_contents($dataPath . "/pollPeriod.txt", $pollPeriod);

    $nextImage = $_POST['nextImage'];
    file_put_contents($dataPath . "/nextImage.txt", $nextImage);

    $nImages = $_POST['nImages'];
    file_put_contents($dataPath . "/nImages.txt", $nImages);
}

?>

<h1>Photo Frame Setup for <?php echo $frameID ?></h1>
<form method='post' action='setup.php'>
<input type='hidden' name='frameID' value='<?php echo $frameID ?>'>
<input type='hidden' name='op' value='set'>

<p>Device poll period: <input type='text' name='pollPeriod' value='<?php echo $pollPeriod ?>'> s.</p>
<p>Last poll: <?php echo strftime("%d.%m.%y %H:%M:%S", $lastUpdate) ?></p>
<p>Expected poll: <?php echo strftime("%d.%m.%y %H:%M:%S", ($lastUpdate+$pollPeriod)) ?></p>
<p>Current image: <?php echo $currentImage ?></p>
<p>Next image: <input type='text' name='nextImage' value='<?php echo $nextImage ?>'></p>
<p>Total images: <input type='text' name='nImages' value='<?php echo $nImages ?>'></p>

    <input type="submit" value="Set config">
</form>

<form action="/cgi-bin/photoFrame/upload.php" method="post" enctype="multipart/form-data">
  <p><b>Upload new image:</b>
  <input type="hidden" name="frameID" id="frameID" value="<?php echo $frameID ?>">
  <input type="hidden" name="imageNum" id="imageNum" value="<?php echo $nImages ?>">
  <input type="hidden" name="message" id="message" value="">
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload" name="submit">
  </p>
</form>

<?php 
        echo "<p>Existing images:<br>";
	for ($i=0; $i < $nImages; $i++){
		echo $i . ": <a href='manage.php?frameID=" . $frameID . "&imageNum=" . $i . "'><img height=100 src='/photoFrame/" . $frameID . "/converted/image" . $i . ".png'></a>";
	}
        echo "</p>";
?>


</bopy>
