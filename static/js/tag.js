if (typeof iTypeID == 'undefined') var iTypeID = 0;
if (typeof iCityID == 'undefined') var iCityID = 0;
var tagauto = $('#sTagName').autocomplete({
    source: function( request, response ) {
    	$.get('/ajax/index/tag/?iCityID=' + iCityID + '&iTypeID=' + iTypeID +'&sKey=' + request.term, function(ret){

    		response(ret.data);
    	}, 'json');
    },
    select: function( event, ui ) {
    	$('#iTagID').val(ui.item.id);
    	$('#sTagName').blur();
    	$('#sTagName').focus();
		$('#sTagName').change();
    },
    response: function( event, ui ) {
    }
}).autocomplete("instance")._renderItem = function( ul, item ) {
	item.label = item.sTagName;
	item.value = item.sTagName;
	item.id = item.iTagID;
	item.data = item;
	return $( "<li>" )
	.append( "<a>" + item.sTagName + "</a>" )
	.appendTo( ul );

};