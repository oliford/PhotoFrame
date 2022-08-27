<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $frameID = $_POST['frameID'];
}else{
    $frameID = $_GET['frameID'];
}

//include 'private-example.php';
include 'private-oliford.php';

?>
