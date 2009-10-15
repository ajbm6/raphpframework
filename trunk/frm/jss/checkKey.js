function checkKey (evt, str) {
    var e = evt ? evt : event;
    return  e.which === 0   || 
            e.which == 8    || 
            e.keyCode == 13 || 
            !(new RegExp (str)).test (String.fromCharCode (e.which ? e.which : e.keyCode));
}