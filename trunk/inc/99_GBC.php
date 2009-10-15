<?php
    ### DEVELOPER:
    # Include all developer files in DEVELOPER_DIR/DEVELOPER_FOOTER;
    $includeFiles = scandir (DOCUMENT_ROOT. DEVELOPER_DIR . _S . DEVELOPER_FOOTER);
    sort ($includeFiles, SORT_STRING);
    foreach ($includeFiles as $k => $v) {
        if ($v[0] != '.') {
            $f = DOCUMENT_ROOT . DEVELOPER_DIR . _S . DEVELOPER_FOOTER . _S . $v;
            // Require ONCE;
            require_once $f;
        }
    }

    ### Wooooohooooo hoooo :D
    # Unset _SESSION['POST'];
    if (isset ($_SESSION['POST'])) {
        unset ($_SESSION['POST']);
    }

    # Unset _SESSION['FILES'];
    if (isset ($_SESSION['FILES'])) {
    	unset ($_SESSION['FILES']);
    }

    # Ok, cleaning done!
?>
