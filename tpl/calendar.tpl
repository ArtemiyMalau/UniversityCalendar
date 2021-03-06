<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Календарь экзаменов</title>

	<!-- Bootstrap styles -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

	<!-- Jquery, Bootstrap scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	
	<!-- Datetimepicker styles -->
	<link rel="stylesheet" href="assets/css/glyphs.css" />
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css" />

	<!-- Datetimepicker scripts -->
	<script src="assets/js/moment-with-locales.min.js"></script>
	<script src="assets/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Project styles -->
	<link rel="stylesheet" href="build/css/Style.css">
	<link rel="stylesheet" href="build/css/light.css" id="theme-link">

	<!-- Project scripts -->
	<script src="build/js/calendar.js"></script>
</head>
<body>
<div class="container">

	{{ include("header.tpl") }}

	<div class="row site-block">
		<div class="col-12">
			<div class="calendar_header">
				<h3 class="h3 block-title">{{ month|date("F Y", "Europe/Moscow") }}</h3>
				<div class="mr-auto"></div>
				<div class="row">
					<div class="col-auto">
						<a href="calendar.php?year={{ prev_calendar.year }}&month={{ prev_calendar.month }}">Предыдущий месяц</a>
					</div>
					<div class="col-auto">
						<a href="calendar.php?year={{ next_calendar.year }}&month={{ next_calendar.month }}">Следующий месяц</a>
					</div>
				</div>
			</div>
			<section class="calendar__days border-theme bg-color-theme" style="padding: 1.5rem">
				<div class="row">
					<span class="col d-none d-md-block top-bar__days">Mon</span>
					<span class="col d-none d-md-block top-bar__days">Tue</span>
					<span class="col d-none d-md-block top-bar__days">Wed</span>
					<span class="col d-none d-md-block top-bar__days">Thu</span>
					<span class="col d-none d-md-block top-bar__days">Fri</span>
					<span class="col d-none d-md-block top-bar__days">Sat</span>
					<span class="col d-none d-md-block top-bar__days">Sun</span>
				</div>
				{% for key, event in events %}
					{% if key % 7 == 0 %}
						<div class="row" style="padding-top: 1rem">
					{% endif %}
						<div class="col-6 col-sm-3 col-md calendar__day
						{% if event.today is defined %}
							today
						{% elseif event.event_counts %}
							event
						{% elseif not event.is_current_month %}
							inactive
						{% else %}
							no-event
						{% endif %}
						">
							<span class="calendar__date">{{ event.format }}</span>
							{% if event.event_counts %}
								<a href="day.php?date={{ event.timestamp }}"><span class="calendar__task">{{ event.event_counts }} Экзаменов</span></a>
							{% endif %}
						</div>
					{% if key % 7 == 6 %}
						</div>
					{% endif %}
				{% endfor %}
			</section>
		</div>
	</div>
	<div class="row site-block end-block">
		<a name="add_schedule"></a>
		<div class="col-lg-6 col-12">
			<form id="add_schedule" method="POST" action="includes/api.php">
				<h3 class="h3 block-title">Добавить занятие</h3>
				<input class="form-control" name="module" type="text" value="add_schedule" hidden>
				<label class="col-form-label">Дата и время экзамена</label>
				<input type="text" class="form-control" id="schedule_datetime" name="date" required>
				<label class="col-form-label">Название предмета</label>
				<select class="form-control" name="id_subject" required>
					{% for subject in subjects %}
						<option value="{{ subject.id }}">{{ subject.title }}</option>
					{% endfor %}
				</select>
				<button class="form-control button" style="background-color: #8adc55;" type="submit">Добавить</button>
			</form>
		</div>
	</div>
</body>
</html>