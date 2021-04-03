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

		$schedules = $db->query("SELECT sch.id, sch.date, sub.title, spec.* 
			FROM schedule sch
			JOIN subjects sub ON sch.id_subject = sub.id
			JOIN subjects_to_specialities sub_to_spec ON sub.id = sub_to_spec.id_subject
			JOIN specialities spec ON sub_to_spec.id_speciality = spec.id
			WHERE DATE(sch.date) = DATE(FROM_UNIXTIME(?i))
			ORDER BY sch.date", $data["date"])->fetchAssocArray();
		
		$api->answer["schedules"] = $schedules;

	case "get_interval_schedules":
		$data = $api->params(["start_date", "end_date"]);

		$events = $db->query("SELECT COUNT(*) as count, UNIX_TIMESTAMP(schedule.date) as 'timestamp' 
			FROM schedule
			WHERE DATE(schedule.date) BETWEEN DATE(FROM_UNIXTIME(?i)) AND DATE(FROM_UNIXTIME(?i))
			GROUP BY DATE(schedule.date)
			ORDER BY schedule.date", $data["start_date"], $data["end_date"])->fetchAssocArray();

		$api->answer["interval_schedules"] = $events;

	case "add_schedule":
		$data = $api->params(["id_subject", "date"]);

		try {
			$db->query("INSERT INTO schedule(id_subject, date) VALUES (?i, FROM_UNIXTIME(?i))", $data["id_subject"], $data["date"]);

			$api->answer["status"] = true;
		} catch (MySqlException $e) {
			$api->answer["status"] = false;
		}
}

