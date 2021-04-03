<?php 

require_once __DIR__ . "/includes/functions.php";
require_once __DIR__ . "/CalendarClass.php";

const MONDAY = 1;
const SUNDAY = 7;

$timezone = new DateTimeZone("Europe/Moscow");

if (isset($_GET["year"], $_GET["month"])) {
    try {
        $month_start = new DateTime("01-{$_GET["month"]}-{$_GET["year"]}");
    } catch (Exception $e) {
        // Redirect to current month calendar page in case of corrupted month and year fields
        $url = get_modified_script_url(false);
        header("Location: $url");
    }
} else {
    $month_start = new DateTime("now", $timezone);
    $month = $month_start->format("m");
    $year = $month_start->format("Y");
    $month_start->setDate($year, $month, 1);
    $month_start->setTime(0, 0);
}

$month_end = new DateTime($month_start->format("t-m-Y"));

$month_start_week_day = $month_start->format("N");
$month_end_week_day = $month_end->format("N");

// Prepend remains week days from previous month
if ($month_start_week_day != MONDAY) {
    $remain_days = $month_start_week_day - 1;
    $calendar_start = new DateTime($month_start->format("Y-m-d"));

    $calendar_start->modify("-{$remain_days} day");
} else {
    $calendar_start = $month_start;
}
// Append remains week days from next month
if ($month_end_week_day != SUNDAY) {
    $remain_days = SUNDAY - $month_end_week_day;
    $calendar_end = new DateTime($month_end->format("Y-m-d"));

    $calendar_end->modify("+{$remain_days} day");
} else {
    $calendar_end = $month_end;
}


// Compile all month's calendar data
$calendar_days = $calendar_end->diff($calendar_start)->format("%a") + 1;
// Get days format for each calendar day
$format_datatime_arr = get_format_datetime_array(
    new DateTime($calendar_start->format("d-m-Y")), 
    $calendar_days, 
    "j"
);

$now = new DateTime("now", $timezone);
$schedule_dt = new DateTime("now", $timezone);
$calendar_events = [];
foreach ($format_datatime_arr as $index => $format_datatime) {
    $month_day = [];

    // Check if current day is day of needed month
    if ($index >= $month_start_week_day - 1
        && $index < $calendar_days - (SUNDAY - $month_end_week_day)) {
        $month_day["is_current_month"] = true;
    } else {
        $month_day["is_current_month"] = false;
    }

    $schedule_dt->setTimestamp($format_datatime["timestamp"]);
    if ($now->diff($schedule_dt)->format("%R%a") === "-0") {
        $month_day["today"] = true;
    }

    $month_day["format"] = $format_datatime["format"];
    $month_day["timestamp"] = $format_datatime["timestamp"];

    $calendar_events[] = $month_day;
}

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

foreach ($interval_schedules as $schedule) {
    $schedule_dt = new DateTime("now", $timezone);
    $schedule_dt->setTimestamp($schedule["timestamp"]);

    // Find index in calendar_events by evaluating days difference
    $days_diff = $schedule_dt->diff($calendar_start)->format("%a");

    $calendar_events[$days_diff]["event_counts"] = $schedule["count"];
}

$paginator = get_month_paginator(new DateTime($month_start->format("d-m-Y")));

// Rendering template
echo $twig->render("calendar.tpl", [
    "events" => $calendar_events,
    "next_calendar" => $paginator["next"],
    "prev_calendar" => $paginator["prev"],
    "month" => $month_start
]);