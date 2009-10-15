$(document).ready (function () {
    var administrationMenu = [ 
        {'Edit this item ...': {
            icon: 'mod/administration/skn/default/jss/jQuery/img/edit-16.png',
            onclick: function (menuItem, menu) {
                top.location = $(this).attr ('href'); 
            }
        }},
        {'Manage images ...': {
            icon: 'mod/administration/skn/default/jss/jQuery/img/manage-pictures-16.png',
            onclick: function (menuItem, menu) {
                top.location = $(this).attr ('manageImages');
            }
        }},
        {'Manage custom properties ...': {
            icon: 'mod/administration/skn/default/jss/jQuery/img/manage-properties-16.png',
            onclick: function (menuItem, menu) {
                top.location = $(this).attr ('manageProperties');
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
});