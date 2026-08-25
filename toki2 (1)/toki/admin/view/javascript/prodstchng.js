var prodstchng = {	
	'geturlparam': function(name) {
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	    return results[1] || 0;
	},
	'save': function(thisvar, id, status) {
		user_token = prodstchng.geturlparam('user_token');
		if(user_token) { 
			$.ajax({
				url: 'index.php?route=catalog/product/prodstchngsave&user_token='+user_token,
				type: 'post',
				data : { id : id, status : status },
				dataType: 'json',
			});
		}
	},
	'initjson': function() {
		$('.chkstatus').each(function() {
			$(this).click(function() {
				var prodid = parseInt($(this).attr('data-str'));
				if($(this).is(':checked')) {
 					prodstchng.save($(this), prodid, 1);
				} else {
 					prodstchng.save($(this), prodid, 0);
				}
			});	
		});
	}
}
$(document).ready(function() {
	prodstchng.initjson();
});