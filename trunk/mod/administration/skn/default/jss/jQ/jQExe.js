$(document).ready (function () {
    // Upon focus in submits, blur please;
    $('input[type=submit]').focus (function () {
        $(this).blur ();
    });
    
    $('.RA_err_msg_tag').click (function () {
        $(this).fadeOut ('slow');
    });
    
    // We need a JQ Clock, for the administrator interface;
    $('.jQueryClock').jclock();
    
    // Add the AJAX effect ...
    $('<div id="ajaxLoading"></div>')
    .ajaxStart  (function () { $(this).show(); })
    .ajaxStop   (function () { $(this).hide(); })
    .prependTo   ('.RA_ajax_form');
    
    // Add ajaxified forms;
    $('.RA_ajax_form input[type=submit]').click (function (eventObj) {
        var formName = $(this).parents ('form').attr ('name');
        // Save TinyMCE data;
        if (typeof (tinyMCE) != 'undefined') {
            tinyMCE.triggerSave ();
        }
        // Memorize ...
        var queryString = $('[name=' + formName +']').serialize ();
        // Memorize if we have errors;
        var weHaveError = 0;
        // Set what kind of ajax errors we do;
        var beforeEveryInput = 0;
        // Do a POST, send each key/var;
        $.post (window.location + '/Ajax/Do/Type/Ajax-POST', queryString, function (data) {
            // First hide ALL;
            $('.RA_ajax_err').fadeOut ();
            $('.RA_ajax_err_msg_tag').remove ();
            
            // Now, foreach, show ...
            $.each (data, function (i, item) {
                if (i == 'ajax_error_show_before_input') {
                    // Set type of error;
                    beforeEveryInput = item;
                } else {
	                // Set the error;
	                if (beforeEveryInput == 1) {
	                    // For each;
	                    $('[name=' + formName +'] #RA_ajax_err_id_' + i)
                        .text (item)
                        .fadeIn ('slow')
                        .click (function () {
                        // Fade out on CLICK;
                            $(this).fadeOut ('slow');
                        });
	                } else {
		                // Do a prepend of the form; 
		                $('[name=' + formName + ']')
		                .prepend ('<div class="RA_ajax_err_msg_tag RA_err_msg_tag" style="display: none;">' + item + '</div>');
		                
		                // Show the errors;
		                $('.RA_ajax_err_msg_tag')
		                .fadeIn ('slow')
		                .click (function () {
		                    $(this).fadeOut ('slow');
		                });
	                }
	                
	                // Save the error status;
                    weHaveError = 1;
                }
            });
            // Do submit ...
            if (weHaveError != 1) {
                // Ypeeee ...
                $('form[name=' + formName +']').submit ();
            }
        }, 'json');
        // Prevent
        eventObj.preventDefault ();
    });
    
    // We also need defaults for inputs;
    swapValues = [];
    $('.RA_input_swap').each(function (i) {
        swapValues[i] = $(this).val();
        $(this).focus(function(){
            if ($(this).val() == swapValues[i]) {
                $(this).val('');
            }
        }).blur(function(){
            if ($.trim($(this).val()) == '') {
                $(this).val(swapValues[i]);
            }
        });
    });
    
    // We do style our own input fields;
    $('input[type=file]').filestyle ({ 
		image: 'frm/img/choosefile.png',
		imageheight : 32,
		imagewidth : 32,
		width : 650
     });
     
    // And add tooltips to [title] attributes;
    $('[title]').tooltip ({ 
		track: true, 
		delay: 0, 
		showURL: false,
		showBody: " - ",  
		fixPNG: true, 
		opacity: 0.75, 
		left: -120
    });
    
    // Widgets are good;
    $.fn.EasyWidgets ({
        behaviour : {
            dragRevert : 300,
            useCookies : true
        },
		effects : {
			effectDuration : 200,
			widgetShow : 'fade',
			widgetHide : 'fade',
			widgetClose : 'fade',
			widgetExtend : 'fade',
			widgetCollapse : 'fade',
			widgetOpenEdit : 'fade',
			widgetCloseEdit : 'fade',
			widgetCancelEdit : 'fade'
		},
		i18n : {
			editText:        '<img src="mod/administration/skn/default/jss/jQuery/img/edit.png" width="16" height="16" />',
			closeText:       '<img src="mod/administration/skn/default/jss/jQuery/img/close.png" width="16" height="16" />',
			collapseText:    '<img src="mod/administration/skn/default/jss/jQuery/img/collapse.png" width="16" height="16" />',
			cancelEditText:  '<img src="mod/administration/skn/default/jss/jQuery/img/edit.png" width="16" height="16" />',
			extendText:      '<img src="mod/administration/skn/default/jss/jQuery/img/extend.png" width="16" height="16" />'
        }
    });
    
    // Or context menus;
    var administrationMenu = [ 
        {'Edit this item ...': {
            icon: 'mod/administration/skn/default/jss/jQuery/img/edit-16.png',
            onclick: function (menuItem, menu) {
                top.location = $(this).attr ('href'); 
            }
        }},
        $.contextMenu.separator,
        {'Erase ...': {
            icon: 'mod/administration/skn/default/jss/jQuery/img/edit-remove-16.png',
            onclick: function (menuItem, menu) {
                var whatDidIAnswer = confirm ('Are you sure?');
                if (whatDidIAnswer) { 
                    top.location = $(this).nextAll ('a.tableTreeErase').attr ('href');
                }
            }
        }}, 
    ];
    
    $('a.tableTreeName').contextMenu (administrationMenu, {
        theme: 'vista',
        shadow: true,
        shadowColor: 'black',
        showTransition: 'fadeIn',
        hideTransition:'fadeOut',
        useIframe: true
    });
    
    $('a.tableTreeErase').click (function (event) {
        var whatDidIAnswer = confirm ('Are you sure?');
        if (!whatDidIAnswer) {
            return false;
        }
    });
    
    // Set'em A's to _self ...
    $('a').attr ('target', '_self');
    $('form').attr ('target', '_self');
    
    // Make'em fancy, now and then ...
    $('a.raFancyBox').fancybox ({ 

    });
});