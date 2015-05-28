var iCityID = typeof iCityID == 'undefined' ? 0 : iCityID;
var newsauto = $('#sNewsTitle').autocomplete({
    source: function( request, response ) {
    	$.get('/ajax/index/news/?iCityID=' + iCityID + '&sKey=' + request.term, function(ret){
    		response(ret.data);
    	}, 'json');
    },
    select: function( event, ui ) {
    	$('#iNewsID').val(ui.item.id);
    	$('#sNewsTitle').blur();
    	$('#sNewsTitle').focus();
		$('#sNewsTitle').change();
    },
    response: function( event, ui ) {
    }
}).autocomplete("instance")._renderItem = function( ul, item ) {

	item.label = item.sShortTitle;
	item.value = item.sShortTitle;
	item.id = item.iNewsID;
	item.data = item;
	return $( "<li>" )
	.append( "<a>" + item.sShortTitle + "</a>" )
	.appendTo( ul );

};