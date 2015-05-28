if (typeof iTypeID == 'undefined') var iTypeID = 0;
var cityauto = $('#sAuthor').autocomplete({
    source: function (request, response) {
        $.get('/ajax/index/author/?iTypeID=' + iTypeID + '&sKey=' + request.term, function (ret) {

            response(ret.data);
        }, 'json');
    },
    select: function (event, ui) {
        $('#iAuthorID').val(ui.item.id);
        $('#sAuthor').val(ui.item.value);
        $('#sSelectAuthor').val(ui.item.value);
        $('#sAuthor').blur();
        $('#sAuthor').focus();
        $('#sAuthor').change();
    },
    response: function (event, ui) {
    }
}).autocomplete("instance")._renderItem = function (ul, item) {
    console.log(item);
    item.label = item.sAuthorName;
    item.value = item.sAuthorName;
    item.id = item.iAuthorID;
    item.data = item;
    return $("<li>")
        .append("<a>" + item.sAuthorName + " " + item.sCityName + " " + item.sPosition + "</a>")
        .appendTo(ul);

};