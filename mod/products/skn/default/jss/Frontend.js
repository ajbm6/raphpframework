$(document).ready (function () {
	// Make the gallery ...
	$('.a_ProductsImgGroup').fancybox ({
		
	});
	
	// ScrollTo ...
	$('#a_ProductTopImage').click (function (event) {
		$.scrollTo ('#div_ProductItemImages', 800);
		return false;
	})
});