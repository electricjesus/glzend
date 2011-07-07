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
        $('div#carouselcontainer').load($(this).attr('href')+'/w/carousel', function() {
            jQuery('#mycarousel').jcarousel({
                                vertical: true,
                                animation: 1000,
                                scroll: 2,
                                visible: 4
                        });
            carouselClickEvtSet.call();
        });
        $('#slideshow').load($(this).attr('href')+'/w/slideshow');
        return false;
    });
});

