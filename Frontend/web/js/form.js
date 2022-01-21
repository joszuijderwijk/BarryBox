$(document).ready(function () {
	
  $(".loader").fadeOut("slow");
	
	$('#message').on('input', function() {
		var istext = ($('#message').val() == '');
		$('#example-tts').prop('disabled', istext); 
	});
	
	$('#example-tts').click( function() {
		var url = 'https://translate.google.com/translate_tts?q=';
		url +=  encodeuricomponent($('#message').val());
		url += '&tl=' + $('#language').val();
		url += '&client=tw-ob';

		// unfortunately google blocks playing this directly
		var a = new Audio(url);
		a.play();
	});
	
  $("form").submit(function (event) {
	  
	// disable button for 5s
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
		$('#example-tts').prop('disabled', false); 
		
        if (data.errors.message) {
          $("#message-group").addClass("has-error");
          $("#message-group").append(
            '<div class="help-block">' + data.errors.message + "</div>"
          );
        }
		
		if (data.errors.language){
		  $("#lang-group").addClass("has-error");
          $("#lang-group").append(
            '<div class="help-block">' + data.errors.language + "</div>"
          );
		}

      } else {
		
        $("#firstrow").append(
          '<div class="alert alert-success alert-dismissible fade show" style="margin-left:5px;width:98%;" role="alert">' + data.result +
		  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
        );
		
		 setTimeout(function() {
			   $('#submit').prop('disabled', false);
			   $('.sound-btn').prop('disabled', false);
		 }, 5000);
		}
    });

    event.preventDefault();
  }),
  $('.sound-btn').click(function () {
	  
		// disable button for 5s
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
		dataType : 'json'
		}).always(function (data) {
			
			 setTimeout(function() {
				   $('#submit').prop('disabled', false);
				   $('.sound-btn').prop('disabled', false);
			 }, 5000);
		 
			//console.log(data);
			
		});
	})
});


