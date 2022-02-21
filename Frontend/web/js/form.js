$(document).ready(function () {

	$(".loader").fadeOut("slow");


	$("form").submit(function (event) {

		// disable button for 3s
		$('#submit').prop('disabled', true);
		$('.sound-btn').prop('disabled', true);

		var formData = {
			message: $("#message").val(),
			language: $("#language").val(),
			client: client
		};

		$.ajax({
			type: "POST",
			url: "../../include/process.php",
			data: formData,
			dataType: "json",
			encode: true,
		}).done(function (data) {

			//console.log(data);

			$('#message').val('');
			$(".help-block").remove();
			$(".alert-success").remove();
			$("#message-group").removeClass("has-error");

			// Validation
			if (!data.success) {

				$('#submit').prop('disabled', false);
				$('.sound-btn').prop('disabled', false);

				if (data.errors.message) {
					$("#message-group").addClass("has-error");
					$("#message-group").append(
						'<div class="help-block">' + data.errors.message + "</div>"
					);
				}

				if (data.errors.language) {
					$("#lang-group").addClass("has-error");
					$("#lang-group").append(
						'<div class="help-block">' + data.errors.language + "</div>"
					);
				}

			} else {

				$("#firstrow").append(
					'<div class="alert alert-success alert-dismissible fade show" style="margin-left:10px;width:98%;" role="alert">' + data.result +
					'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
				);

				setTimeout(function () {
					$('#submit').prop('disabled', false);
					$('.sound-btn').prop('disabled', false);
				}, 3000);
			}
		});

		event.preventDefault();
	}),
		$('.sound-btn').click(function () {

			// disable button for 3s
			$('#submit').prop('disabled', true);
			$('.sound-btn').prop('disabled', true);

			var val = $(this).val();
			$.ajax({
				type: 'POST',
				url: '../../include/sound.php',
				data: {
					sound: val,
					client: client
				},
				dataType: 'json'
			}).always(function (data) {

				setTimeout(function () {
					$('#submit').prop('disabled', false);
					$('.sound-btn').prop('disabled', false);
				}, 3000);

				//console.log(data);

			});
		})
});