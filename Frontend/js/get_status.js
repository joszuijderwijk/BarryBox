

	var params = window.location.pathname.split('/').slice(1);
	var client = params[0];

	if(client == "")
		client = default_user;
		
	startInterval(30, function() {
		$.ajax({
			type: 'POST',
			url: '../../include/get_status.php',
			data: {
				client: client
			},
		dataType: 'json',
		encode:true,
		}).done(function(data){
			
			if (!data){
				displayError("Er was een fout bij het opvragen van de gegevens.");
				disableComponents(true);
			} else{
				// Client not found!
				if (data.error){
					$('#alert').css('display','');
					$('#aliasBadge').text("Onbekend");
				}else{
					$('#aliasBadge').text('@' + data.alias);
				}

				if(data.status == 1){
					$('#statusBadge').addClass('bg-success');
					$('#statusBadge').removeClass('bg-danger');
					$('#statusBadge').text("Online");
					disableComponents(false);
				} else {
					$('#statusBadge').addClass('bg-danger');
					$('#statusBadge').removeClass('bg-success');
					$('#statusBadge').text("Offline");
					disableComponents(true);
				}
			}
			
		}).fail(function(){
			displayError("Er was een fout bij het verbinden naar de server.");
			disableComponents(true);
			console.log('failed');
		});
		
	});
		
function disableComponents(value){
	$('#submit').prop('disabled', value);
	$('.sound-btn').prop('disabled', value);
	$('input').prop('disabled',value);
	$('select').prop('disabled',value);	
}

function displayError(value){
	$('#alert-text').text(value);
	$('#alert').css('display','');
	$('#statusBadge').addClass('bg-secondary');
	$('#statusBadge').removeClass('bg-success');
	$('#statusBadge').removeClass('bg-danger');
	$('#aliasBadge').text("Onbekend");
	$('#statusBadge').text("Onbekend");
}

function startInterval(seconds, callback) {
  callback();
  return setInterval(callback, seconds * 1000);
}
