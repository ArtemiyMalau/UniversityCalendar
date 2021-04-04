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
	
	<!-- Project styles -->
	<link rel="stylesheet" href="build/css/Style.css">
	<link rel="stylesheet" href="build/css/light.css" id="theme-link">
</head>
<body>
	<div class="container">

	{{ include("header.tpl") }}

	<div class="row site-block">
		<div class="col-md-12">
			<h3 class="h3 block-title">{{ date|date("F d", "Europe/Moscow") }}</h3>
			{% if schedules|length > 0 %}
				<table class="table table-hover">
					<thead>
					<tr>
						<th scope="col">Время</th>
						<th scope="col">Предмет</th>
						<th scope="col">Специальности</th>
					</tr>
					</thead>
					<tbody>
						{% for schedule in schedules %}
							<tr>
								<td>{{ schedule.date|date("H:i", "Europe/Moscow") }}</td>
								<td>{{ schedule.title }}</td>
								<td>
									<table>
										<tbody>
										{% for speciality in schedule.specialities %}
											<tr>
												<td>{{ speciality.code }} - {{ speciality.title }}</td>
											</tr>
										{% endfor %}
										</tbody>
									</table>
								</td>
							</tr>
						{% endfor %}
					<?php
						}
					?>
					</tbody>
				</table>
			{% else %}
				<p>На данный день не запланирован ни один экзамен</p>
			{% endif %}
		</div>
	</div>
</body>
<script src="../includes/core.js"></script>
</html>