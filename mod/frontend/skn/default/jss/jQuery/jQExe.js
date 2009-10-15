$(document).ready (function () {
	// Clear the target _self for inputs ...
	$('form').attr ('target', '_self');
	
    // Upon focus in submits, blur please;
    $('input[type=submit]').focus (function () {
        $(this).blur ();
    });

    // We do style our own input fields;
    $('input[type=file]').filestyle ({ 
		image: 'frm/img/choosefile.png',
		imageheight : 32,
		imagewidth : 32,
		width : 250
     });
    
    // Make'em fancy, now and then ...
    $('a.raFancyBox').fancybox ({ 

    });
    
    // Hide'em description and lyrics ...    
    $('.h1_AudioLyrics').click (function () {
    	$(this).next ().slideToggle ();
    });
    
    $('.h1_AudioDescription').click (function () {
    	$(this).next ().slideToggle ();
    });
});
