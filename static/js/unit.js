if (typeof sCityCode == 'undefined') var sCityCode = 0;
var untiauto = $('#sLoupanName').autocomplete({
    source: function( request, response ) {
    	$.get('/ajax/unit/?sCityCode=' + sCityCode+'&sKey=' + request.term, function(ret){
    		response(ret.data);
    	}, 'json');
    },
    select: function( event, ui ) {
    	$('#iLoupanID').val(ui.item.id);
    	$('#sLoupanName').blur();
    	$('#sLoupanName').focus();
		$('#sLoupanName').change();
    },
    response: function( event, ui ) {
    }
}).autocomplete("instance")._renderItem = function( ul, item ) {
	var line = item.label.split('|');
	if (line.length < 4) {
		item.label = '';
    	item.value = '';
    	item.id = '';
    	return $( "<li>" )
        .append( "<span style='color: #818181;'>未找到该小区,请确认小区名称是否正确</span>" )
        .appendTo( ul );
	} else {
    	item.label = line[1] + " <span style='color: #818181;'>(" + line[4] + ")</span>";
    	item.value = line[1];
    	item.id = line[0];
    	item.data = line;
    	return $( "<li>" )
        .append( "<a>" + line[1] + " <span style='color: #818181;'>(" + line[4] + ")</span></a>" )
        .appendTo( ul );
	}
};