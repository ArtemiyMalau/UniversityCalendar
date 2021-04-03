<?php

require_once __DIR__ . "/includes/functions.php";

$ch = curl_init();

$query = http_build_query([
	"module" => "get_day_schedules",
	"date" => $_GET["date"]
]);
curl_setopt($ch, CURLOPT_URL, $config["fs"]["api"] . "?{$query}");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$schedules = json_decode(curl_exec($ch), JSON_OPTIONS);

echo $twig->render("day.tpl", [
	"date" => $_GET["date"]
] 
+ $schedules);