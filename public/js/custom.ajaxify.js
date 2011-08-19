carouselClickEvtSet = function() {
    $('a.carousel-link').click(function() {
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
        clearInterval(slideshowIntervalID);
        slideshowIntervalID = setInterval( "slideSwitch()", 3500 );
        $('#inspiration-active').removeAttr('id');
        $(this).attr('id','inspiration-active');
        $('div.viewport').load($(this).attr('href')+'/w/carousel', function() {
            $("div.gallery.tinycarousel").tinycarousel({ axis: 'y'});
            carouselClickEvtSet.call();
        });
        $('#slideshow').load($(this).attr('href')+'/w/slideshow');
        return false;
    });
});

