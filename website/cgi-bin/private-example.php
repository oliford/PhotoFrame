<?php

//The first one built had no ID
if ($frameID == '') {
    $frameID = 'defaultFrame';
}
$dataPath = '/home/user/website/photoFrameHiddenData/' . $frameID . '/';
$imagePath = '/home/user/website/photoFrameImages/' . $frameID . '/';

if ($frameID === 'frame2') {
  //newer one had slightly different dimensions
  $width = "648";
  $height = "480";

} else {
  //original one was a smaller
  $width = "600";
  $height = "448";
}

?>
