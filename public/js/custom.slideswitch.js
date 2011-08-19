var slideshowIntervalID = 0;
slideSwitch = function () {
        if($('#slideshow div').length < 1)
            return;
        else if($('#slideshow div').length >= 1) {
            var $active = $('#slideshow DIV.active');
            if ( $active.length == 0 ) $active = $('#slideshow DIV:last');
            if($('#slideshow div').length > 1) {
            // use this to pull the divs in the order they appear in the markup
            var $next =  $active.next().length ? $active.next() : $('#slideshow DIV:first');
            $active.addClass('last-active');            
            $next.css({opacity: 0.0})
                    .addClass('active')
                    .animate({opacity: 1.0}, 1000, function() {
                            $active.removeClass('active last-active');
                            $active.animate({opacity: 0});
                });
            }
            else {
                $('#slideshow div:first')
                    .css({opacity: 0.0})
                    .addClass('active')
                    animate({opacity: 1});
                clearInterval(slideshowIntervalID);
            }
        }
    }
    jQuery(document).ready(function() {
    //start product slider
    jQuery(function() {
        slideshowIntervalID = setInterval( "slideSwitch()", 3500 );
    });    
    
});

