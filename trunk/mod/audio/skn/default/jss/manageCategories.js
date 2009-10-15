$(document).ready (function () {
    // Hide;
    $('#dragDropConfirmationDialog').hide ();
    
    // Drag;
    $('a.tableTreeName').draggable ({
        revert: 'invalid'
    });
    // Drop;
    $('a.tableTreeName').droppable ({
        tolerance: 'intersect',
        drop: function (event, droppedThis) {
            var draggedId = $(droppedThis.draggable).attr ('dragDropId');
            var droppedId = $(this).attr ('dragDropId');
            
            $('#dragDropConfirmationDialog').dialog ({
                bgiframe: true,
                title: 'How should we move your category?',
                resizable: false,
                closeOnEscape: false,
                width: 550,
                modal: true,
                overlay: {
                    backgroundColor: '#222222',
                    opacity: 0.5
                },
                buttons: {
                    'As first child of': function () {
                        // Change address and redirect;
                        window.location = window.location + '/Do/Move/Id/' + draggedId + '/To/' + droppedId + '/Type/' + 1;
                    },
                    'As last child of': function () {
                        // Change address and redirect;
                        window.location = window.location + '/Do/Move/Id/' + draggedId + '/To/' + droppedId + '/Type/' + 2;
                    },
                    'As previous brother of': function () {
                        // Change address and redirect;
                        window.location = window.location + '/Do/Move/Id/' + draggedId + '/To/' + droppedId + '/Type/' + 3;
                    },
                    'As next brother of': function () {
                        // Change address and redirect;
                        window.location = window.location + '/Do/Move/Id/' + draggedId + '/To/' + droppedId + '/Type/' + 4;
                    }
                },
                close: function () {
                    // Do a page redirect to itself;
                    window.location = window.location;
                }
            }); 
        }
    });
});