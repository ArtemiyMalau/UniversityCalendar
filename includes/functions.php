<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . "/config.php";

use Krugozor\Database\Mysql;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

const JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION;


$db = Mysql::create($config["db"]["host"], $config["db"]["user"], $config["db"]["password"])
	->setDatabaseName($config["db"]["database"]);
if (!$db) {
    echo "Connection false";
    exit();
}

// $fs_loader = new FilesystemLoader(__DIR__ . "/../tpl");
$fs_loader = new FilesystemLoader($config["fs"]["tpl"]);
$twig = new Environment($fs_loader);


function get_format_datetime_array(
	DateTime $start,
    int $counts,
    string $format="Y-m-d", 
    string $date_modifier="+1 day"
) {
    $format_datatime = [];
    for ($i = 0; $i < $counts; $i++) {
		$format_datatime[] = [
			"timestamp" => $start->getTimestamp(),
			"format" => $start->format($format)
		];
        $start->modify($date_modifier);
    }

    return $format_datatime;
}

function get_modified_script_url($add_query=true) {
	$raw_url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	$url_parts = parse_url($raw_url);

	$modified_url = "{$url_parts["scheme"]}://{$url_parts["host"]}/{$url_parts["path"]}";
	if ($add_query) {
		$modified_url .= "?{$url_parts["args"]}";
	}

	return $modified_url;
}

function get_month_paginator(DateTime $month) {
	$paginator = [];

	$month->modify("+1 month");
	$paginator["next"] = [
		"month" => $month->format("m"),
		"year" => $month->format("Y")
	];

	$month->modify("-2 month");
	$paginator["prev"] = [
	    "month" => $month->format("m"),
	    "year" => $month->format("Y")
	];

	return $paginator;
}