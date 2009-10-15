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

############# Motto: "Adam Savage: 'I reject your reality and substitute my own!' ...";
/**
 * Concrete CLASS providing a 'Smarty' way to separate any project in an MVC (Model/View/Controller) manner, by separating business
 * logic from business presentation.
 *
 * @package RA-Template-And-DOM-Management
 * @category RA-Concrete-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class TPL extends ERR implements IFaceTPL {
    /**
     * @staticvar $objPageTitle Container of an array of strings to be added to the <title></title> tags;
     * @staticvar $objPageTitleBackward Container of a status of how to display the <title></title> tag, forward or backward ...
     * @staticvar $objPageCSS Container of a list of CSS identifiers with proper FilePath to them ...
     * @staticvar $objPageJSS Container of a list of JSS identifiers with proper FilePath to them ...
     * @staticvar $objMetaTAG Container of a list of TAG (meta tags) with proper information about the current page ...
     * @staticvar $objDocumentType Container for the current type of HTML document (or any other type by that matter) ...
     * @staticvar $objContainerHTML Status container if we need to add the <head></head> string to the content.
     * @staticvar $objExecutionCounter Fixes "bug" when same .tp is executed twice with same cache time. It's a 'seed' var ...
    */
    protected static $objName                   = 'TPL :: RA PHP Framework';
	protected $objIdentificationString          = __CLASS__;
    protected static $objRequestIsPHP           = NULL;
    private static $objPageTitle                = NULL;
    private static $objPageTitleBackward        = NULL;
    private static $objPageCSS                  = NULL;
    private static $objPageJSS                  = NULL;
    private static $objMetaTAG                  = NULL;
    private static $objMetaEQV                  = NULL;
    private static $objMetaLNK                  = NULL;
    private static $objDocumentType             = NULL;
    private static $objContainerHTML            = NULL;
    private static $objFilePathArray            = NULL;
    private static $objUserAgentCapability      = NULL;
    private static $objTpEXECounter             = NULL;
    private static $objApacheReqHeaders         = NULL;

	# CONSTRUCT;
    public function __construct () {
    	parent::__construct ();
    	// Register this object with the chain;
    	ChainOfCommand::registerExecutor ($this);

    	// Do the Factory-Singleton method;
		if (!TheFactoryMethodOfSingleton::checkHasInstance (__CLASS__)) {
			// Set specific TPL object properties;
			self::setExeTime (new S (__CLASS__));
			self::$objPageTitle           = new A;
            self::$objPageCSS             = new A;
            self::$objPageJSS             = new A;
            self::$objMetaTAG             = new A;
            self::$objMetaEQV             = new A;
            self::$objMetaLNK             = new A;
            self::$objUserAgentCapability = new A;
            self::$objPageTitleBackward   = new I (0);
            self::$objContainerHTML       = new I (1);
            self::$objTPLLoaded           = new B (TRUE);
            self::$objRequestIsPHP        = new I (1);
            self::$objTpEXECounter        = new I (0);

            // If this is a CSS File, then setupCSSEnvironmentIfCSSFile for it;
            self::setupCSSEnvironmentIfCSSFile ();
            self::setupJSSEnvironmentIfJSSFile ();
            self::activateDeactiveAjax ();

            // Output Gzipped content, if that's the case for it;
            self::setGzippedOutputRequest (new S ('set_header_information'));

            // Set document type, UserAgent stats and more ...
            if (self::$objRequestIsPHP->toInt () == 1) {
				// Set the document type to a default;
	            self::setDocumentType (new S ('xhtml_strict'));

	            // Set an array of 'f_path' (file path);
	            self::$objFilePathArray = new A;
            	self::getUserAgentStats ();
            }
		} else {
			// Return the instantiated object;
			return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
		}
    }

    /**
     * Will disable the output stream, clean the environments, and echo the <head> prepended output ...
     *
     * This method, __destruct, along with the parent __destruct are the engines of the RA PHP Framework, as they clean the
     * information stored in the class objects, output content, do last-minute string replacements, and a bunch of
     * other stuff ... In time, if it grows in "stuff-that-it-must-do", some modifications will be needed to separate things
     * in an ordered fashion ...
     * @todo Add possibility to detect browser gzip/deflate headers, and issue the proper encoding. This is a todo, and will
     * be done in spare/free time. We can already serve GZIP, so it's a choice we make or not, but after we make the choice,
     * checking the user's browser will trully mimic the way PHP does gzipping on it's own.
     *
     * @return void Will not return a thing;
    */
    public function __destruct () {
        if ($this->objIdentificationString == __CLASS__) {
            // CLOSE_SESSION_ON_OBJECT_SCOPE == TRUE, we destroy the SESSION on __destruct;
            if (CLOSE_SESSION_ON_OBJECT_SCOPE == TRUE) {
                // Should be carefull. Closing _SESSION on object scope is horrible;
                self::closeSession ();
                self::disableOutputStream ();
            } else {
                self::disableOutputStream ();
            }

            // Clean CSS Environment if this file is a CSS File;
            self::cleanJSSEnvironmentIfJSSFile ();
            self::cleanCSSEnvironmentIfCSSFile ();

            // Make the OutputBuffer, an A;
            (!self::$objOutputBuffer instanceof A)                      ?
            (self::$objOutputBuffer = new A (self::$objOutputBuffer))   :
            (FALSE);

            // Add the header;
            if (self::getContainerHTMLStatus ()->toInt () == 1 && self::getErrorStatus ()->toInt () != 1) {
                // Output catched buffer, concatenating header;
                self::$objOutputBuffer->arrayUnShift (
                self::getDocumentType ()->toString ()           .
                self::getHTMLHeadContainer ()->toString ()      .
                self::getMetaEQVHeader ()->toString ()          .
                self::getPageTitle ()->toString ()              .
                self::getMetaTAGHeader ()->toString ()          .
                self::getJSSHeader ()->toString ()              .
                self::getCSSHeader ()->toString ()              .
                self::getMetaLNKHeader ()->toString ()          .
                self::getEndHTMLHeadContainer ()->toString ());
            }

            // Add the footer;
            if (self::getContainerHTMLStatus ()->toInt () == 1) {
                // End </body> output;
                self::$objOutputBuffer[count (self::$objOutputBuffer)] = self::getHTMLEnd ()->toString ();
            }

            // Make the Gzipped output;
            self::setGzippedOutputRequest (new S ('output_stored_content'));
        }
    }

    /**
     * Will add a string to the <title></title> array, that will be represented by the getPageTitle method. If you need to have
	 * control over the <title></title> tags of the generated DOM, than you can use this method by passing a string that will get
	 * appended to the title string. You can see {@link TPL::switchtTL ()} on how to reverse the title string;
     *
     * @param S $webPageStringTitle The string to be added to the title;
     * @return M Either the current object instance, or boolean false, depends;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see TPL::switchTTL()
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function manageTTL (S $webPageStringTitle) {
        if (self::$objPageTitle[self::$objPageTitle->doCount ()->toInt ()] = $webPageStringTitle
        ->entityDecode (ENT_QUOTES)->stripTags ()->entityEncode (ENT_QUOTES)) {
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will switch the title representation back or forward, so that SEO can be properly set for a page. If you need to inverse the
	 * way the <title></title> is generated, you can use this method to switch the order of the generated page title string, so
	 * for example the more important keywords in the string are added at the front;
     *
     * @return object The current object instance
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see TPL::manageTTL()
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function switchTTL () {
        switch (self::$objPageTitleBackward->toInt () == 0) {
            case TRUE:
                // Switch TITLE backwards:
                self::$objPageTitleBackward->setInt (1);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            break;

            case FALSE:
                // Switch TITLE back again;
                self::$objPageTitleBackward->setInt (0);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            break;
        }
    }

    /**
     * Will return the current page title, needed when outputing the current rendered page. Upon destruction of the framework TPL object
	 * this method is called to insert the proper <title></title> string in the correct order in the DOM. As such, it's an internal
	 * method used by core developers and it provides an uniform way to access the contained data;
     *
     * @return S Will return the current page title
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see TPL::manageTTL()
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getPageTitle () {
        // Execute the <title tag> .tp file;
        $webHeaderString = new FileContent (FORM_TP_DIR . _S . 'frm_web_header_title.tp');
        return $webHeaderString->doToken ('[%TITLE_REPLACE_STRING%]',
        implode (_DCSP, ((self::$objPageTitleBackward->toInt () == 1) ? (self::$objPageTitle->arrayReverse ()->toArray ()) :
        self::$objPageTitle->toArray ())));
    }

    /**
     * Will add/remove a CSS FilePath from the current <head>. This method provides a uniform way to handle the inclusion of CSS files
	 * through the <link rel='stylesheet'> tag in the header section of our DOM. The file to be included through the FilePath must
	 * previously exist before even trying to include it;
     *
     * @param S $relativeWebCSSFile The relative path to the CSS file
     * @param S $relativeWebCSSFileTag A tag that's used to identify this CSS file
	 * @return mixed Will return either the current object or false;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function manageCSS (FilePath $relativeWebCSSFile, S $relativeWebCSSFileTag = NULL) {
        if ($relativeWebCSSFileTag != NULL) {
            if (!(isset (self::$objPageCSS[$relativeWebCSSFileTag]))) {
                self::$objPageCSS[$relativeWebCSSFileTag] = $relativeWebCSSFile->toRelativePath ()->prependString (DOCUMENT_HOST);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            }
        } else {
            // Code fixed from OLD variant;
            if ($k = array_search ($relativeWebCSSFile->toRelativePath (), self::$objPageCSS->toArray ())) {
                unset (self::$objPageCSS[$k]);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CSS_FILE_PATH_NOT_SET),
                new S (CSS_FILE_PATH_NOT_SET_FIX));
            }
        }
    }

    /**
     * Will return the CSS header, parsing some .tp files for tokens to be replaced. This allow us to have an uniform way of calling
	 * the generated <link rel='stylesheet'> tags in the header section of our generated DOM. This method is an internal method used
	 * by the core developers in the development of this framework;
     *
     * @return S Will return the CSS header string;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see TPL::manageCSS()
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static final function getCSSHeader () {
        $cssHeaderString = new S;
        $cssReplaceString = new S ('[%CSS_HREF%]');
        $cssPureString = new FileContent (FORM_TP_DIR . _S . 'frm_web_header_css.tp');
        foreach (self::$objPageCSS as $k => $v) {
            $cssHeaderString->appendString (str_replace ($cssReplaceString->toString (),
            self::$objPageCSS[$k], $cssPureString->toString ()));
        }
        // Do return ...
        return $cssHeaderString;
    }

    /**
     * Will store key/var options for the <link rel='alternate'> descriptors. This method is used to add the necesary <link
	 * rel='alternate' (rss or XML) tags to the header section of our DOM. We use such a method to have more control over how
	 * the DOM is managed. Use this method for example to add alternate links (like RSS) to your header;
     *
     * @param S $metaTAGName The tag key to be added to the meta tag header
     * @param S $metaTAGContent The content of the meta tag key
     * @param S $metaTAGRelated The rel='' attribute
     * @param S $metaTAGType The type (rss/xml) of the LINK'ed resource
     * @return M The current instance of the object
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function manageLNK (S $metaTAGName, S $metaTAGRelated = NULL,
    S $metaTAGType = NULL, S $metaTAGContent = NULL) {
        if ($metaTAGContent != NULL) {
            // If metaTAGContent contains something, added to the header;
            if (isset (self::$objMetaLNK[$metaTAGName])) {
                self::$objMetaLNK[$metaTAGName]['info']->appendString (_SP . $metaTAGContent);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::$objMetaLNK[$metaTAGName]['name'] = $metaTAGName;
                self::$objMetaLNK[$metaTAGName]['info'] = $metaTAGContent;
                self::$objMetaLNK[$metaTAGName]['type'] = $metaTAGType;
                self::$objMetaLNK[$metaTAGName]['relt'] = $metaTAGRelated;
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            }
        } else {
            // Else, this means it was called without a parameter, remove it;
            if (isset (self::$objMetaLNK[$metaTAGName])) {
                unset (self::$objMetaLNK[$metaTAGName]);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (META_TAG_NOT_SET),
                new S (META_TAG_NOT_SET_FIX));
            }
        }
    }

    /**
     * Will return the meta tag header string, replacing tokens in a .tp file. This method will return the generated string after
	 * parsing the array of <link rel='alternates' to process. This method is used by the core developers to return the generated
	 * header to be prepended to the top of the DOM;
     *
     * @return S Will return the string containing the meta tags for the current page
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getMetaLNKHeader () {
        $tagMetaHeaderString = new S;
        $tagMetaPureString = new FileContent (FORM_TP_DIR . _S . 'frm_web_head_link_rel.tp');
        foreach (self::$objMetaLNK as $k => $v) {
            $tagMetaHeaderString->appendString (str_replace (array ('[%META_NAME_REPLACE%]', '[%META_CONTENT_INFO%]',
            '[%META_TYPE_INFO%]', '[%META_REL_INFO%]'), array (self::$objMetaLNK[$k]['name'], self::$objMetaLNK[$k]['info'],
            self::$objMetaLNK[$k]['type'], self::$objMetaLNK[$k]['relt']), $tagMetaPureString));
        }

        // Do return ...
        return $tagMetaHeaderString;
    }


    /**
     * Will store key/var options for the <meta http-equiv tag> descriptors. This method is used to add <meta http-equivalent tags. You
	 * can use it was you already use the manageTTL/manageCSS methods to add the necesary info to the DOM header.
     *
     * @param S $metaTAGName The tag key to be added to the meta tag header
     * @param S $metaTAGContent The content of the meta tag key
     * @return M The current instance of the object
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function manageEQV (S $metaTAGName, S $metaTAGContent = NULL) {
        if ($metaTAGContent != NULL) {
            // If metaTAGContent contains something, added to the header;
            if (isset (self::$objMetaEQV[$metaTAGName])) {
                self::$objMetaEQV[$metaTAGName]['info']->appendString (_SP . $metaTAGContent);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::$objMetaEQV[$metaTAGName]['name'] = $metaTAGName;
                self::$objMetaEQV[$metaTAGName]['info'] = $metaTAGContent;
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            }
        } else {
            // Else, this means it was called without a parameter, remove it;
            if (isset (self::$objMetaEQV[$metaTAGName])) {
                unset (self::$objMetaEQV[$metaTAGName]);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (META_TAG_NOT_SET),
                new S (META_TAG_NOT_SET_FIX));
            }
        }
    }

    /**
     * Will return the meta equiv header by parsing a .tp file. This method is an internal method used by core developers to return
	 * the DOM header string for meta http-equiv tags that get automatically prepended to the generated DOM string.
     *
     * @return S Will return the string containing the meta tags for the current page;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getMetaEQVHeader () {
        $tagMetaHeaderString = new S;
        $tagMetaPureString = new FileContent (FORM_TP_DIR . _S . 'frm_web_head_meta_eqv.tp');
        foreach (self::$objMetaEQV as $k => $v) {
            $tagMetaHeaderString->appendString (str_replace (array ('[%META_NAME_REPLACE%]', '[%META_CONTENT_INFO%]'),
            array (self::$objMetaEQV[$k]['name'], self::$objMetaEQV[$k]['info']), $tagMetaPureString));
        }
        // Do return ...
        return $tagMetaHeaderString;
    }

    /**
     * Will store key/var options for the <meta tag> descriptors. You can use this method to add meta keywords, meta description,
	 * meta what you can even think of to the header section of our generated DOM. It's a way to have a better control over how
	 * the these tags are managed;
     *
     * @param S $metaTAGName The tag key to be added to the meta tag header
     * @param S $metaTAGContent The content of the meta tag key
     * @return O The current instance of the object
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function manageTAG (S $metaTAGName, S $metaTAGContent = NULL) {
        if ($metaTAGContent != NULL) {
            // If metaTAGContent contains something, added to the header;
            if (isset (self::$objMetaTAG[$metaTAGName])) {
                self::$objMetaTAG[$metaTAGName]['info']->appendString (_SP . $metaTAGContent);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::$objMetaTAG[$metaTAGName]['name'] = $metaTAGName;
                self::$objMetaTAG[$metaTAGName]['info'] = $metaTAGContent;
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            }
        } else {
            // Else, this means it was called without a parameter, remove it;
            if (isset (self::$objMetaTAG[$metaTAGName])) {
                unset (self::$objMetaTAG[$metaTAGName]);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (META_TAG_NOT_SET),
                new S (META_TAG_NOT_SET_FIX));
            }
        }
    }

    /**
     * Will return the meta tag header string, replacing tokens in a .tp file. This method is used by core developers to add the
	 * necessary tags to the header section of our generated DOM.
     *
     * @return S Will return the string containing the meta tags for the current page
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getMetaTAGHeader () {
        $tagMetaHeaderString = new S;
        $tagMetaPureString = new FileContent (FORM_TP_DIR . _S . 'frm_web_head_meta.tp');
        foreach (self::$objMetaTAG as $k => $v) {
            $tagMetaHeaderString->appendString (str_replace (array ('[%META_NAME_REPLACE%]', '[%META_CONTENT_INFO%]'),
            array (self::$objMetaTAG[$k]['name'], self::$objMetaTAG[$k]['info']), $tagMetaPureString));
        }
        // Do return ...
        return $tagMetaHeaderString;
    }

    /**
     * Will manage JSS FilePaths added to the header, having the same calling method as the CSS method. You can use this method to add
	 * <script type='text/javascript' src='...'> - to the header section of our DOM. It's the same use as the {@link TPL::manageCSS}
	 * method that you regurally use to add CSS files to the header;
     *
     * @param S $relativeWebJSSFile Relative path to the included JSS files
     * @param S $relativeWebJSSFileTag The identifier used to tag this JSS file for quick removal
     * @return O Will return the current object instance
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function manageJSS (FilePath $relativeWebJSSFile, S $relativeWebJSSFileTag = NULL) {
        if ($relativeWebJSSFileTag != NULL) {
            if (!(isset (self::$objPageJSS[$relativeWebJSSFileTag]))) {
                self::$objPageJSS[$relativeWebJSSFileTag] = $relativeWebJSSFile->toRelativePath ()->prependString (DOCUMENT_HOST);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            }
        } else {
        	// Fixed OLD code;
            if ($k = array_search ($relativeWebJSSFile->toRelativePath (), self::$objPageJSS->toArray ())) {
                unset (self::$objPageJSS[$k]);
                return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
            } else {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (JSS_FILE_PATH_NOT_SET),
                new S (JSS_FILE_PATH_NOT_SET_FIX));
            }
        }
    }

    /**
     * Will return the JSS header, parsing some .tp files for tokens to be replaced. This method is an internal method used by core
	 * developers to add the specific <script type='text/javascript' src='...'> to the header section of our DOM.
     *
     * @return S Will return the JSS header for the current page
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getJSSHeader () {
        $jssHeaderString = new S;
        $jssReplaceString = new S ('[%SCRIPT_JS_SRC%]');
        $jssPureString = new FileContent (FORM_TP_DIR . _S . 'frm_web_header_js.tp');
        foreach (self::$objPageJSS as $k => $v) {
            $jssHeaderString->appendString (str_replace ($jssReplaceString->toString (),
            self::$objPageJSS[$k], $jssPureString->toString ()));
        }
        // Do return ...
        return $jssHeaderString;
    }

    /**
     * Will return the <html><head><base href=''> container. This method is used by core developers to add the specific container and
	 * <base href=''> tags to the start of the header section of our generated DOM;
     *
     * @return S Will return the header container for the current page
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getHTMLHeadContainer () {
        // Return the <HTML><head> container;
        $containerHTMLHead = new FileContent (FORM_TP_DIR . _S . 'frm_web_html_head_container.tp');
        return $containerHTMLHead->doToken ('[%BASE_HREF_URL%]', DOCUMENT_HOST);
    }

    /**
     * Will return the </head><body> section of our DOM. This method is used by core developers to add the specific end </head><body>
	 * section of the HEAD section of our DOM.
     *
     * @return S Will return the end of the container for the current page
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getEndHTMLHeadContainer () {
        // Return the <HTML><head> container;
        return new FileContent (FORM_TP_DIR . _S . 'frm_web_html_head_body.tp');
    }

    /**
     * Will return the </body></html> end of our DOM. This method is used by core developers to append the </body></html> tags that
	 * end our DOM string before outputting.
     *
     * @return S Will return the end of the HTML document (and footer) for that matter
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getHTMLEnd () {
        // Just return the contents of the frm_web_footer;
        return new FileContent (FORM_TP_DIR . _S . 'frm_web_footer.tp');
    }

    /**
     * Will set a <DOCTYPE header for the current page. You can use this method by specifying an existing <!DOCTYPE in the FRM_DIR
	 * that will change the generated document type that will be outputed to the browser. For example we use the XHTML Strict 1.0 but
	 * you can opt for transitional or any other kind of <!DOCTYPE you want as long as you create the specific file at fist;
     *
     * @param S $documentType The kind of document type (xhtml, html, etc.) to set the page to
     * @return B Will return true if the content of the document DTD was loaded, else will return a false
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function setDocumentType (S $documentType) {
        if (self::$objDocumentType = new FileContent (FORM_TP_DIR . _S . 'frm_web_header_' . $documentType . '.tp')) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the current containerHTML status, meaning if we want to add <head> information or not. Core developers use this
	 * method to determine if they should add the header section or not. This is mainly used when opting to show an error screen by
	 * reasons that an error has happened or for example if they need to output other kinds of data, like binary data, JSON, xML, etc;
	 *
	 * @return I The current status;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getContainerHTMLStatus () {
        // Do return ...
        return self::$objContainerHTML;
    }

    /**
     * Used in AJAX/CSS/JSS files to deactive the HTML <head>. For exampel, if you want to output XML, JSON or any other kind of data
	 * that doesn't need the outputted pre- and post- HTML header and footer strings to the DOM.
     *
     * @return B Will return true in either cases, if it set to HTML container or not. The chances of failre are minimum
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function switchHTML () {
        switch (self::$objContainerHTML->toInt () == 0) {
            case TRUE:
                // Switch showing of HTML container;
                self::$objContainerHTML->setInt (1);
                self::$objHTWriteIt->setInt (1);
                self::$objRequestIsPHP->setInt (1);
                return new B (TRUE);
            break;

            case FALSE:
                // Switch showing of HTML container;
                self::$objContainerHTML->setInt (0);
                self::$objHTWriteIt->setInt (0);
                self::$objRequestIsPHP->setInt (0);
                return new B (TRUE);
            break;
        }
    }

    /**
     * Used to return the current <DOCTYPE. This method is used by core developers to get the chosen <!DOCTYPE in the header section
	 * of our generated DOM. Non-core developers should no have a thing with this method;
     *
     * @return S Will return the current document type
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getDocumentType () {
        // Do return ...
        return self::$objDocumentType;
    }

    /**
     * Will activate/deactive the <head> container if we detect an AJAX call. This method, used by core developers will activate or
	 * deactiovate the <head> container of our DMO in case it detects that the CALL was requested with XMLHttpRequest (AJAX) -
	 * so we can automatically output proper code;
     *
     * @return B Will return true if HTML container has been disabled, due to an AJAX request
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function activateDeactiveAjax () {
        // There WAS a self::switchHTML here, but no more;
        if (isset ($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will initialize a CSS execution environment if the current script contains a .css extension. This method is used by core
	 * developers when they choose to PHPify .css files. We provide such a mechanism (deactivated by default) - that will let you
	 * do "nasty" things in CSS files. On a daily basis you don't need such functionality but we provide it anyway;
     *
     * @return boolean Will return true if the CSS environment has been set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function setupCSSEnvironmentIfCSSFile () {
        if (strpos ($_SERVER['SCRIPT_FILENAME'], '.css')) {
            self::switchHTML ();
            self::setHeaderKey (new S ('text/css'), new S ('Content-type'));
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will destroy the CSS environment, cleaning any set variables to that point. This method is used to CLEAN the CSS environment
	 * of CSS _SESSION keys set using the setCSSKey method.
     *
     * @return B Will return true if CSS environment has been cleaned
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function cleanCSSEnvironmentIfCSSFile () {
        if (strpos ($_SERVER['SCRIPT_FILENAME'], '.css') && isset ($_SESSION['CSS'][$_SERVER['SCRIPT_FILENAME']])) {
            unset ($_SESSION['CSS'][$_SERVER['SCRIPT_FILENAME']]);
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will initialize a JSS execution environment if the current script contains a .js extension. This method is used by the core
	 * developers to initialize the PHPifyed .js files environment;
     *
     * @return B Will return true if the JSS environment has been set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function setupJSSEnvironmentIfJSSFile () {
        if (strpos ($_SERVER['SCRIPT_FILENAME'], '.js'))  {
            self::switchHTML ();
            self::setHeaderKey (new S ('text/javascript'), new S ('Content-type'));
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will destroy the JSS environment, cleaning any set variables to that point. It's the same use as the previous CSS method,
	 * trying to clean keys set by the setJSSKey methods for executing PHPifyed .js files;
     *
     * @return B Will return true if the JSS environemnt has been cleaned
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function cleanJSSEnvironmentIfJSSFile () {
        if (strpos ($_SERVER['SCRIPT_FILENAME'], '.js') && isset ($_SESSION['CSS'][$_SERVER['SCRIPT_FILENAME']]))  {
            unset ($_SESSION['JSS'][$_SERVER['SCRIPT_FILENAME']]);
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set a JSS key to be taken back in the JSS file that was executed. This method is used if you decide to PHPify your JSS
	 * files and to be able to set specific keys from inside the executing PHP environment;
     *
     * @param S $jssSESSIONKey The JSS key to retrieve information by
     * @param S $jssSESSIONVariable The JSS content for the key
     * @param S $jssFile The path to the JSS file where we'll process the key/var variable content
	 * @return void Won't return a thing
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function setJSSKey (S $jssSESSIONKey, M $jssSESSIONVariable, FilePath $jssFile) {
        if ($absoluteJSSFile = new FilePath ($jssFile->toRelativePath (), TRUE)) {
            // Do implicit checking, issuing a new FilePath, with no local taking object;
            $_SESSION['JSS'][$absoluteJSSFile->toString ()][$jssSESSIONKey->toString ()] =
            $jssSESSIONVariable;
        }
    }

    /**
     * Will return a key set by the setJSSKey method, store in the _SESSION variable. After you've used the TPL::setJSSKey method,
	 * inside the PHPifyed .js file.
     *
     * @param S $jssSESSIONKey The key to retrieve the information by
     * @return M Will return false if the key was not found
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function getJSSKey (S $jssSESSIONKey) {
        if (isset ($_SESSION['JSS'][$_SERVER['SCRIPT_FILENAME']][$jssSESSIONKey->toString ()])) {
            return $_SESSION['JSS'][$_SERVER['SCRIPT_FILENAME']][$jssSESSIONKey->toString ()];
        } else {
            self::setHeaderKey (new S ('text/html'), new S ('Content-type'));
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (VIEW_FILE_DIRECTLY_DENIED),
            new S (VIEW_FILE_DIRECTLY_DENIED_FIX));
        }
    }

    /**
     * Will set a CSS key to be taken back in the CSS file that was executed. The use is the same as the setJSSKey method where you
	 * can se a specific key to be used in the PHPifyed .css file;
     *
     * @param S $cssSESSIONKey The key to set information by
     * @param S $cssSESSIONVariable The information to be set for the requested key
     * @param S $cssFile The path to the file for which we set the information
	 * @return void Won't return a thing
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function setCSSKey (S $cssSESSIONKey, M $cssSESSIONVariable, FilePath $cssFile) {
        if ($absoluteCSSFile = new FilePath ($cssFile->toRelativePath (), TRUE)) {
            // Do implicit checking, issuing a new FilePath, with no local taking object;
            $_SESSION['CSS'][$absoluteCSSFile->toString ()][$cssSESSIONKey->toString ()] =
            $cssSESSIONVariable;
        }
    }

    /**
     * Will return a key set by the setCSSKey method, store in the _SESSION variable.  This method can be used to retrieve a specific
	 * key in the PHPifyed .css fiel;
     *
     * @param S $cssSESSIONKey The key with which to get the information by
     * @return B Will return false (or an error page) if tried to be accesed directly, or key not found
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function getCSSKey (S $cssSESSIONKey) {
        if (isset ($_SESSION['CSS'][$_SERVER['SCRIPT_FILENAME']][$cssSESSIONKey->toString ()])) {
            return $_SESSION['CSS'][$_SERVER['SCRIPT_FILENAME']][$cssSESSIONKey->toString ()];
        } else {
            self::setHeaderKey (new S ('text/html'), new S ('Content-type'));
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (VIEW_FILE_DIRECTLY_DENIED),
            new S (VIEW_FILE_DIRECTLY_DENIED_FIX));
        }
    }

    /**
     * Will outout the required sitemap XML, to the calling function or context. It's used for XML processing (as quick as
     * possible) and can be $argumentEd so that it returns different kind of standard XML declarations that we need. For the
     * moment it serves as the sitemap XML, but can be configured for more (and code easily adjusted afterwards);
     *
	 * @param S $objType The kind of SimpleXML object to retrieve from the FRM_DIR;
     * @return SimpleXML Will return the SimpleXML object;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function getSitemapRSSOrXML (S $objType) {
        // Do return ...
        switch ($objType) {
            case 'RSS':
                // Do return ...
                return SIMPLEXML_LOAD_STRING (new FileContent (FORM_TP_DIR .
                _S . 'frm_xml_header.tp'));
                break;

            case 'MAP':
            default:
                // Do return ...
                return SIMPLEXML_LOAD_STRING (new FileContent (FORM_TP_DIR .
                _S . 'frm_ste_map_xml_header.tp'));
                break;
        }
    }

    /**
     * Will output a given ajax json array, discarding output, and exiting right after. This method will output and array as a JSON
	 * resource to a calling Ajax request;
     *
     * @param A $objArrayToJSON The array to output
     * @return S Outputs the json string, using echo
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function outputAjaxJSON (A $objArrayToJSON) {
        // Clean-up around the house ...
        TPL::discardOutputStream (new B (TRUE));
        TPL::switchHTML ();

        // Do an echo with JSON;
        echo json_encode ($objArrayToJSON->toArray ());

        // Unset _SESSION['POST'];
        if (isset ($_SESSION['POST'])) {
            unset ($_SESSION['POST']);
        }

        // Unset _SESSION['FILES'];
        if (isset ($_SESSION['FILES'])) {
            unset ($_SESSION['FILES']);
        }

        // DIE ...
        TPL::disableFurtherOutput ();
    }

    /**
     * Will output a given ajax string, discarding output, and exiting right after. This method is used to output an Ajax sring back
	 * to a calling Ajax request. You can use this method to make the same code output either HTML or either Ajax, depending on your
	 * specific needs;
     *
     * @param S $objStringToOutput The string to output
     * @return S Outputs the string, using echo
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function outputAjaxString (S $objStringToOutput) {
        // Clean-up around the house ...
        TPL::discardOutputStream (new B (TRUE));
        TPL::switchHTML ();

        // Do just an echo ...
        echo $objStringToOutput;

        // Unset _SESSION['POST'];
        if (isset ($_SESSION['POST'])) {
            unset ($_SESSION['POST']);
        }

        // Unset _SESSION['FILES'];
        if (isset ($_SESSION['FILES'])) {
            unset ($_SESSION['FILES']);
        }

        // DIE;
        TPL::disableFurtherOutput ();
    }

    /**
     * Will output a given XML string, discarding output, and exiting right after. This can be used for example to output RSS or
	 * an XML Sitemap (sitemaps.org) or any other kind of XML data you need to throw back from where it came from;
     *
     * @param S $objStringToOutput The string to output
     * @return S Outputs the string, using echo
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function outputXMLString (S $objStringToOutput) {
        // Clean-up around the house ...
        TPL::setHeaderKey (new S ('text/xml'), new S ('Content-Type'));
        TPL::discardOutputStream (new B (TRUE));
        TPL::switchHTML ();

        // Do an XML echo ...
        echo $objStringToOutput;

        // Unset _SESSION['POST'];
        if (isset ($_SESSION['POST'])) {
            unset ($_SESSION['POST']);
        }

        // Unset _SESSION['FILES'];
        if (isset ($_SESSION['FILES'])) {
            unset ($_SESSION['FILES']);
        }

        // DIE;
        TPL::disableFurtherOutput ();
    }

    /**
     * Will clean/start the cache mechanism for a specified .tp file. This method will start a background Output Buffer and catch
	 * the content of the surrounded .tp file, saving it for the specified templateCacheTime. If you want to save it in an invariable
	 * way to the URL where it's called, you can se the tpInvariable to true which will ignore the current URL when caching;
     *
     * @param S $tpFileName The path to the requested .tp file
     * @param I $templateCacheTime For how many seconds should we cache the page for
	 * @param B $tpInvariable Set this to true if you want the caching to be invariable of URL
     * @return S Will return a cache identifier, letting the code execute properly
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function tpIni (FilePath $tpFileName, I $templateCacheTime = NULL, B $tpInvariable = NULL) {
        // Make the "default" parameter;
        if ($templateCacheTime == NULL) {
            // Set it to the default CACHE_TIMEOUT
            $templateCacheTime = new I (CACHE_TIMEOUT);
        }

		// Do a switch, and clean buffers depending on templateCacheTime;
        switch ($templateCacheTime->toInt () != 0) {
            case TRUE:
                // So, we check if there is a cacheFile;
                $cacheFile = self::getCacheFileName ($tpFileName, $templateCacheTime, $tpInvariable);
                if (self::checkCacheFile ($cacheFile, $templateCacheTime)->toBoolean ()) {
                    // And we do the tpDoCache, just to return new B (FALSE) on the if-condition;
                    return self::tpCache ($cacheFile)->toBoolean (); # We return the toBoolean, because of if-condition ...
                } else {
                    // Else we clean them ouput;
                    self::discardOutputStream ();
                    return $cacheFile;
                }
            break;
            case FALSE:
                // So here, we just clean, and return a 'do_not_cache' instruction;
                self::discardOutputStream ();
                return new S ('do_not_cache');
            break;
        }
    }

    /**
     * Will set an object variable in the .php file to be extracted in the .tp file, and used there. This method, similar to the
	 * popular Smarty->assign, is the method that will assign variables to the template file to be executed. You need to specify
	 * the name of the variable inside the execution loop;
     *
     * @param M $tpVar The variable to be set for the current .tp
     * @param S $tpVarString The name of the variable that will be set
     * @param S $tpFileName Path to the .tp file where to set the requested variables
	 * @param S $tpAction What action to take on the variable
	 * @return M Will return a mixed result (either false/true or string, depends)
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function tpSet (M $tpVar, S $tpVarString, FilePath $tpFileName, S $tpAction = NULL) {
		// Set .tp vars;
		if ($tpFileName->checkPathExists ()) {
    		// Set the variable, taking tpAction in consideration;
    		if ($tpAction != NULL) {
    			switch ($tpAction->toString ()) {
    				case 'capitalize':
    					// Make it an S, so we can be sure it's a string;
    					$tpVar = new S ($tpVar);
    					// Capitalize it, with strtoupper ();
    					self::$objFilePathArray[$tpFileName][$tpVarString] = $tpVar->toUpper ();
    				break;
    				case 'explode':
    					if (($tpVar instanceof A)) {
    						foreach ($tpVar as $k => $v) {
    							// Parse every array key as a .tp variable;
    							self::$objFilePathArray[$tpFileName][$k] = $v;
    						}
    					} else {
							self::renderScreenOfDeath (new S (__CLASS__),
							new S (CANNOT_WORK_ON_NON_ARRAY),
							new S (CANNOT_WORK_ON_NON_ARRAY_FIX));
    					}
    				break;
    				#################################################################
    				# Define other actions here!
    			}
    		} else {
    			// Set an unmodified var, as-is;
    			self::$objFilePathArray[$tpFileName][$tpVarString] = $tpVar;
    		}
		} else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (FILE_DOESNT_EXIST),
            new S (FILE_DOESNT_EXIST_FIX));
		}
    }

    /**
     * Will execute the passed .tp FilePath, and extract the variables as references (or overwrites). Assigning the variables is
	 * not enough. You need to make a CALL to TPL::tpExe ($tpF) to execute the specified $tpF file;
     *
     * @param string $tpFileName The path to the .tp file to be executed
     * @param boolean $tpAsAJAXResponse Set to TRUE when echo'ing the .tp as an AJAX response
     * @return boolean Will return true if the file has been executed properly
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function tpExe (FilePath $tpFileName, B $tpAsAJAXResponse = NULL) {
        // Set some requirements ...
        if ($tpAsAJAXResponse == NULL) $tpAsAJAXResponse = new B (FALSE);

        // Make sure we're actually there ...
        if ($tpFileName->checkPathExists ()) {
            self::$objFilePathArray[$tpFileName]['dummy_' . $_SERVER['REQUEST_TIME']] = new S ('do_not_cache');
            if (extract (self::$objFilePathArray[$tpFileName]->toArray (), EXTR_REFS)) {
                // If it's an AJAX request for HTML, CLEAN it;
                if ($tpAsAJAXResponse->toBoolean () == TRUE) {
                    TPL::discardOutputStream (new B (TRUE));
                    TPL::switchHTML ();
                }

                // Require the template file ...
            	require $tpFileName->toString ();

            	// Unset all predefined variables, for a memory clean-up;
            	unset (self::$objFilePathArray[$tpFileName]);

            	// Do me a favor and STOP right here ...
            	if ($tpAsAJAXResponse->toBoolean () == TRUE) {
            	    // DIE;
            	    TPL::disableFurtherOutput ();
            	}

            	// Return the status;
            	return new B (TRUE);
            } else {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CANNOT_EXTRACT_VARS),
                new S (CANNOT_EXTRACT_VARS_FIX));
            }
        } else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (FILE_DOESNT_EXIST),
            new S (FILE_DOESNT_EXIST_FIX));
        }
    }

    /**
     * Will end the output stream started by tpIni, and save the generated the contents, or return a true. This method must be called
	 * after tpExe, before the ending } of the if ($cId = TPL::tpIni ...) { you're executing the code in, as a $tpEnd ($cId) where
	 * the so called $cId is a cache identifier (file name) that's passed between these two methods;
     *
     * @param S $cacheFile The cache file id, or cache identifier to store the content in
     * @param B $getContent Should this method return the content, or store it, default is to store it
     * @return M Will return true if the stream was stored, else will return the content
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function tpEnd (S $cacheFile, B $getContent = NULL) {
        // Based on getContent and cacheFile, determine the type of action we need to take;
		if (($getContent != NULL) && ($cacheFile == 'do_not_cache') && self::discardOutputStream ()) {
            // Get OutputBuffering Content, and discardOutputStream () it quick, while returning;
            return new B (TRUE);
        } else if (($cacheFile != 'do_not_cache')) {
            // writeCacheFile and get the OutputBuffer that was started by tpIni;
            // In case tpCacheTime was 0 in tpIni;
            self::writeCacheFile ($cacheFile);
        }
    }

    /**
     * Will clear the CACHE_DIR, of all cache files. Used for a general refresh of the system cache. This method is provided for
	 * functionality that needs to refresh the whole system cache. For the moment, no even the core developers have found an use
	 * for such a method but we provide it anyway;
     *
     * @return B Will return true if the cache directory has been cleared
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function clearCache () {
        // Do a new FileDirectory, prepended DOCUMENT_ROOT;
    	$cacheFile = new FileDirectory (CACHE_DIR, TRUE);
    	// Clean-up EVERYTHING, except the dots;
    	// Should use DirectoryIterator here, to be implemented soon;
        for ($i = 2, $cacheCount = count ($cacheFile = scandir ($cacheFile)); $i < $cacheCount; ++$i) {
            // Just do skip over .dirs and .files;
            if ($cacheFile[$i][0] != '.') {
                UNLINK ($cacheDirectory . _S . $cacheFile[$i]);
            }
        }

        // Do return ...
        return new B (TRUE);
    }

    /**
     * Will return a FilePath (a string) containing the stored cache file. This method is the core of our caching mechanism,
	 * providing a way to avoid conflicts in cached file names, a way to make the caching mechanism URL variable or not and a way
	 * to cache files only for a specific time;
     *
     * @param S $tpFileName Path to the executed .tp file
     * @param I $templateCacheTime How much time the cache file needs to be stored/compared tp
	 * @return M Will return either a boolean or a string
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function getCacheFileName (FilePath $tpFileName, I $templateCacheTime, B $tpInvariable = NULL) {
		// Make the CACHE sub-directory && set some requirements;
        $objTPLNoChange = $tpInvariable == NULL ? new B (FALSE) : new B (TRUE);

        // Make CACHE directory, based on variable ...
        if ($objTPLNoChange->toBoolean () == FALSE) {
            // Make the cache dir, based on _GET array (page);
		    $objTPLCacheDir = new S ('dir_cache_' .
		    $objSHA1 = sha1 (implode (_U, $_GET->toArray ())));
        } else {
            // Invariate the cache directory ...
            $objTPLCacheDir = new S ('dir_cache_' .
            $objSHA1 = sha1 ($tpFileName . $templateCacheTime));
        }

        // Get the a/b/c/d directory structure
        for ($i = 0; $i < strlen ($objSHA1); ++$i) {
            // Append the DIR ...
            $objTPLCacheDir->prependString ('cache_' . $objSHA1[$i] . _S);
        }

        // Make directories, recursive ...
		if (!is_dir (DOCUMENT_ROOT .
        CACHE_DIR . _S . $objTPLCacheDir)) {
            // Mkdir, recursive ...
			mkdir (DOCUMENT_ROOT . CACHE_DIR .
            _S . $objTPLCacheDir, 0777, TRUE);
		}

        // Make the cache_fname string;
        if ($objTPLNoChange->toBoolean () == FALSE) {
            // Do return variable to _GET array;
            return new FilePath (CACHE_DIR . _S . $objTPLCacheDir . _S . 'cache_' .
            sha1 ($tpFileName . md5 ($templateCacheTime . implode (_U, $_GET->toArray ()) . self::$objTpEXECounter->doInc ())) .
            _U . md5  ($tpFileName . $templateCacheTime . implode (_U, $_GET->toArray ())), FALSE);
        } else {
            // Do return unvariable to _GET array (to page I mean);
            return new FilePath (CACHE_DIR . _S . $objTPLCacheDir . _S . 'cache_' .
            sha1 ($tpFileName . md5 ($templateCacheTime)) . _U .
            md5  ($tpFileName . $templateCacheTime), FALSE);
        }
    }

    /**
     * Will write the output stream contents, trying first to touch the file. This method is used to write the generated content
	 * to the cache file;
     *
     * @param S $cacheFile The path of the cache file to be written
     * @return B Will return true if the cache file has been written properly
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function writeCacheFile (FilePath $cacheFile) {
        // Let me 'touch' you before, just to check permissions;
        if ($cacheFile->touchPath ()) {
            // Just dump the content, use file_put_contents, because it's faster;
            $cacheFile->putToFile (self::getContentFromOutputStream ());
            return new B (TRUE);
        } else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_WRITE_CACHE_FILE),
            new S (CANNOT_WRITE_CACHE_FILE_FIX));
        }
    }

    /**
     * Will check if the file path cache time expired or not, returning a boolean true or false. This method is used by core
	 * developers to check if the need to refresh the cache or not. The cache is refreshed upon a given time, time specified by the
	 * specific project developer according to his needs;
     *
     * @param string $cacheFile Path to the executed .tp file
     * @param integer $templateCacheTime What time was the file cached, so we can calculate the difference
     * @return mixed Will return an integer, or boolean false if it can't determine the cache time
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function checkCacheFile (FilePath $cacheFile, I $templateCacheTime) {
        if ($cacheFile->checkPathExists ()->toBoolean ()) {
            return (($cacheFile->getPathInfo ('mtime')->toInt () + $templateCacheTime->toInt ()) >
            ($_SERVER['REQUEST_TIME']) ? new B (TRUE) : new B (FALSE));
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return a boolean, to indicate if the cache file exists, and was read OK. Core developers use this method to return
	 * the stored file output directly in the output buffer, passing any other intermediary functions between;
     *
     * @param S $cacheFile The path to the cache file
     * @return B Will return true or false, if it could read the cached file
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function tpCache (FilePath $cacheFile) {
        #######################################################################
        # The logic is inversed here, we return new B (TRUE) upon error;
        #######################################################################
        # Cause of the fact that we want the static HTML, we issue a readfile ()
        # instead of an 'include', which would've been much slower;
        return new B (!($cacheFile->checkPathExists () && $cacheFile->readFilePath ()));
        # This should make the content in the if-else block, not execute;
    }

    /**
     * Will return a browser property. This method maps to the EXTERNAL plugin: phpAgentSniffer which will detect a current browser
	 * property. You can check the official documentation of this plugin to understand what a property is;
     *
     * @param S $objProperyKey The key to return the propery for the browser
     * @return M Will return either string or boolean
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getUserAgentProperty (S $objPropertyKey) {
        if (isset (self::$objUserAgentCapability['browser_properties'][$objPropertyKey])) {
            return new S (self::$objUserAgentCapability['browser_properties'][$objPropertyKey]);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return a browser feature. This methods maps to the EXTERNAL plugin: phpAgentSniffer which will detect a current browser
	 * property. You can check the official documentation of this plugin to understand what a featuer is
     *
     * @param S $objFeatureKey The key to check the browser feature for
     * @return M Will return either string or boolean
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getUserAgentFeature (S $objFeatureKey) {
        if (isset (self::$objUserAgentCapability['browser_features'][$objFeatureKey])) {
            return self::$objUserAgentCapability['browser_features'][$objFeatureKey];
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return a browser quirk. This method maps to the EXTERNAL plugin: phpAgentSniffer to return a browser quirck. You can check
	 * the official documentation to find out what a quirk is;
     *
     * @param S $objQuirkKey The key for the browser quirk information to be returned
     * @return M Will return either string or boolean
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getUserAgentQuirk (S $objQuirkKey) {
        if (isset (self::$objUserAgentCapability['browser_quirks'][$objQuirkKey])) {
            return self::$objUserAgentCapability['browser_quirks'][$objQuirkKey];
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return a full-blown array, containing the browser information. This method will return both features, quirks and the
	 * browser properties of the client. You can use this method if you need to retrieve all the stats from it;
     *
     * @return A Will return an array with the user agent properties
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getUserAgentStats () {
        if (count (self::$objUserAgentCapability) == 0) {
            // Set the PHPUserAgentSniffer object;
            $userAgentPHPCapabilityObject = new phpSniff;

            // Get information back from it!
            self::$objUserAgentCapability['browser_properties'] = $userAgentPHPCapabilityObject->property ();
            self::$objUserAgentCapability['browser_features'] = $userAgentPHPCapabilityObject->_feature_set;
            self::$objUserAgentCapability['browser_quirks'] = $userAgentPHPCapabilityObject->_quirks;

            // Do return ...
            return self::$objUserAgentCapability;
        } else {
        	// Do return ...
            return self::$objUserAgentCapability;
        }
    }

    /**
     * Will memorize the browser request headers sent to the server, if PHP is an Apache module. This method is used by the core
	 * developers to determine the headers received from the client so they can automatically detect what the client supports and
	 * act accordingly;
     *
     * @return B Will store the Apache Request Headers (so they can be later checked)
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getApacheRequestHeaders () {
    	if (self::$objApacheReqHeaders == NULL) {
    		// Determine if we're an APACHE module ...
    		if (function_exists ('apache_request_headers')) {
		    	// Get the Apache Client Request HDR ...
		    	self::$objApacheReqHeaders = new A (apache_request_headers ());
		    	return new B (TRUE);
    		} else {
    		    // Do return ...
    			return new B (FALSE);
    		}
    	} else {
    	    // Do return ...
    		return new B (TRUE);
    	}
    }

    /**
	 * Will output the stored output buffer normaly, gzipped or deflated, according to the client 'Accept-Encoding' header at first,
	 * while taking care to remember the general framework settings;
	 *
     * @param S $whatActionToTake An internal action passed to this method, to be taken for Gzipped output
     * @return void Being an internal method, it doesn't need to explicitly return something
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 10_TPL.php 315 2009-10-11 07:11:31Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function setGzippedOutputRequest (S $whatActionToTake) {
    	// Get the browser - APACHE, request headers ...
    	if (self::getApacheRequestHeaders ()->toBoolean () == TRUE) {
	    	// Explode the 'Accept-Encoding' HDR, determine what kind of encodings we have;
	    	$typeOfEncoding = explode (',', self::$objApacheReqHeaders['Accept-Encoding']);
	    	// Chose first one, which should be 'gzip', over deflate ...
	    	$typeOfEncoding = str_replace (_SP, _NONE, $typeOfEncoding[OB_GZIP_TYPE]);
	    	// Switch the action to TAKE
	    	switch ($whatActionToTake) {
	    		case 'set_header_information':
	    			// Fix Linux/Windows bug, when adding the Content-enconding: gzip HDR, works both in Linux/Win;
			    	if ((OB_GZIP == TRUE && OB_GZIP_LEVEL > 0 && OB_GZIP_LEVEL <= 9) && (self::getErrorStatus ()->toInt () != 1) &&
			        (self::getContainerHTMLStatus ()->toInt () == 1) && !(checkIfItsACSSFile () || checkIfItsAJSSFile ())) {
			            // Determine GZIP or DEFLATE;
			            // Use the first one, which should be 'gzip', in theory ...
			            self::setHeaderKey (new S ($typeOfEncoding), new S ('Content-encoding'));
			        }
		        break;
	    		case 'output_stored_content':
			    	// Do just ONE echo ...
		            if (checkIfItsACSSFile() or checkIfItsAJSSFile()) {
		            	// Get the content out as quickly as possible;
		            	echo str_replace (self::$objTokensReplace->toArray (),
		            	self::$objStringReplace->toArray (), implode (_NONE, self::$objOutputBuffer->toArray ()));
		            	// ELSE:
		            } else if (
		            (OB_GZIP == TRUE && OB_GZIP_LEVEL > 0 && OB_GZIP_LEVEL <= 9) &&
		            (self::getErrorStatus ()->toInt () != 1) &&
		            (self::getContainerHTMLStatus ()->toInt () == 1)) {
	                    // Echo as GZIP or DELAFTE;
	                    switch ($typeOfEncoding) {
	                    	case 'deflate':
	                    		// Echo as DEFLATE;
	                    		echo gzcompress (
	                    		str_replace (self::$objTokensReplace->toArray (), self::$objStringReplace->toArray (),
	                    		implode (_NONE, self::$objOutputBuffer->toArray ())), OB_GZIP_LEVEL);
                    		break;
                    		default:
                    			// Echo as GZIPPED;
                    			echo gzencode   (
                    			str_replace (self::$objTokensReplace->toArray (), self::$objStringReplace->toArray (),
                    			implode (_NONE, self::$objOutputBuffer->toArray ())), OB_GZIP_LEVEL, FORCE_GZIP);
                    		break;
	                    }
		            } else {
	                    // Echo, un-encoded string ... RAW output ...
	                    echo str_replace (
	                    self::$objTokensReplace->toArray (), self::$objStringReplace->toArray (),
	                    implode (_NONE, self::$objOutputBuffer->toArray ()));
		            }
	    		break;
	    	}
    	}
    }
}
?>
