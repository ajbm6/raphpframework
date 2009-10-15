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

############# Motto: "Code is poetry!";
/**
 * Concrete CLASS providing the basic error-handling mechanis, the initialization using the parent abstract CLASSes, the control over
 * the whole environment, the writing of the .htaccess file upon first execution and when content changes, output buffering and more;
 *
 * @package RA-Error-Handling
 * @category RA-Concrete-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class ERR extends HTA {
    /**
     * @staticvar $objErrorHappened Memorize the status of whether an error occured or not ...
     * @staticvar $objTPLLoaded Did the TPL object loaded, or did an error happened to this moment?!
    */
    protected static $objName                 	= 'ERR :: RA PHP Framework';
    protected $objIdentificationString 	        = __CLASS__;
    private static $objErrorHappened            = NULL;
    protected static $objTPLLoaded              = NULL;

    /**
     * Sets anything that's needed to make the RA PHP Framework work as expected. Usually we don't document __constructors as the name
	 * of the method says it all, but in this case we need to document that the 'ERR' __constructor is called to initialize the RA
	 * PHP Framework as it's the first non-abstract inherited CLASS. For this purpose only, it will set the default configuration
	 * options, the Ouput Buffering mechanis, the Error Handling mechanism and other properties;
     *
     * @return B Returns true if the object has been constructed properly
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
    */
    public function __construct () {
        if (!TheFactoryMethodOfSingleton::checkHasInstance (__CLASS__)) {
			self::$objErrorHappened  = new I (0);
			self::$objHT             = new A;
			self::$objExecutionTime  = new A;
			self::$objTokensReplace  = new A;
			self::$objStringReplace  = new A;
			self::$objOutputBuffer   = new A;
			self::$objOutputBufferCount = new I (0);
            if (self::setRAPHPFrameworkINIObjectProps ()->toBoolean ()) {
                self::setExeTime (new S (__CLASS__));
                self::setOutputStreamCallbackWorker (new S (__CLASS__ . _DC . 'executionStreamedOutput'));
                self::setOutputErrorCatcherCallback (new S (__CLASS__ . _DC . 'executionCatchPHPErrors'));
                self::executeOutputStream ();
                self::setCacheHeaderKeys ();
                self::setHTAutoPHP ();
                // If the LOAD is ok, go on!
                if (self::getRAPHPFrameworkAverageLoading()->toInt () > SYSTEM_LOAD_MAX) {
                    // SYSTEM_LOAD_TO_HIGH, show an error screen;
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (SYSTEM_LOAD_TOO_HIGH),
                    new S (SYSTEM_LOAD_TOO_HIGH_FIX));
                } else {
                    // If an attempt to re-open the session is made, output an error;
                    if (!self::checkSessionVar (new S ('in_session'), new O (TRUE))->toBoolean ()) {
                        if (self::openSession ()) {
                        	// Set _SESSION variables;
                            if (self::setSessionVar (new S ('in_session'), new O (TRUE)) &&
							self::setSessionVar (new S ('skin'), new O (SKIN)) &&
							self::setSessionVar (new S ('language'), new O (LANGUAGE)) &&
							self::setSessionVar (new S ('default_timezone'), new O (DATE_TIMEZONE))) {
							    // Do return;
                                return new B (TRUE);
                            } else {
                                // Error me proudly;
                                self::renderScreenOfDeath (new S (__CLASS__),
                                new S (CANNOT_START_SESSION),
                                new S (CANNOT_START_SESSION_FIX));
                            }
                        }
                    } else {
                        // Error me proudly;
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_START_SESSION),
                        new S (CANNOT_START_SESSION_FIX));
                    }
                }
            }
        } else {
            // Return the object instance, from the array;
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Sets the HT file, output the content, disable the streams and do some cleanup. This __destruct'or will set the .htaccess file
	 * and also destroy the buffering mechanism while outputing the information back to the client.
     *
     * @return void Doesn't return anything
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see APC::setHTFile()
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
    */
    public function __destruct () {
        // Execute __destruct output only once per platform;
        if ($this->objIdentificationString == __CLASS__) {
            // Set .htaccess;
            self::setHTFile ();

            // Backwards compatible "at-script-end" output;
            if ((self::getErrorStatus ()->toInt () == 1) && (self::$objTPLLoaded == NULL)) {
                if (CLOSE_SESSION_ON_OBJECT_SCOPE == TRUE) {
                    self::closeSession ();
                    self::disableOutputStream ();
                } else {
                    self::disableOutputStream ();
                }

                // Do output buffering, WITHOUT GZIP. If something goes wrong, we will output here, otherwise
                // output will be made in the TPL class ...
                if (self::$objOutputBuffer instanceof A) {
                    echo str_replace (self::$objTokensReplace->toArray (),
                    self::$objStringReplace->toArray (), implode (_NONE, self::$objOutputBuffer->toArray ()));
                } else {
                    self::$objOutputBuffer = new A (self::$objOutputBuffer);
                    echo str_replace (self::$objTokensReplace->toArray (),
                    self::$objStringReplace->toArray (), implode (_NONE, self::$objOutputBuffer->toArray ()));
                }
            }
        }
    }

    /**
     * Will replace the normal output buffering mechanism with our own. This worker method is set by the 'ERR' __constructor as the
	 * default framework worker method. Core developers can hook-up with other specific buffering functions here. As you can actually
	 * see from the code this is the place we actually catch parse errors (appended or prepended with the error string);
     *
     * @todo Try to speed-up the way buffer handling is done. Maybe future PHP versions will have a performance boost in terms
     * of the SPL, and because of the fact that we implemented the buffer as SPL, will gain that advantage;
     * @param S $bufferedStringToParse The buffered string by PHP
     * @param I $bufferState What state is the buffer in; (see more on php.net, ob_start ())
     * @return S Will returned the buffered string
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function executionStreamedOutput ($bufferedStringToParse, $bufferState) {
        if (eregi (self::$objErrorPrependString . _ANY . self::$objErrorAppendString, $bufferedStringToParse, $eregString)) {
            // Set the status to 1;
            self::setErrorStatus ();

            // An error that got catched by out <RA_php_error></RA_php_error> tags, return a FATAL screen;
            return self::renderScreenOfDeath (new S (__CLASS__), NULL, NULL,
            new S (strip_tags (trim ($eregString[0]))), new B (TRUE));
        } else {
            // Check if it's a CSS/JSS file;
            if (checkIfItsACSSFile () or checkIfItsAJSSFile ()) {
	            // It's a CSS file, get OUTPUT as FAST as you CAN!
	            // NO error_detection is done AT THIS LEVEL;
	            return $bufferedStringToParse;
            } else {
            	// Just save the content for output at the end of the script, and go further;
                self::$objOutputBuffer[self::$objOutputBufferCount->doInc ()->toInt ()] = $bufferedStringToParse;
                // Just return NULL, because we don't want any output here;
                return NULL;
            }
        }
    }

    /**
     * Will replace the PHP set_error_handler machanism, outputing an error-screen if an error is detected. This worker method is set by
	 * the ERR __constructor as the worker method for the PHP error handling mechanism. If an error is detected an error screen will
	 * be outputed by this method and the developer will be notified to fix his code prior to releasing it;
     *
     * @todo To implement difference between FATAL and non-FATAL error screens by adding a parameter to the renderScreenOfDeath
     * method, that should change the CSS of the error screen by some method ...
     * @param I (native) $errorType The type of the catched PHP error
     * @param S (native) $errorString The string containing a description of the error
     * @param S (native) $errorFile Path to the file where the error was found in
     * @param I (native) $errorFileNo Number of line in the file where the detected error was found
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function executionCatchPHPErrors ($errorType, $errorString, $errorFile, $errorFileNo) {
        // Check integrity of passed parameters. We're pushing it ...
        $errorType      = new I ($errorType);
        $errorString    = new S ($errorString);
        $errorFile      = new S ($errorFile);
        $errorFileNo    = new I ($errorFileNo);

        // Get the content of the error screen, from a file this time;
        $errPHPStringOutput = new FileContent (FORM_TP_DIR . _S . 'frm_error_php.tp');
        $errPHPStringOutput->doToken ('[%ERR_EMSG%]', $errorString);
        $errPHPStringOutput->doToken ('[%ERR_FILE%]', $errorFile);
        $errPHPStringOutput->doToken ('[%ERR_LINE%]', $errorFileNo);

        // Do the switch, or do the tango. Personally I like waltz;
        switch ($errorType->toInt ()) {
            case 2:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (WARNING_ERROR),
                new S (WARNING_ERROR_FIX),
                new S ($errPHPStringOutput));
            break;
            case 8:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (NOTICE_ERROR),
                new S (NOTICE_ERROR_FIX),
                new S ($errPHPStringOutput));
            break;
            case 256:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (NOTICE_USER_ERROR),
                new S (NOTICE_USER_ERROR_FIX),
                new S ($errPHPStringOutput));
            break;
            case 512:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (WARNING_USER_ERROR),
                new S (WARNING_USER_ERROR_FIX),
                new S ($errPHPStringOutput));
            break;
            case 1024:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (NOTICE_USER_NOTICE),
                new S (NOTICE_USER_NOTICE_FIX),
                new S ($errPHPStringOutput));
            break;
            case 4096:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (NOTICE_RECOVERABLE_ERROR),
                new S (NOTICE_RECOVERABLE_ERROR_FIX),
                new S ($errPHPStringOutput));
            break;
            case 8191:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (NOTICE_ERROR_ALL),
                new S (NOTICE_ERROR_ALL_FIX),
                new S ($errPHPStringOutput));
            break;
            default:
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (FATAL_ERROR),
                new S (FATAL_ERROR_CHECK_LOG),
                new S ($errPHPStringOutput));
            break;
        }

        // If not, check against some PHP constans;
        if ($errorType->toInt () == E_ERROR              ||
            $errorType->toInt () == E_PARSE              ||
            $errorType->toInt () == E_CORE_WARNING       ||
            $errorType->toInt () == E_COMPILE_ERROR      ||
            $errorType->toInt () == E_COMPILE_WARNING    ||
            $errorType->toInt () == E_STRICT) {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (FATAL_ERROR),
            new S (FATAL_ERROR_CHECK_LOG),
            new S ($errPHPStringOutput));
        }
    }

    /**
     * Will set the error status to true, which will cause un-prepending the body content with <head> info. This is an internal method
	 * we use to toggle the error status, thus making the framework append the <html><body> or not to the output buffer. This helps us
	 * output a proper error screen where the CSS is not overriden by the specific CSS of the page we're generating;
     *
     * @return void Will return nothing
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setErrorStatus () {
        switch (self::$objErrorHappened->toInt () == 1) {
            case TRUE:
                // Set self::objErrorHappened to 0;
                self::$objErrorHappened->setInt (0);
                break;

            case FALSE:
                // Set self::objErrorHappened to 1;
                self::$objErrorHappened->setInt (1);
                break;
        }
    }

    /**
     * Will return the objErrorHappened object, that will indicate the catched error status. This method is internally used to determine
	 * if the error status is set or not. Core developers can use this method to understand what has happened since the moment of the
	 * execution of his methods. 
     *
     * @return I Will return an integer != 0, if no an error happened;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see EXE::setErrorStatus()
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getErrorStatus () {
        // Just return the self::objErrorHappened object!
        return self::$objErrorHappened;
    }

    /**
     * Will render the 'Screen Of Death', by using tested and well working PHP functions. This method relies on the fact that, at least
	 * the framework abstract classes are working. Non-core developers should not modify anything here as they can get their hands
	 * dirty really quick and destroy something without even knowing it;
     *
     * @param S $errFrom Error from where did it came
     * @param S $errString What's the string of the error
     * @param S $errTip Do you have an information to show
     * @param S $debugErrorString Should we show the debugger
     * @param B $errFATAL Is this error FATAL
     * @return S Will return the error screen string, or just void, depends on what kind of error we caught
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static function renderScreenOfDeath (S $errFrom, S $errString = NULL, S $errTip = NULL,
    S $debugErrorString = NULL, B $errFATAL = NULL) {
        // Set the execution time, discard the output stream, and set the error status to TRUE;
        self::setExeTime (new S (__FUNCTION__));
        // Add header, if it's a CSS file, on an error!;
        switch (checkIfItsACSSFile() or checkIfItsAJSSFile()) {
            case TRUE:
                // Set the text/HTML, header!;
                self::setHeaderKey (new S ('text/html'), new S ('Content-type:'));
            break;
        }

        // Se the errorStatus to TRUE;
        self::setErrorStatus ();
        $catchedKrumoContent = _NONE;

        // Change behaviour, if this is a FATAL error;
        if (($errFATAL != NULL) && ($errFATAL->toBoolean () == TRUE)) {
            $errString = new S (FATAL_ERROR);
            $errTip = new S (FATAL_ERROR_CHECK_LOG);
            // Clean the output buffer;
            self::$objOutputBuffer = _NONE;
        } else {
        	// Clean the output buffer;
            self::discardOutputStream (new B (TRUE));
            // Execute the KRUMO PLUGIN Framework;
            $catchedKrumoContent = self::getKrumoContent ();
        }

        // Get the file contents;
        $debugContent = new FileContent (FORM_TP_DIR . _S . 'frm_error_screen.tp');
        // Do an if, for the [%CODE%] part of our template;
        if ((GESHI >= 1) && (DEBUG >= 1)) {
            // Replace [%CODE%], with GeSHi parsed code from our PHP files;
            // Really helpfull when trying to find hidden bugs;
            $debugContent->doToken ('[%CODE%]', self::getDebugBacktrace (array_reverse (debug_backtrace ())));
            $debugContent->doToken ('[%KRUMO%]', $catchedKrumoContent);
        } else {
            // Replace [%CODE%], with _NONE, hiding the output;
            // Well, can't help the developer here!;
            $debugContent->doToken ('[%CODE%]', _NONE);
            $debugContent->doToken ('[%KRUMO%]', _NONE);

            // Set as  'Hacking attempt';
            $errString = VIEW_FILE_DIRECTLY_DENIED;
            $errTip = VIEW_FILE_DIRECTLY_DENIED_FIX;
            $debugErrorString = HACKING_ATTEMPT_BANG_YOU_DEAD;
        }

        // Start replacing information in the 'frm_error_screen.tp';
        $debugContent->doToken ('[%HSIMG%]',            DOCUMENT_HOST . IMAGE_DIR . _WS);
        $debugContent->doToken ('[%HSJSS%]',            DOCUMENT_HOST . JAVASCRIPT_DIR . _WS);
        $debugContent->doToken ('[%ERBGR%]',            ERBGR);
        $debugContent->doToken ('[%ERPIX%]',            ERPIX);
        $debugContent->doToken ('[%ERPXL%]',            ERPXL);
        $debugContent->doToken ('[%MEMORY%]',           memory_get_usage ()/1024);
        $debugContent->doToken ('[%PID%]',              getmypid ());
        $debugContent->doToken ('[%MICROTIME%]',        self::getExeTime (new S (__CLASS__), new S (__FUNCTION__)));
        $debugContent->doToken ('[%ERROR_FROM%]',       $errFrom);
        $debugContent->doToken ('[%ERROR_DATE%]',       date (DATE_STRING, $_SERVER['REQUEST_TIME']));
        $debugContent->doToken ('[%ERROR_EMSG%]',       $errString);
        $debugContent->doToken ('[%ERROR_ETIP%]',       $errTip);
        $debugContent->doToken ('[%ERROR_FROM_PHP%]',   $debugErrorString);

        // Try to MAIL ... If we got this far, we have MAIL ...
        if (self::checkClassExistence (new S ('MAIL'))->toBoolean () == TRUE) {
            // Set some requirements ...
            if ($errString == NULL) $errString = new S;
            if ($errTip == NULL) $errTip = new S;

            // Make'em as new as possible ...
            $objEML = new MAIL;
            $objEML->setFrom (new S (MAIL_FROM));
            $objEML->setStringAttachment (new S ($debugContent));
            $objEML->doMAIL  (new S (MAIL_FROM), $errString,
            new S ($errTip . _DCSP . URL::rewriteURL () . _DCSP . $_SERVER['HTTP_USER_AGENT']
            . _DCSP . $_SERVER['REMOTE_ADDR']));
        }

        // Exit, with an error screen. We could also die, it would mean the same;
        if ((($errFATAL != NULL) && ($errFATAL->toBoolean () == TRUE))) {
            // Return the content;
            if (OB_GZIP == TRUE && OB_GZIP_LEVEL > 0 && OB_GZIP_LEVEL <= 9) {
                return (gzencode ($debugContent, OB_GZIP_LEVEL));
            } else {
                return $debugContent;
            }
        } else {
            // Or die script now;
            if (OB_GZIP == TRUE && OB_GZIP_LEVEL > 0 && OB_GZIP_LEVEL <= 9) {
                exit (gzencode ($debugContent, OB_GZIP_LEVEL));
            } else {
                exit ($debugContent);
            }
        }
    }

    /**
     * Will return the string made by parsing a .tp file, and replacing tokens. This method wil actually replace the tokens in the 
	 * framework error-screen template file. This will help us output debugged code to the developer so we can help him quickly find
	 * the error he was looking for;
     *
     * @param A (native) $debugArray The debug array to be parsed
     * @return S (native) The array, parsed as an error screen string
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function getDebugBacktrace ($debugArray) {
        # Set the errorString, to be returned;
        $errorString = _NONE;
        foreach ($debugArray as $k => $v) {
            # Declare some __CODE__ variables;
            $codeFile = _NONE;
            $codeLine = 0;
            $debugContent = new FileContent (FORM_TP_DIR . _S . 'frm_error_debug.tp');
            if (isset ($debugArray[$k]['file'])) {
                # Replace [%FILE%] with the one given in the backtrace;
                $debugContent->doToken ('[%FILE%]',
                $codeFile = str_replace (DOCUMENT_ROOT, _NONE, $debugArray[$k]['file']));
            } else {
                # Else, determine the current __FILE__, and output something;
                $debugContent->doToken ('[%FILE%]',
                $codeFile = str_replace (DOCUMENT_ROOT, _NONE, __FILE__));
            }
            if (isset ($debugArray[$k]['line'])) {
                # Replace [%LINE%] with the one given in the backtrace;
                $debugContent->doToken ('[%LINE%]',
                $codeLine = $debugArray[$k]['line']);
            } else {
                # Set it to LINE: 20, almost near the declaration of our classes;
                $debugContent->doToken ('[%LINE%]',
                $codeLine = 20);
            }
            if (isset ($debugArray[$k]['class'])) {
                # Replace [%CLASS%] with the one in the backtrace;
                $debugContent->doToken ('[%CLASS%]',
                $debugArray[$k]['class']);
            } else {
                # Set it to [no __CLASS__] ...
                $debugContent->doToken ('[%CLASS%]',
                '[no __CLASS__]');
            }
            if (isset ($debugArray[$k]['function'])) {
                $debugContent->doToken ('[%METHOD%]',
                $debugArray[$k]['function']);
            } else {
                $debugContent->doToken ('[%METHOD%]',
                '[no __METHOD__]');
            }
            # Replace [%CODE%] from error file;
            $debugContent->doToken ('[%CODE%]',
            self::getDebugCode (new S ($codeFile), new I ($codeLine)));
            $debugContent->doToken ('[%ID%]', $k);
            $errorString .= $debugContent;
        }
        # After the foreach, return the concatenated error screen;
        return $errorString;
    }

    /**
     * Wrapper method for GeSHi, returning the string parsed by the GeSHi class. This method will query the GeSHi external plugin and
	 * return the highlighted code we use for debugging. We use the GeSHI plugin because such a highlighting mechanism would take us
	 * quite a lot of time to develop on our own;
     *
     * @param S $codeFile The path to the file to be GeSHi'ed
     * @param I $codeLine The number of line (+/-1) that should be GeSHi'ed
     * @return S The highlighted code, passed to it
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function getDebugCode (S $codeFile, I $codeLine) {
        // Do some 'GeSHi' magic. Be happy, life's short! Add DOCUMENT_ROOT to reported FILE;
        $codeFile->setString (DOCUMENT_ROOT . $codeFile);
        // Do the GESHI;
        $GSH = new GeSHi (self::getDebugCodeLine ($codeFile, $codeLine)->toString (), 'php',
        $parsedCode, $codeLine->toInt ());
        // Return the GeSHi syntax-colored code;
        return new S ($parsedCode);
    }

    /**
     * Will return a string containing three lines from the PHP file that was in a debug_backtrace, by parsing the previous, current and
	 * next line from the debugged file. It's an easy way to get the content of a file in many circumstances. Also, for EVAL'ed code,
	 * where we actually don't have a file, we issue a default NULL string back to the calling method so execution can go on;
     *
     * @param S $codeFile The path to the file to be GeSHi'ed
     * @param S $codeLine The number of line (+/-1) that should be GeSHi'ed
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function getDebugCodeLine (S $codeFile, I $codeLine) {
        // S make;
        $theString = new S;
        // Get the file array from the passed argument;
        if (WIN == TRUE) {
            // Get the contents;
            if (file_exists ($codeFile)) {
                // PHP Code: FILE;
                $theFileArray = file ($codeFile->doToken (DOCUMENT_ROOT,
                _NONE)->toString (), FILE_SKIP_EMPTY_LINES);
            } else {
                // PHP Code: EVAL;
                return new S (_NONE);
            }
        } else {
            // Get the contents;
            if (file_exists ($codeFile)) {
                // PHP Code: FILE;
                $theFileArray = file ($codeFile->toString (),
                FILE_SKIP_EMPTY_LINES);
            } else {
                // PHP Code: EVAL;
                return new S (_NONE);
            }
        }

		// Files that are bellow 3 LINES have problems, so here's the fix;
		if (count ($theFileArray) >= 3) {
			if (isset ($theFileArray[$codeLine->toInt () - 2])) {
				$theString->setString ($theFileArray[$codeLine->toInt () - 2] .
				$theFileArray[$codeLine->toInt () - 1] .
				$theFileArray[$codeLine->toInt ()]);
			}
		} else if (count ($theFileArray) == 1) {
			$theString->setString ($theFileArray[$codeLine->toInt () - 1]);
		} else if (count ($theFileArray) == 2) {
			$theString->setString ($theFileArray[$codeLine->toInt () - 1] .
			$theFileArray[$codeLine->toInt ()]);
		}

		// We should empty theFileArray straight away, and return the string;
		unset ($theFileArray); return $theString;
    }

    /**
     * Will set all necessary headers to ensure that NO cache will be done by the browser. We set this method in the framework to skip
	 * some refresh bugs caused by not sending these headers. These will ensure that the content the user gets are the latest. If we
	 * actually want to do some caching, then that caching should be done server-side and relied more on the server than on the user;
     *
     * @return void Will not return a thing;
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function setCacheHeaderKeys () {
        if (!(checkIfItsACSSFile () or checkIfItsAJSSFile ())) {
            self::setHeaderKey (new S ('no-store, no-cache, must-revalidate'),  new S ('Cache-Control'));
            self::setHeaderKey (new S ('pre-check=0, post-check=0, max-age=0'), new S ('Cache-Control'));
            self::setHeaderKey (new S ('no-cache'), new S ('Pragma'));
        }
    }

    /**
     * Will return the content generated by the KRUMO plugin. We use the KRUMO to automatically dump the variables generat @ run-time,
	 * when an error occured to help the develper quickly debug some of it's variables. This helps him skip some var_dump () actions
	 * just to check the content of a variable, which we can do with just a mouse-click in the echoed browser error-screen;
     *
     * @return S Catched Krumo contented, parsed by the KRUMO plugin, for error/variable dumping
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 08_ERR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
    */
    private static final function getKrumoContent () {
    	// Determine if we can KRUMO, only if the ERR has passed;
    	// If _GET instanceof A, means that the our URL parser has loaded;
    	if (isset ($_GET)) {
	    	if ($_GET instanceof A) {
		    	// Execute the KRUMO Framework PLUGIN;
		    	// Wished we could've made an object out of Krumo, but we will call it statically ...
		    	krumo::get ();
		    	krumo::post ();
		    	krumo::session ();
		    	krumo::cookie();
		    	krumo::headers ();
		    	krumo::includes ();
		    	krumo::server ();
		    	krumo::env ();
		    	krumo::conf ();
		    	krumo::extensions ();
		    	krumo::interfaces ();
		    	krumo::path ();

		    	// Get its content;
	            $catchedKrumoContent = self::getContentFromOutputStream ();

	            // Save it, and then clean the stream again;
	            self::discardOutputStream (new B (TRUE));

	            // Yey! It's over!
	            return $catchedKrumoContent;
	    	}
    	}
    }
}
?>
