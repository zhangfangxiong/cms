$(function(){
	$('#map_search').click(function(){
		var serachContent = $('.seach_text').val();
		var citycode = $(this).attr('citycode');
		var url = 'http://www.fangjiadp.com/' + citycode + '/' + serachContent;
		window.open(url);
	});
});