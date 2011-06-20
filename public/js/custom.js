function slideSwitch() {
	var $active = $('#slideshow DIV.active');

	if ( $active.length == 0 ) $active = $('#slideshow DIV:last');

	// use this to pull the divs in the order they appear in the markup
	var $next =  $active.next().length ? $active.next()
		: $('#slideshow DIV:first');

	// uncomment below to pull the divs randomly
	// var $sibs  = $active.siblings();
	// var rndNum = Math.floor(Math.random() * $sibs.length );
	// var $next  = $( $sibs[ rndNum ] );


	$active.addClass('last-active');

	$next.css({opacity: 0.0})
		.addClass('active')
		.animate({opacity: 1.0}, 1000, function() {
			$active.removeClass('active last-active');
			$active.animate({opacity: 0});
		});
		
}