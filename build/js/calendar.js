window.onload = function() {
	function objectifyForm(formArray) {
	    //serialize data function
	    var returnArray = {};
	    for (var i = 0; i < formArray.length; i++){
	        returnArray[formArray[i]['name']] = formArray[i]['value'];
	    }
	    return returnArray;
	}

	$('#schedule_datetime').datetimepicker({
		locale: 'ru',
	});

	$("#add_schedule").on("submit", function(event) {
		event.preventDefault();

		let fields = objectifyForm(this);

		var date = $('#schedule_datetime').data("DateTimePicker").date();
		fields["date"] = date.unix();

		console.log(fields);

		$.ajax({
			url: this.action,
			method: this.method,
			data: fields,
			success: function (response) {
				if (response["status"]) {
					alert("Запись добавлена");
					location.reload();
				} else {
					alert("Не удалось добавить запись");
				}
			}
		});
	});
}