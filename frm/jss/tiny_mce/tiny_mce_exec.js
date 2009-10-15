tinyMCE.init ({
    mode: "specific_textareas",
    editor_selector: "RA_mceRichText",
    theme: "advanced",
    gecko_spellcheck: true,
    skin: "o2k7",
    ask: true,
    width: "950",
    height: "200",
    plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager,images",
    theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo",
    theme_advanced_buttons2 : "link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,|,forecolor,backcolor,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,insertlayer,moveforward,movebackward,absolute,|,fullscreen",
    theme_advanced_buttons3 : "styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,blockquote,|,insertfile,insertimage",
    theme_advanced_toolbar_location : "bottom",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    template_external_list_url : "js/template_list.js",
    external_link_list_url : "js/link_list.js",
    external_image_list_url : "js/image_list.js",
    media_external_list_url : "js/media_list.js"
});

tinyMCE.init ({
    mode: "specific_textareas",
    editor_selector: "tinyMCESimple",
    theme: "simple",
    skin: "o2k7",
    valid_elements : "a[href|target=_blank],strong,b,br,p,ul,li,ol,em,i,u"
});