function alertThenReload(msg) {
	alert(msg);
	window.location.reload(false);
}

function attemptJoinEventAjax(eid)
{
	ajax = $.ajax({
			url: 'includes/events-includes.php',
			type: 'POST',
			data: {method: "joinEventHelper", eid: eid},
			dataType: 'json',
			success: function(data){
				if (data.status === 'ok') {
					alertThenReload("Successfully joined event.");
				}
				else {
					var errorMessage = data.statusText;
					alertThenReload('Error - ' + errorMessage);
				}
			}
		});
	return;
}
function attemptJoinEvent(eventid)
{
	eid = JSON.stringify(eventid);
	attemptJoinEventAjax(eid);
}