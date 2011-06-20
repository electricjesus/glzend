/**
* jCarouselFade - Simple addition to Jan Sorgalla's jCarousel
* http://www.kelvinluck.com/assets/jquery/jCarouselFade
*
* Copyright (c) 2006 Kelvin Luck (http://www.kelvinluck.com)
* Dual licensed under the MIT (MIT-LICENSE.txt)
* and GPL (GPL-LICENSE.txt) licenses.
*
* Built on top of the jQuery library
* http://jquery.com
* And jCarousel
* http://sorgalla.com/jcarousel/
*/
$.jCarouselFade = function()
{
// the number of milliseconds to spend fading in:
var fadeInTime = 1000;
// the number of milliseconds to spend fading out:
var fadeOutTime = 1000;
return {
beforeAnimationHandler : function(carousel, first, last, prevFirst, prevLast, size)
{
if (prevFirst == 0) {
// first time this is called, set the opacity of all above the field of view to none.
for (var i=last+1; i<=size; i++) {
carousel.get(i).css({opacity:.01});
}
}
if (first < prevFirst) {
for (var i=first; i<prevFirst; i++) {
carousel.get(i).animate({opacity:1}, fadeInTime);
}
} else {
for (var i=prevFirst; i<first; i++) {
carousel.get(i).animate({opacity:.01}, fadeOutTime);
}
}
if (last < prevLast) {
for (var i=last+1; i<=prevLast; i++) {
carousel.get(i).animate({opacity:.01}, fadeOutTime);
}
} else {
for (var i=prevLast+1; i<=last; i++) {
carousel.get(i).animate({opacity:1}, fadeInTime);
}
}
return;
}
}
}();
$.fn.jCarouselFade = function(configObject)
{
if (configObject.beforeAnimationHandler) {
// preserve existing handler
var oldBeforeAnimationHandler = configObject.beforeAnimationHandler;
configObject.beforeAnimationHandler = function()
{
oldBeforeAnimationHandler();
$.jCarouselFade.beforeAnimationHandler();
};
} else {
configObject.beforeAnimationHandler = $.jCarouselFade.beforeAnimationHandler;
}
return this.each(function() {
var jc = new jQuery.jcarousel(this, configObject);
});
}