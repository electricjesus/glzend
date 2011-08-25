clearTimeouts = function() {
    for (var i = 1; i < 9999; i++)
        window.clearInterval(i);
}
carouselClickEvtSet = function() {
    $('a.carousel-link').click(function() {
        clearTimeouts.call();
        $('#slideshow').html('');
        $('#active').removeAttr('id');
        $('#selected-product').load($(this).attr('href'))
        $(this).children('img').attr('id','active');
        return false;
    });
}
$(document).ready(function() {
    carouselClickEvtSet.call();
    $('a.inspiration-link').click(function() {
        if($(this).attr('id') != 'inspiration-active') {
            clearTimeouts.call();
            $('#slideshow').empty();
            $('#selected-product').empty();
            $('#inspiration-active').removeAttr('id');
            $(this).attr('id','inspiration-active');
            $('div.viewport').load($(this).attr('href')+'/w/carousel', function() {
                $("div.gallery.tinycarousel").tinycarousel({ axis: 'y'});
                carouselClickEvtSet.call();
            });
        }
        
        return false;
    });
});

