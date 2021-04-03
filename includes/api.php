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

		$resp = $db->query("SELECT sch.date, sub.title AS sub_title, spec.*, 
			DENSE_RANK() OVER (ORDER BY sch.date, sub.title) AS idx 
			FROM schedule sch 
			JOIN subjects sub ON sch.id_subject = sub.id 
			JOIN subjects_to_specialities sub_to_spec ON sub.id = sub_to_spec.id_subject 
			JOIN specialities spec ON sub_to_spec.id_speciality = spec.id 
			WHERE DATE(sch.date) = DATE(FROM_UNIXTIME(?i)) 
			ORDER BY sch.date, idx", $data["date"])->fetchAssocArray();
		
		$schedules = [];
		$idx = null;
		foreach ($resp as $schedule) {
			if ($idx != $schedule["idx"]) {
				$idx = $schedule["idx"];
				$schedules[] = [
					"date" => $schedule["date"], 
					"title" => $schedule["sub_title"], 
					"specialities" => []
				];
			}
			$schedules[count($schedules) - 1]["specialities"][] = [
				"code" => $schedule["code"], 
				"title" => $schedule["title"]
			];
		}


		$api->answer["schedules"] = $schedules;

	case "get_interval_schedules":
		$data = $api->params(["start_date", "end_date"]);

		$events = $db->query("SELECT COUNT(*) AS count, UNIX_TIMESTAMP(schedule.date) AS 'timestamp' 
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

