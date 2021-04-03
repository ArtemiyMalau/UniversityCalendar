<?php

const MONDAY = 1;
const SUNDAY = 7;

class Calendar {
	private $calendar_start = null;

	private $calendar_end = null;

	private $cur_month = null;

	private $timezone = null;

	private $calendar_days = 0;
	private $month_start_week_day = -1;
	private $month_end_week_day = -1;


	public function __construct($month="", $year="", $timezone=null) {
		$this->timezone = $timezone ? $timezone : new DateTimeZone("Europe/Moscow"); 
		if ($month && $year) {
			$this->cur_month = new Datetime("01-{$month}-{$year}", $this->timezone);
		} else {
			$this->cur_month = new DateTime("now", $this->timezone);

			// Setting first day of month
			$day_offset = $this->cur_month->format("j") - 1;
			$this->cur_month->modify("-{$day_offset} day");

		    $this->cur_month->setTime(0, 0);
		}

		$this->init_calendar_boundaries();
	}

	private function init_calendar_boundaries() {
		$month_end = new DateTime($this->cur_month->format("t-m-Y"));

		$this->month_start_week_day = $this->cur_month->format("N");
		$this->month_end_week_day = $month_end->format("N");

		// Prepend remains week days from previous month
	    $this->calendar_start = new DateTime($this->cur_month->format("Y-m-d"));
		if ($this->month_start_week_day != MONDAY) {

		    $remain_days = $this->month_start_week_day - 1;
		    $this->calendar_start->modify("-{$remain_days} day");
		}

		// Append remains week days from next month
	    $this->calendar_end = new DateTime($month_end->format("Y-m-d"));
		if ($this->month_end_week_day != SUNDAY) {

		    $remain_days = SUNDAY - $this->month_end_week_day;
		    $this->calendar_end->modify("+{$remain_days} day");
		}

		$this->calendar_days = $this->calendar_end->diff($this->calendar_start)->format("%a") + 1;
	}

	public function get_month() {
		return new Datetime($this->cur_month->format("Y-m-d"));
	}

	public function get_calendar_days() {
		return $this->calendar_days;
	}

	public function get_month_boundaries() {
		return [
			new Datetime($this->calendar_start->format("Y-m-d")),
			new Datetime($this->calendar_end->format("Y-m-d"))
		];
	}

	public function is_month_day($day_index) {
		if ($day_index >= $this->month_start_week_day - 1
	        && $day_index < $this->calendar_days - (SUNDAY - $this->month_end_week_day)) {
	        return true;
	    } else {
	        return false;
	    }
	}
}