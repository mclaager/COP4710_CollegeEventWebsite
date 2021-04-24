function alertThenReload(msg) {
	alert(msg);
	window.location.reload(false);
}

function attemptLeaveRSOAjax(rid)
{
	ajax = $.ajax({
			url: 'includes/my_rsos-includes.php',
			type: 'POST',
			data: {method: "leaveRSOHelper", rid: rid},
			dataType: 'json',
			success: function(data){
				if (data.status === 'ok') {
					alertThenReload("Successfully left RSO.");
				}
				else {
					var errorMessage = data.statusText;
					alertThenReload('Error - ' + errorMessage);
				}
			}
		});
	return;
}
function attemptLeaveRSO(rsoid)
{
	rid = JSON.stringify(rsoid);
	attemptLeaveRSOAjax(rid);
}