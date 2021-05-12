<?php

ini_set("allow_url_fopen", 1);

$token = $_GET['token'];
$mid = $_GET['mid'];

$url = "http://localhost/ogweb/application/mission/get.php?";

$url = $url."token=".$token;
$url = $url."&mid=".$mid;

$json = file_get_contents($url);
print($json);