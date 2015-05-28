/*
*
* 文本框自动提示
*/
var autocomplete = function(option){
    if (typeof sCityCode == 'undefined') var sCityCode = 0;
    var untiauto = $('#'+option.name).autocomplete({
        source: function( request, response ) {
            $.get(option.url+'&sCityCode=' + sCityCode+'&sKey=' + request.term, function(ret){
                response(ret.data);
            }, 'json');
        },
        select: function( event, ui ) {
            if ($('#'+option.id)) {
                $('#'+option.id).val(ui.item.id);
            }
            $('#'+option.name).blur();
            $('#'+option.name).focus();
        },
        response: function( event, ui ) {
        }
    }).autocomplete("instance")._renderItem = function( ul, item ) {
        item.label = item.sName;
        item.value = item.sName;
        item.id = item.id;
        item.data = item;
        return $("<li>")
            .append("<a>" + item.sName  + "</a>")
            .appendTo(ul);
    };
}
