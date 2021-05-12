<?php

ini_set("allow_url_fopen", 1);

$token = $_GET['token'];
$mid = $_GET['mid'];

$url = "http://ec2-54-179-248-71.ap-southeast-1.compute.amazonaws.com/application/mission/get.php?";

$url = $url."token=".$token;
$url = $url."&mid=".$mid;

$json = file_get_contents($url);
print($json);
