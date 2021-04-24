function alertThenReload(msg) {
	alert(msg);
	window.location.reload(false);
}

function attemptJoinRSOAjax(rid)
{
	ajax = $.ajax({
			url: 'includes/rsos-includes.php',
			type: 'POST',
			data: {method: "joinRSOHelper", rid: rid},
			dataType: 'json',
			success: function(data){
				if (data.status === 'ok') {
					alertThenReload("Successfully joined RSO.");
				}
				else {
					var errorMessage = data.statusText;
					alertThenReload('Error - ' + errorMessage);
				}
			}
		});
	return;
}
function attemptJoinRSO(rsoid)
{
	rid = JSON.stringify(rsoid);
	attemptJoinRSOAjax(rid);
}