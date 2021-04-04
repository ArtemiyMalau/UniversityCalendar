<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Магазин "Одежда"</title>


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
		<div class="col-md-12">
			<div style="display: flex;">
				<h3 class="h3 block-title">{{ month|date("F Y", "Europe/Moscow") }}</h3>
				<div class="mr-auto"></div>
				<div>
						<a href="calendar.php?year={{ prev_calendar.year }}&month={{ prev_calendar.month }}">Предыдущий месяц</a>
						<a style="padding-left: 20px" href="calendar.php?year={{ next_calendar.year }}&month={{ next_calendar.month }}">Следующий месяц</a>
				</div>
			</div>
			<section class="calendar__days border-theme bg-color-theme" style="padding: 1.5rem">
				<section class="calendar__top-bar">
					<span class="top-bar__days">Mon</span>
					<span class="top-bar__days">Tue</span>
					<span class="top-bar__days">Wed</span>
					<span class="top-bar__days">Thu</span>
					<span class="top-bar__days">Fri</span>
					<span class="top-bar__days">Sat</span>
					<span class="top-bar__days">Sun</span>
				</section>
				{% for key, event in events %}
					{% if key % 7 == 0 %}
						<section class="calendar__week">
					{% endif %}
						<div class="calendar__day
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
						</section>
					{% endif %}
				{% endfor %}
			</section>
		</div>
	</div>
	<div class="row site-block end-block">
		<a name="add_schedule"></a>
			<div class="col-md-6">
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