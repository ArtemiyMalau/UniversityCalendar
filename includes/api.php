<?php

require "./functions.php";

// error_reporting(0);

use DigitalStars\SimpleAPI;
use Krugozor\Database\MySqlException;

session_start();


$api = new SimpleAPI();

switch ($api->module) {
	case "get_day_schedules":
		$data = $api->params(["date"]);

		$dt = new DateTime();
		$midnight = $dt->setTimestamp($data["date"])->setTime(0, 0)->getTimestamp();
		$end_day = $midnight + 86399;

		$schedules = $db->query("SELECT schedule.id, schedule.date, subjects.title FROM schedule
			JOIN subjects ON schedule.id_subject = subjects.id
			WHERE schedule.date BETWEEN ?i AND ?i
			ORDER BY schedule.date", $midnight, $end_day)->fetchAssocArray();

		$api->answer["schedules"] = $schedules;

	case "get_interval_schedule_counts":
		$data = $api->params(["start_date", "end_date"]);

		$sec_per_day = 86400;

		$dt = new DateTime();
		$start_date_stamp = $dt->setTimestamp($data["start_date"])->setTime(0, 0)->getTimestamp();
		$end_date_stamp = $dt->setTimestamp($data["end_date"])->setTime(23, 59, 59)->getTimestamp();

		$events = $db->query("SELECT schedule.id, schedule.date, subjects.title FROM schedule
			JOIN subjects ON schedule.id_subject = subjects.id
			WHERE schedule.date BETWEEN ?i AND ?i
			ORDER BY schedule.date", $start_date_stamp, $end_date_stamp)->fetchAssocArray();

		$cur_date_stamp = $start_date_stamp - $sec_per_day;
		$schedule_counts = [];
		foreach ($events as $event) {
			while ($event["date"] >= $cur_date_stamp + $sec_per_day) {
				$cur_date_stamp += $sec_per_day;
				$schedule_counts[] = ["start_date" => $cur_date_stamp, "end_date" => $cur_date_stamp + $sec_per_day, "count" => 0];
			}
			$schedule_counts[count($schedule_counts)-1]["count"] += 1;
		}

		$api->answer["schedule_counts"] = $schedule_counts;

	case "add_schedule":
		$data = $api->params(["id_subject", "date"]);

		try {
			$db->query("INSERT INTO schedule(id_subject, date) VALUES (?i, ?i)", $data["id_subject"], $data["date"]);

			$api->answer["status"] = true;
		} catch (MySqlException $e) {
			$api->answer["status"] = false;
		}
}