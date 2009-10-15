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

############# Motto: 'The (S/C)EO of PHP programming ...';
/**
 * Concrete CLASS providing a mechanism to process the URL as a pair of key/var/key1/var1 elements. Also, allows us to generate URLs
 * based on array of keys and variables that we pass to two specific URL rewriting methods we give;
 *
 * @package RA-URL-Handling
 * @category RA-Concrete-CORE
 * @author Elena Ramona <no_reply@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class URL extends ERR implements IFaceURL {
    /**
     * @staticvar $objGETAssocArray Associative array, containing key/var pairs from GET URL string;
     * @staticvar $objGETURLString The current URL string, saved in a variable;
     * @staticvar $objGETURLSegment A numeric array of all exploded elements in the current GET string ...
     * @staticvar $objGETURLOffset A number, indicating the current URL offset. Can be set by the config file (URL_REWRITE_OFFFSET)
     * @const GET_URL_PREG_SEARCH Constant used for searching segments in the URL ...
     * @const GET_URL_PREG_REPLAC Constant indicating a backreference in the preg PHP functions, used to explode ...
    */
    protected static $objName                   = 'URL :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    private static $objGETAssocArray            = NULL;
    private static $objGETURLString             = NULL;
    private static $objGETURLSegment            = NULL;
    private static $objGETURLOffset             = NULL;
    const GET_URL_PREG_SEARCH                   = '|/*(.+?)/*$|';
    const GET_URL_PREG_REPLAC                   = '\\1';

	# CONSTRUCT;
    public function __construct () {
		// Make a CALL to the parent;
        parent::__construct ();
        // Make it private to avoid inheritance;
        self::explodeURLFromGET ();
    }

    /**
     * Will explode the current page URL, into segments, that we can use further down the execution path. The segments are exploded
	 * based on the: key1/var1/key2/var2 schema. You can further use _GET['key1'] to return the 'var1' as you would do with native PHP,
	 * but allowing you now to have proper SEO friendly URLs by means of string identifiers;
     *
     * @return void Will not return a thing, it just parses
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function explodeURLFromGET () {
        // Initialize self::objContainers to their respective DTs;
        self::$objGETURLOffset   = new I (URL_REWRITE_OFFFSET);
        self::$objGETAssocArray  = new A;
        self::$objGETURLSegment  = new A;

        // GET CURRENT PAGE URL, EXPLODE URL SEGMENTS AND SWITCH SEGMENTS TO GET;
        // GET CURRENT PAGE URL;
        self::$objGETURLString = new S ('http');
        switch ((isset ($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on')) {
            case TRUE:
                // Add a https:// in a secured HTTPS connection;
                self::$objGETURLString->appendString ('s');
                self::$objGETURLString->appendString ('://');
            break;
            default:
                // Add the :// in a normal HTTP connection;
                self::$objGETURLString->appendString ('://');
            break;
        }
        switch ((isset ($_SERVER['SERVER_PORT'])) &&
        ($_SERVER['SERVER_PORT'] != '80')) {
            case TRUE:
                // Add :PORT to our STRING;
                self::$objGETURLString->appendString ($_SERVER['SERVER_NAME'] . ':'
                . $_SERVER['SERVER_PORT']
                . $_SERVER['PHP_SELF']);
                // Else, we need to cut-out just two segments.
                // More checking should be done on subdomains, or other types of configuration;
                self::$objGETURLOffset->doInc ();
            break;
            default:
                // Leave the string as it is;
                self::$objGETURLString->appendString ($_SERVER['SERVER_NAME']
                . $_SERVER['PHP_SELF']);
                // Else, we need to cut-out just two segments.
                // More checking should be done on subdomains, or other types of configuration;
                self::$objGETURLOffset->doInc ();
            break;
        }

        // EXPLODE URL SEGMENTS;
        // Set an index;
        $i = new I (-1);
        foreach (explode (_WS, preg_replace (self::GET_URL_PREG_SEARCH,
        self::GET_URL_PREG_REPLAC, self::$objGETURLString)) as $v) {
            if ($v != _NONE) {
                self::$objGETURLSegment[$i->doInc ()] = new S (trim ($v));
            }
        }

        // SWITCH SEGMENTS TO GET;
        // Parse GETKeyValue pairs;
        self::$objGETAssocArray = self::associativeFromURL (new I (self::countSegments (
        self::getSiteURL ())->toInt () + self::$objGETURLOffset->toInt ()));
        if (self::$objGETAssocArray instanceof A) {
            // Set _GET to a new array, empty previous contents;
            $_GET = new A;
            foreach (self::$objGETAssocArray as $k => $v) {
                // Introduce new key/var pairs, overwriting other if the exists!
                // Broken code upon PORT: $_GET[$k] = $v->entityEncode (ENT_QUOTES);
                $_GET[$k] = new S ((string) $v);

                // !WARNING! ... doing massive cleaning, should keep eye open;
				$_GET[$k] = $_GET[$k]->entityEncode (ENT_QUOTES);
                $_GET[$k] = $_GET[$k]->eregReplace ('[^a-zA-Z0-9 &#;,_:-]', _NONE);
                $_GET[$k] = $_GET[$k]->trimLeft ();
                $_GET[$k] = $_GET[$k]->trimRight ();

                // Stupidy fix ... (no ... REALLY ...) ...
                if ($_GET[$k]->toLength ()->toInt () == 0) {
                    // DEATH of HACKERS FIX;
                    $_GET[$k] = new S ('1');
                }

                // Make STUPID fix for STUPID BROWSERS ... IE8, IE7, IE6 ...
                if ($_GET[$k] == MOD_DIR) {
                    // Redirect them to us;
                    HDR::setHeaderKey (new S ('http://www.kitsoftware.ro'),
                    new S ('Location'));
                }
            }
        }
    }

    /**
     * Will parse a string and make it URL compatible. You would use this method for example to generate a SEO field from a title or
	 * name field that is important to the way you automatically generate the SEO friendly URLs;
     *
     * @param S $objURLString The passed string
     * @return S Will return the string as an URL
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
     */
    public static function getURLFromString (S $objURLString) {
        // Get a CLONE;
        $objURLString = clone $objURLString;
        // Do return ...
        return $objURLString->entityDecode (ENT_QUOTES)->stripTags ()->eregReplace ('[^a-zA-Z0-9 _-]', _NONE)
        ->trimLeft ()->trimRight ()->eregReplace (_SP, '-');
    }

    /**
     * Will add/remove key/var pairs from the current URL string. You can pass it an array of keys and a second parameter consisting of
	 * an array of vars that will be automatically transform in a type of URL we can process with the help of this CLASS.
	 * <code>
	 * <?php
	 *		// How to use it ...
     *		URL::rewriteURL (new A (Array ('Action', ADMIN_ACTION_ID)), new A (Array ('Go', '3'))); # To add ...
     *  	URL::rewriteURL (new A (Array ('Action'))); # To remove ...
     *  	// Same as A, or B but with a third parameter: new S ('example.php'), that will work on that 'example.php' page suffix.
	 * ?>
	 * </code>
     *
     * @param A $getKey The array of keys
     * @param A $getVar The array of variables
     * @param S $getSuffix A suffix, if for example we want to pass to another controller file
	 * @return S The URL string you generated, relative to the current page URL;
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function rewriteURL (A $getKey = NULL, A $getVar = NULL, S $getSuffix = NULL) {
        if ($getKey == NULL) {
            // Return the current URL string;
            return self::getSiteURL (self::associativeToURL (isset ($_GET) ? new A ($_GET->toArray ()) : new A));
        } else {
            // Choose either to append or remove the segment;
            if ($getVar != NULL) {
                // Append to URL;
                $objGETAssocArray = self::associativeFromURL(new I (self::countSegments (
                self::getSiteURL())->toInt () + self::$objGETURLOffset->toInt ()));
                foreach ($getKey as $k => $v) {
                    // Do a for-each loop;
                    $objGETAssocArray[$getKey[$k]] = $getVar[$k];
                }
                // Return the rewritten URL;
                return self::getSiteURL (self::associativeToURL ($objGETAssocArray), $getSuffix);
            } else {
                // Remove from URL;
                $objGETAssocArray = self::associativeFromURL(new I (self::countSegments (
                self::getSiteURL())->toInt () + self::$objGETURLOffset->toInt ()));
                foreach ($getKey as $k => $v) {
                    if (isset ($objGETAssocArray[$v])) {
                        unset ($objGETAssocArray[$v]);
                    }
                }
                // Return the associativeToURL string;
                return self::getSiteURL (self::associativeToURL ($objGETAssocArray));
            }
        }
    }

    /**
     * Will add/remove key/var pairs from the current URL string. The difference between this method and {@link URL::rewriteURL} is that
	 * the rewriteURL is sensitive to the URL it's invoked on, where this method is used to generate static URLs that won't modify
	 * by the page they're invoked on. This helps us keep a decent fixed structure while having the advantage of easy rewritten URLs;
     *
     * @param A $getKey The array of keys
     * @param A $getVar The array of variables
     * @param S $getSuffix A suffix, if for example we want to pass to another controller file
	 * @return S The URL string you generated;
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function staticURL (A $getKey = NULL, A $getVar = NULL, S $getSuffix = NULL) {
        if ($getKey == NULL) {
            // Return the current URL string;
            return self::getSiteURL (self::associativeToURL (isset ($_GET) ? new A ($_GET->toArray ()) : new A));
        } else {
            // Choose either to append or remove the segment;
            if ($getVar != NULL) {
                // Append to URL;
                $objGETAssocArray = new A;
                foreach ($getKey as $k => $v) {
                    // Do a for-each loop;
                    $objGETAssocArray[$getKey[$k]] = $getVar[$k];
                }
                // Return the rewritten URL;
                return self::getSiteURL (self::associativeToURL ($objGETAssocArray), $getSuffix);
            } else {
                // Remove from URL;
                $objGETAssocArray = new A;
                foreach ($getKey as $k => $v) {
                    if (isset ($objGETAssocArray[$v])) {
                        unset ($objGETAssocArray[$v]);
                    }
                }
                // Return the associativeToURL string;
                return self::getSiteURL (self::associativeToURL ($objGETAssocArray));
            }
        }
    }

    /**
     * Will add/remove key/var pairs from the current URL string. Same as the rewriteURL, but with another name, if you're going to use
	 * it in a manner that's going to resemble the process of creating <a href=''> tags. Just a placeholder method.
     *
     * @param A $getKey The array of keys;
     * @param A $getVar The array of variables;
     * @param S $getSuffix A suffix, if for example we want to pass to another controller file;
	 * @return S The same as {@link URL::rewriteURL}
	 * @author Elena Ramona <elena.ramona@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function rewriteLink (A $getKey = NULL,
    A $getVar = NULL, S $getSuffix = NULL) {
        // Do return ...
        return self::rewriteURL ($getKey, $getVar, $getSuffix);
    }

    /**
     * Will parse the URL into an associative array. This method will get the current URL string and parse it in an associative array
	 * that we can then insert in the _GET superglobal to work with. It's an internal private method not used by non-core developers.
     *
     * @param I $segmentNumber The number of the segment to get from;
     * @return A The associative array, parsed from the URL, as key/var pairs;
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function associativeFromURL (I $segmentNumber) {
        $getURLSegments = new A (array_slice (self::$objGETURLSegment
        ->toArray (), ($segmentNumber->toInt () - 1)));
        $i = new I (0);
        $lastURLVar = new S (_NONE);
        $returnedArray = new A;
        foreach ($getURLSegments as $v) {
            if ($i->toInt () % 2) {
                $returnedArray[$lastURLVar->toString ()] = $v;
            } else {
                $returnedArray[$v] = new B (FALSE);
                $lastURLVar->setString ($v);
            }
            // Increment;
            $i->doInc ();
        }
        // Do return ...
        return $returnedArray;
    }

    /**
     * Will make an URL string frmo an associative array. After we took the URL and transformed it into an associative array, we need
	 * to make it back to a string that can be used as a LINK to another resoure. This is what this method does.
     *
     * @param A $passedArray The array ot be imploded as as tring
     * @return S The imploded array, returned as a string now
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function associativeToURL (A $passedArray) {
        $temporaryArray = new A;
        $i = new I (-1);
        foreach ($passedArray as $k => $v) {
            $temporaryArray[$i->doInc ()] = $k;
            $temporaryArray[$i->doInc ()] = $v;
        }
        // After parsing of the array, return the proper URL new S;
        return new S (implode (_WS, $temporaryArray->toArray ()));
    }

    /**
     * Will get the siteURL, concatenated or not with a pageSuffix, which can be NULL. The method will return the current URL, which
	 * can be used to determine the current page the client is on. Either you can use this function from inside a child of the
	 * current one or you can use the {@link URL::rewriteURL} method to retrieve such information.
     *
     * @return S Will return the current website URL string
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getSiteURL (S $websiteURL = NULL, S $pageSuffix = NULL) {
        // Make a switch ...
        switch ($pageSuffix != NULL) {
            case TRUE:
                $objURL = new S (DOCUMENT_HOST . $pageSuffix . _WS . $websiteURL);
            break;

            case FALSE:
                $pageSuffix = new S (str_replace (DOCUMENT_ROOT, _NONE, $_SERVER['SCRIPT_FILENAME']));
                $objURL = new S (DOCUMENT_HOST . $pageSuffix . _WS . $websiteURL);
            break;
        }

        // Do return ...
        return (REWRITE_ENGINE == TRUE && strpos ($_SERVER['REQUEST_URI'],
        _WS . ADMIN_DIR . _WS) === FALSE ? $objURL
        ->doToken ($pageSuffix . _WS, _NONE) : $objURL);
    }

    /**
     * Will clean the URL path, so that ALL key/var pairs get removed. This method is used to clean the URL to the BASE of the URL,
	 * for example when yo want to restrict an URL (like administration) from "saved bookmarks" that won't be the same after the
	 * code changes in time. It's a mantainance method that can be used to redirect back to a base URL;
	 *
	 * @return void Won't return a thing
	 * @author Elena Ramona <no_reply@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function doCleanURLPath () {
        if ($_GET->doCount ()->toInt () != 0) {
            $objFromGET = new A;
            foreach ($_GET as $k => $v) {
                $objFromGET[] = $k;
            }

            # Do the redirect;
            self::setHeaderKey (URL::rewriteURL ($objFromGET), new S ('Location'));
        }
    }

    /**
     * Will count the number of segments in an URL string, returning it as an integer. This method is used to calculate from where
	 * we start processing the URL as key/var pairs. It's an internal method that's used only by core developers.
     *
     * @param S $websiteURL The string URL to count segments for
     * @return I The number of segments in the given URL
	 * @copyright Elena Ramona <no_reply@raphpframework.ro>
     * @version $Id: 09_URL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function countSegments (S $websiteURL) {
        // Execute the count;
        if ($websiteURL != _NONE) {
            $k = new I (0);
            foreach (explode (_WS, preg_replace (self::GET_URL_PREG_SEARCH,
            self::GET_URL_PREG_REPLAC, $websiteURL)) as $v) {
                if ($v != _NONE) {
                    $k->doInc ();
                }
            }
            return $k;
        } else {
            $k = new I (0);
            return $k;
        }
    }
}
?>
