<?php
include_once "torProxy.class.php";

$url = "ifconfig.me/ip";
echo $url."\n";
$tor = new torProxy();
$data = $tor->get($url);
echo $data."\n";

sleep(5);
echo "Reset Tor Node...\n";
$tor->resetTor();
echo $url."\n";
$data = $tor->get($url);
echo $data."\n";