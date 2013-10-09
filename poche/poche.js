function poche(id) {
    try {
	var query = "?op=pluginhandler&plugin=poche&method=inthepoche&id=" + param_escape(id);
	
	console.log(query);
		
	new Ajax.Request("backend.php",	{
	    parameters: query,
	    onComplete: function(transport) {
		var ti = JSON.parse(transport.responseText);
		
		var share_url = ti.poche_url + "?action=add&url="+ btoa(ti.link);
		console.log(share_url);
		window.open(share_url,'_blank','menubar=no,height=390,width=600,toolbar=no,scrollbars=yes,status=no,dialog=1');
		
	    } });
    } catch (e) {
	exception_error("poche", e);
    }
}

