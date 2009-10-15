<?php
    // That's it. Easy, ain't it?!
    require_once 'inc/00_CFG.php';
/*
    $objQ = SQL::getQuery (new S ('SELECT * FROM ra_lyrics_items'));

    foreach ($objQ as $k => $v) {
        $objSQL = 'UPDATE ra_lyrics_items SET seo ="' . URL::getURLFromString (new S ($v['title'] . _U .
        $v['artist'] . _U . $v['album'] . _U . $v['date_added'])) . '" WHERE id ="' . $v['id'] . '"';
        mysql_query ($objSQL);
    }

    err (TRUE);*/
    // Ok, one more thing, activate the Frontend;
    $FRT = MOD::activateModule (new FilePath ('mod/frontend'), new B (TRUE));
    $FRT->tieInWithAuthentication (MOD::activateModule (new FilePath ('mod/authentication'), new B (TRUE)));
    $FRT->doTieALLNecessaryRequirementsAndRenderFrontend ();
?>
