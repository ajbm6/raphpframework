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

############# Motto: 'Hug me, kiss me, love me ... I'm your love stream ...';
/**
 * Abstract CLASS implementing the core of our Output Buffering mechanism, the methods that set the OutputStreamHandler and Catcher
 * worker methods, the buffer array, the token string/replace array and other operations regarding the stored buffer;
 *
 * @package RA-Output-Buffering
 * @category RA-Abstract-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class OBS extends INI {
    /**
     * @staticvar $objOutputStreamHandler Container for the Output Buffering Execution Stream Handler;
     * @staticvar $objOutputStreamCatcher Container for the PHP set_error_handler mechanism;
     * @staticvar $objOutputBuffer Container for the PHP Output Buffer, so we can echo it at the end of the script;
     * @staticvar $objTokensReplace Array of 'tokens' to be replaced the outputing the buffer;
     * @staticvar $objStringReplace Array of strings to replace the current tokens set in the buffer;
    */
    protected static $objName                   = 'OBS :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* OUTPUT: Function Callbacks  */
    private static $objOutputStreamHandler      = NULL;
    private static $objOutputStreamCatcher      = NULL;

    /* OUTPUT: Counter & Containers */
    protected static $objOutputBuffer           = NULL;
    protected static $objOutputBufferCount      = NULL;

    /* TOKENS: Output tokens */
    protected static $objTokensReplace          = NULL;
    protected static $objStringReplace          = NULL;

    /**
     * Used to set the Output Stream (Buffer) Callback Worker method. This method is used to automatically set a 'worker' method for
	 * our framework. For example, this method is automatically set by the 'ERR' error-handling mechanism, to a method it defines, but
	 * the same can be done for a different, user-created method which can be implemented. You need just to define its name;
	 * <code>
	 * <?php
	 *		// Set another worker method you've chosen;
	 *		self::setOutputStreamCallbackWorker (new S ('aWorkerMethodIMadeInTheCurrentCLASS'));
	 * ?>
	 * </code>
     *
     * @param S $objOutputStream The name of the worker method for the output stream
     * @return B Will return true if the variable was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/ob_start
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setOutputStreamCallbackWorker (S $objOutputStream) {
        // First, check we can CALL it;
        if (self::checkMethodIsCallable ($objOutputStream)->toBoolean ()) {
            // Everything is in order, so we can CALL it;
            if (self::$objOutputStreamHandler = $objOutputStream) {
                // Do return ...
                return new B (TRUE);
            } else {
                // Do return ...
                return new B (FALSE);
            }
        } else {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_SET_OUTPUT_WORKER),
            new S (CANNOT_SET_OUTPUT_WORKER_FIX));
        }
    }

    /**
     * Used to set the ErrorCatcherCallback for the PHP set_error_handler mechanism. The error handling mechanism implemented in PHP
	 * needs a 'worker' method it can actually CALL to provide error handling capabilities to the developer. We've already implemented
	 * such a function in our 'ERR' error-handling mechanism, but the developer can replace that method with his own using this method;
	 * <code>
	 * <?php
	 * 		// Set an error handling method you've chosen;
	 *		self::setOutputErrorCatcherCallback (new S ('anErrorHandlingMethodInTheCurrentCLASSIHaveMade'));
	 * ?>
	 * </code>
     *
     * @param S $objOutputStream The name of the worker method for catching errors
     * @return B Will return true if the variable was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/set_error_handler
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setOutputErrorCatcherCallback (S $objOutputStream) {
        // First, check we can CALL it;
        if (self::checkMethodIsCallable ($objOutputStream)->toBoolean ()) {
            // Everything is in order, so we can CALL it;
            if (self::$objOutputStreamCatcher = $objOutputStream) {
                // Do return ...
                return new B (TRUE);
            } else {
                // Do return ...
                return new B (FALSE);
            }
        } else {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_SET_ERROR_CATCHER),
            new S (CANNOT_SET_ERROR_CATCHER_FIX));
        }
    }

    /**
     * Used to start the Output Stream, in concurency with the WorkerCallback and ErrorCatcherCallback methods. The method is used
	 * by the internal 'ERR' error-handling mechanism to start the Output Buffering mechanism and to set the proper error-handling worker
	 * method used to detect PHP errors;
     *
     * @return B Will return TRUE if it has executed the output stream and error catcher methods
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function executeOutputStream () {
        // Execute the output stream;
        if ((ob_start (self::$objOutputStreamHandler->toString ())) &&
        (set_error_handler (self::$objOutputStreamCatcher->toString ()))) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Used to end the OutputStream, if detected LVL is != 0. You can use this method if you wish to disable and flush the output
	 * stream to the browser. This function will not trigger if the output buffer LEVEL == 0, because that would again trigger a PHP
	 * error, which in the context of a specific RA Output Buffering mechanism will a blank page (ob functions SHOULD NOT error!);
     *
     * @return B It will return true if it has flushed the output stream
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/ob_end_flush
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function disableOutputStream () {
        if (self::getOutputStreamIdNo ()->toInt () != 0) {
            // Flush and clean;
            return new B (ob_end_flush ());
        }
    }

    /**
     * Used to change tokens set in the Output Stream, adding a little level of "dynamicity. If you ever need to modify a specific work
	 * or a specific string in the output buffer, something you need to masivelly change but are unable to, you can use this little
	 * post-processing method to modify the string held in the output buffer according to your needs. For exameple: 
	 * TPL::changeOutputStreamToken ('a', 'b') - will replace ALL a's with b's in the page you're about to send to the browser;
	 * <code>
	 * <?php
	 *			// Change 'Microsoft' to 'Linux' for ALL rendered pages
	 *			self::changeOutputStreamToken (new S ('Microsoft'), new S ('Linux'));
	 * ?>
	 * </code>
	 *
     * @param S $stringToReplace The token to be replaced in the string
     * @param S $withWhatToReplace The string that will replace the token
     * @return B will return true if the values have been memorized for parsing
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function changeOutputStreamToken (S $stringToReplace, S $withWhatToReplace) {
        // Add'em to the array;
        if ((self::$objTokensReplace[] = $stringToReplace) &&
            (self::$objStringReplace[] = $withWhatToReplace)) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Used to empty the contents of the Output Stream, in two ways. Any of the ways you decide to use (one soft, one hard) - will
	 * allow you to discard the output buffer up to the point of execution. This can allow you for example to echo binary or XML data,
	 * without the need to worry about the automatically pre- and appended HTML tags or already echoed output. These are discared by
	 * using this method in conjuction with switchHTML from the TPL;
     *
     * @param B $whatKindOfCleaning What kind of cleaning should be done, hard or soft
     * @return B Will return true in either cases, if everything was OK
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function discardOutputStream (B $whatKindOfCleaning = NULL) {
        // Verify we do HAVE an opened stream;
        if (self::getOutputStreamIdNo ()->toInt () != 0) {
            if ($whatKindOfCleaning != NULL) {
                // Dump the self::objOutputBuffer;
                ob_clean ();
                self::$objOutputBuffer = NULL;
                // Do return ...
                return new B (TRUE);
            } else {
                // Just don't dump it, weird cleaning;
                ob_clean ();
                // Do return ...
                return new B (TRUE);
            }
        }
    }

    /**
     * Used to die the current script, so no further content is outputted. Instead of actually using an ugly die () PHP function in our
	 * code, we use this 'fancy' naming for a function that will disable any further output from the script to the client. Also, it's
	 * a container method that can be used for example if some cleaning operations are at hand (for example the ones in the GBC (garbage
	 * collector) of our framework;
	 * <code>
	 * <?php
	 *		// We just want a fancy way to DIE!;
	 *		self::disableFurtherOutput ();
	 * ?>
	 * </code>
     *
     * @return void Won't return a thing
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function disableFurtherOutput () {
        // Just DIE ...
        die ();
    }

    /**
     * Used to return the content of the Output Stream. Mainly used in caching, if we want to get the Output Stream and save it as so,
	 * to a file we will specify. Do not forget that for each output buffer you open, the context from which you open the buffer, until
	 * you close it is specific for that buffer. This way, you can have template caching, by opening and closing output buffers as you
	 * wish in the context of your projector application.
	 * <code>
	 * <?php
	 *		// Save the content to a FILE ...
	 *		file_put_contents (self::getContentFromOutputStream ());
	 * ?>
	 * </code>
     *
     * @return S Will return the contents of the output stream
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/ob_get_contents
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getContentFromOutputStream () {
        // Do return ...
        return new S (ob_get_contents ());
    }

    /**
     * Used to return the length of the content of the output stream. Usually, you can use this method to determine if we need to
	 * do something and change the content of the cached file, or not (if the content changes in size). Usually used as a method to
	 * call the specific @ob_get_length as specified by the PHP documentation;
     *
     * @return I Will return the length of the output stream
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/ob_get_length
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getOutputStreamSize () {
        // Do return ...
        return new I (ob_get_length ());
    }

    /**
     * Used to return the #id of the current output stream, automatically assigned by PHP. On some very rare occasions you may need to
	 * know on what LEVEL of buffering you are at (for example, above, to determine if you're going to CALL ob_end_flush) if you're at
	 * the LEVEL 0. Such a function can be used for purposes like limiting the number of open buffers or doing other fancy stuff;
     *
     * @return I Will return the nesting level of the current output stream
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/ob_get_level
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getOutputStreamIdNo () {
        // Do return ...
        return new I (ob_get_level ());
    }

    /**
     * Used to write the generated output stream to a file. It only does so much as dumping the contents of the output buffer to an user
	 * specified file. We made this method so we could have a quick method in our TPL object to dump the cached content to a file,
	 * without needing to have buggy code inserted in the TPL::tpExe method for writing a file, thus trying to relly on much of the
	 * functionality on the specific FilePath/FileContent DataType, more than on developer created code;
     *
     * @param FilePath $whereToSaveOutputStream The path to the file where to save the output stream
     * @return B Will return true if the content was dumped to the file
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License
	 * @version $Id: 04_OBS.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function writeOutputStreamToFile (FilePath $whereToSaveOutputStream) {
        // Do return ...
    	return $whereToSaveOutputStream->putToFile (self::getContentFromOutputStream ());
    }
}
?>
