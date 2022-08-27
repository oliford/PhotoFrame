<?php

function convert($imagePath, $frameID, $sourceFile, $imageNum, $message, $width, $height){
    $convertedFile = "image" . $imageNum . ".png";
    $convertedPath = $imagePath . "/converted/" . $convertedFile;
    $palettePath = $imagePath . "/palette.png";

    //$convertCommand = "/usr/bin/convert " . $sourceFile . " -resize " . $width . "x" . $height . " -crop " . $width . "x". $height . "+0+0 -dither FloydSteinberg -remap " . $palettePath;
    $convertCommand = "/usr/bin/convert " . $sourceFile . " -resize " . $width . "x" . $height . " -background Black -gravity center -extent " . $width . "x". $height . "+0+0 -dither FloydSteinberg -remap " . $palettePath;

    if ($message <> ""){
        $convertCommand = $convertCommand . " -gravity NorthWest -weight 700 -pointsize 30 -fill red -stroke black -strokewidth 1 -annotate +10+10 '" . $message . "'";
    }

    $convertCommand = $convertCommand . " " . $convertedPath;

    echo "<p>convert command: " . $convertCommand . "</p>";
    exec($convertCommand, $output, $retVal);

    if ($retVal != 0){
        echo "<p>Image conversion failed. Make sure it's a valid standard image (JPEG, PNG etc). Try resizing/cropping it to 600x448.</p>";
        return;
    }

    //echo "<p>convert returned " . $retVal . ", output:" . $output . "</p>";

    echo "<p>Conversion complete. It will look like the image below. 
          <form action='/cgi-bin/photoFrame/makeBin.php' method='post'>
          <input type='hidden' name='frameID' id='frameID' value=". $frameID . ">
          <input type='hidden' name='imageNum' id='imageNum' value=". $imageNum . ">
          <input type='submit' value='OK, Accept' name='submit'>
          </form></p>
          <p><img src='/photoFrame/" . $frameID . "/converted/" . $convertedFile . "' /></p>";

}
?>

