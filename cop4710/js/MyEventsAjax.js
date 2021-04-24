function alertThenReload(msg) {
	alert(msg);
	window.location.reload(false);
}

function attemptLeaveEventAjax(eid)
{
	ajax = $.ajax({
			url: 'includes/my_events-includes.php',
			type: 'POST',
			data: {method: "leaveEventHelper", eid: eid},
			dataType: 'json',
			success: function(data){
				if (data.status === 'ok') {
					alertThenReload("Successfully left event.");
				}
				else {
					var errorMessage = data.statusText;
					alertThenReload('Error - ' + errorMessage);
				}
			}
		});
	return;
}

function attemptLeaveEvent(eventid)
{
	eid = JSON.stringify(eventid);
	attemptLeaveEventAjax(eid);
}