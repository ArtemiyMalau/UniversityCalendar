<?php

require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/includes/Calendar.php";

$timezone = new DateTimeZone("Europe/Moscow");

if (isset($_GET["year"], $_GET["month"])) {
    try {
        $calendar = new Calendar($_GET["month"], $_GET["year"]);
    } catch (Exception $e) {
        // Redirect to current month calendar page in case of corrupted month and year fields
        $url = get_modified_script_url(false);
        header("Location: $url");
    }
} else {
    $calendar = new Calendar();
}

[$calendar_start, $calendar_end] = $calendar->get_month_boundaries();

// Getting event count for each day of calendar interval
$ch = curl_init();
$query = http_build_query([
    "module" => "get_interval_schedules",
    "start_date" => $calendar_start->getTimestamp(),
    "end_date" => $calendar_end->getTimestamp()
]);
curl_setopt($ch, CURLOPT_URL, $config["fs"]["api"] . "?{$query}");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$interval_schedules = json_decode(curl_exec($ch), JSON_OPTIONS)["interval_schedules"];

$calendar_events = $calendar->get_calendar_page();

foreach ($interval_schedules as $schedule) {
    $schedule_dt = DateTime::createFromFormat("U", $schedule["timestamp"]);

    // Find index in calendar_events by evaluating days difference
    $days_diff = $schedule_dt->diff($calendar_start)->format("%a");

    $calendar_events[$days_diff]["event_counts"] = $schedule["count"];
}

$paginator = get_month_paginator($calendar->get_month());

// Rendering template
echo $twig->render("calendar.tpl", [
    "events" => $calendar_events,
    "next_calendar" => $paginator["next"],
    "prev_calendar" => $paginator["prev"],
    "month" => $calendar->get_month(),
    "subjects" => $db->query("SELECT * FROM subjects")->fetchAssocArray()
]);