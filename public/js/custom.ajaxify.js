/**/
$(document).ready(function() {    
    $('a.ajaxify').click(function() {
        $('#slideshow').remove();
        $('#active').removeAttr('id');
        $('#selected-product').load($(this).attr('href'))
        $(this).children('img').attr('id','active');
        return false;
    });
    $('a.ajaxify2').click(function() {  
        if(!$('#slideshow')) {
            $('div.product_content').append('<div id="slideshow"></div>');
        }
        $('div#carouselcontainer').load($(this).attr('href')+'/w/carousel', function() {
            jQuery('#mycarousel').jcarousel({
				vertical: true,
				animation: 1000,
				scroll: 2,
				visible: 4
			});
            $('a.ajaxify').click(function() {
                $('#slideshow').remove();
                $('#active').removeAttr('id');
                $('#selected-product').load($(this).attr('href'))
                $(this).children('img').attr('id','active');
                return false;
            });
        });        
        $('#slideshow').load($(this).attr('href')+'/w/slideshow');
        return false;
    });
});
