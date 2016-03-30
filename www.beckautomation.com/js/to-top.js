/**
 * This file will cause a <a> element that is pointing to href="#" 
 * with id="to-top" to slide to the top slowly as oppose to the 
 * standard unaethetic result.
 *
 * Copy-paste into HTML file:
 * <a id="to-top" href="#">back to top</a>
 */

$(document).ready(function() {
	$('.to-top').click(function(){
		$('html, body').animate({scrollTop:0}, 'slow');
		return false;
	});
});