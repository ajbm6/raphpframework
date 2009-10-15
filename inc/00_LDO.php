<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
    // FIXES: Here to make some weird PHP errors go away ...
    // -- Set'em TIME to ... somethin';
    $_SERVER['REQUEST_TIME'] = time ();

    // -- Make'em USER_AGENT, to something ...
    if (!isset ($_SERVER['HTTP_USER_AGENT'])) {
		// Make it something, as we do get errors sometimes when no UA is set!
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1;' . _SP .
        'en-GB; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11 GTB5';
    }

    ### Define some functions, that used GLOBALLY, will half-load the framework for .css/.js files;
    // Check to see if it's a CSS File;
    // The two methods, checkIfItsACSSFile and checkIfItsAJSSFile are used to half-load the framework for .css/.js files,
    // meaning that much of the above classes and modules that are loaded in a .php page, aren't loaded for these other
    // types of files. Why: performance wise! Why load the entire framework for some normally static files ... ?!
    function checkIfItsACSSFile () {
    	// Memorize the result, performance wise;
    	static $returnOfCSS = NULL;

    	if ($returnOfCSS == NULL) {
    	    // Do return ...
    		return $returnOfCSS = strpos ($_SERVER['SCRIPT_FILENAME'],
			'.css') !== FALSE;
    	} else {
    	    // Do return ...
    		return $returnOfCSS;
    	}
    }

    // Check to see if it's a JSS File;
    function checkIfItsAJSSFile () {
        // Memorize the result, performance wise;
    	static $returnOfJSS = NULL;

    	if ($returnOfJSS == NULL) {
    	    // Do return ...
            return $returnOfJSS = strpos ($_SERVER['SCRIPT_FILENAME'],
			'.js')  !== FALSE;
    	} else {
    	    // Do return ...
    		return $returnOfJSS;
    	}
    }

	### AUTO-LOADING necessary PHP files/objects; ###
	### LANGUAGE:
    // Load the language files, just before we need them. Thus, we're sure of some definitions;
    $includeFiles = scandir (DOCUMENT_ROOT . LANGUAGE_DIR . _S . LANGUAGE);
    sort ($includeFiles, SORT_STRING);
    foreach ($includeFiles as $k => $v) {
    	if ($v[0] != '.') {
    		$f = DOCUMENT_ROOT . LANGUAGE_DIR . _S . LANGUAGE . _S . $v;
            require_once $f;
    	}
    }

    ### INCLUDES:
    // Include ALL files in INCLUDE_DIR, so we can have some features working;
    $includeFiles = scandir (DOCUMENT_ROOT . INCLUDE_DIR . _S);
    sort ($includeFiles, SORT_STRING);
    $includeFilesCount = count ($includeFiles);
    for ($i = 0; $i < $includeFilesCount - 1; ++$i) {
    	if ($includeFiles[$i][0] != '.') {
    		$f = DOCUMENT_ROOT . INCLUDE_DIR . _S . $includeFiles[$i];
    		require_once $f;
    		// Get the error handler up as soon as possible;
            if (class_exists ('ERR')) {
                // Make the ERR object, NOW!
                $ERR = TheFactoryMethodOfSingleton::getInstance ('ERR');
            }

            // If it's a CSS/JSS file, get out!;
            if (class_exists ('TPL')) {
                if (checkIfItsACSSFile ()) {
                    break;
                }

                if (checkIfItsAJSSFile ()) {
                    break;
                }
            }
    	}
    }

    ### DEVELOPER:
    // Include all developer files in DEVELOPER_DIR/DEVELOPER_HEADER;
    $includeFiles = scandir (DOCUMENT_ROOT. DEVELOPER_DIR . _S . DEVELOPER_HEADER);
    sort ($includeFiles, SORT_STRING);
    foreach ($includeFiles as $k => $v) {
        if ($v[0] != '.') {
            $f = DOCUMENT_ROOT . DEVELOPER_DIR . _S . DEVELOPER_HEADER . _S . $v;
            # Require ONCE;
            require_once $f;
        }
    }

    // We're done loading, we can make it work, now ... Yey!;
    // We can either use these individual objects, to acces specific functions just from them, which should make the code
    // a little bit clearer, or we can use the slower, object delegator that's setup below;
    $URL = TheFactoryMethodOfSingleton::getInstance ('URL');
    $TPL = TheFactoryMethodOfSingleton::getInstance ('TPL');
    $SQL = TheFactoryMethodOfSingleton::getInstance ('SQL');
    $GPH = TheFactoryMethodOfSingleton::getInstance ('GPH');
    $FRM = TheFactoryMethodOfSingleton::getInstance ('FRM');
    $CNF = TheFactoryMethodOfSingleton::getInstance ('CNF');
    $MOD = TheFactoryMethodOfSingleton::getInstance ('MOD');

    ### Make a common interface for ALL, with a delegator;
    // Besides using each object, we can use ALL to map-out all objects in the framework;
    // Because people are lazy, and they would usually want "one object to rule them all!";
    $OBJ = new ObjectMethodDelegator;
    $OBJ->registerObject ($ERR);
    $OBJ->registerObject ($TPL);
    $OBJ->registerObject ($SQL);
    $OBJ->registerObject ($GPH);
    $OBJ->registerObject ($FRM);
    $OBJ->registerObject ($CNF);
    $OBJ->registerObject ($MOD);
?>
