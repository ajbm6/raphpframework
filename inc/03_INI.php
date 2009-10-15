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

############# Motto: 'Looking forward, look behind ...';
/**
 * Abstract class that handles all the ini_set: PHP_INI_ALL settings and initiates the RA execution environment. Implements the basics
 * of our error handling mechanism, sets the required error tags and checks for proper permissions;
 *
 * @package RA-Framework-Core-Init-And-Settings
 * @category RA-Abstract-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class INI extends LOG {
    /**
     * @staticvar $objErrorPrependString For parsing purposes, prepend any error string with this string;
     * @staticvar $objErrorAppendString Do the same as prepending, but only append the string here;
    */
    protected static $objName                   = 'INI :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    protected static $objErrorPrependString     = NULL;
    protected static $objErrorAppendString      = NULL;

    /**
     * Used to set the, for ex: <RA_php_error>, error-prepend-tag. This tag is processed in the Output Buffering function to determine,
	 * for example if a parsing error has happened or if other types of non-catchable errors through the PHP error-handling mechanism,
	 * have arrisen. For that, we set a specific tag (the above) which can then be searched for in the buffer;
     *
     * @param S $errorPrependString The string to prepend errors with
     * @return B Will return true if the variable was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 03_INI.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setRAErrorPrependString (S $errorPrependString) {
        // Just set the INTERNAL objErrorPrependString ...
        if (self::$objErrorPrependString = $errorPrependString) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Used to set the, for ex: </RA_php_error>, error-append-tag. Any open tag has a closing tag. This tag is appended after the error
	 * string. In combination, these tags can help us actually catch the error that PHP has given us, so we can report it back to the
	 * developer and make the developer fix that certain issue;
     *
     * @param S $errorAppendString The string to append errors with
     * @return B Will return true if the path was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 03_INI.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setRAErrorAppendString (S $errorAppendString) {
        // Just set the INTERNAL objErrorAppendString ...
        if (self::$objErrorAppendString = $errorAppendString) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set the PHP_INI_ALL directives that can be set through @ini_set from PHP. This method, will try to: set the script time,
	 * the LC_LOCALE, the external RA plugins, the include path and most of all them settable configuration options marked PHP_INI_ALL
	 * in the official documentation of the PHP language. If something ever goes wrong, this method will report back an error through
	 * the ERR method of 'renderScreenofDeath' - not allowing the script to execute further;
     *
     * @return B If everything was OK, returns a TRUE
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 03_INI.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setRAPHPFrameworkINIObjectProps () {
        // Set the script time, to our own SCRIPT_TIME_LIMIT constant;
        set_time_limit (SCRIPT_TIME_LIMIT);

        // Set the LOCALE information, really needed ...
        setlocale (LC_ALL, LANGUAGE);

        // Load the RA PHP Framework PLUGINs;
        self::setRAGeshiSyPath (new FilePath (PLUGIN_DIR . _S . 'geshi_syntax/Geshi.php'));
        self::setRAUserAgentSniffer (new FilePath (PLUGIN_DIR . _S . 'php_browser/phpSniff.class.php'));
        self::setRAKrumoPath (new FilePath (PLUGIN_DIR . _S . 'php_krumo/class.krumo.php'));
        self::requireRAPHPFrameworkLOWLEVELPlugins ();

        // Set the error prepend/append strings;
        self::setRAErrorPrependString (new S ('<RA_php_error>'));
        self::setRAErrorAppendString (new S ('</RA_php_error>'));

        // Set the PHP include path, to what was, adding our own;
        if (set_include_path (get_include_path () . PATH_SEPARATOR . DOCUMENT_ROOT . INCLUDE_DIR)) {
            // Set the PHP options that can be set, or die on error;
            if (ini_set ('memory_limit', MEMORY_LIMIT) !== FALSE) {
                // Set error_reporting; IMPORTANT!
                if (ini_set ('error_reporting',
                ERROR_REPORTING_LEVEL) !== FALSE) {
                    // Go forward;
                } else {
                    // Nope, error here!
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (FATAL_ERROR),
                    new S (FATAL_ERROR_CHECK_LOG),
                    new S (FATAL_CANNOT_SET_ERROR_REPORTING));
                }

                // Set the session.cache_expire;
                if (ini_set ('session.cache_expire',
                SESSIONCACHEEXPIRE) !== FALSE) {
                    // Go forward;
                } else {
                    // Nope, error here!
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (FATAL_ERROR),
                    new S (FATAL_ERROR_CHECK_LOG),
                    new S (FATAL_CANNOT_SET_CACHE_EXPIRE));
                }

                // And the max_execution_time of a script;
                if (ini_set ('max_execution_time',
                SCRIPT_TIME_LIMIT) !== FALSE) {
                    // Go forward;
                } else {
                    // Nope, error here!
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (FATAL_ERROR),
                    new S (FATAL_ERROR_CHECK_LOG),
                    new S (FATAL_CANNOT_SET_SCRIPT_TIME_LIMIT));
                }

                // While showing me errors;
                if (ini_set ('display_errors',
                DISPLAY_ERRORS) !== FALSE) {
                    // Go forward;
                } else {
                    // Nope, error here!
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (FATAL_ERROR),
                    new S (FATAL_ERROR_CHECK_LOG),
                    new S (FATAL_CANNOT_SET_DISPLAY_ERRORS));
                }

                // Set other non-important settings;
                ini_set ('display_startup_errors', DISPLAY_STARTUP_ERRORS);
                ini_set ('default_charset', DEFAULT_CHARSET);
                ini_set ('error_log', DOCUMENT_ROOT . LOG_DIR . _S . PHP_ERROR_LOG);
                ini_set ('html_errors', PHP_HTML_ERRORS);
                ini_set ('date.timezone', DATE_TIMEZONE);
                ini_set ('implicit_flush', IMPLICIT_FLUSH);
                ini_set ('error_prepend_string', self::$objErrorPrependString);
                ini_set ('error_append_string', self::$objErrorAppendString);

                // Set default framework mapping ...
                self::mapMethodToFunction ('escapeCString',     'addcslashes');
                self::mapMethodToFunction ('escapeString',      'addslashes');
                self::mapMethodToFunction ('toHex',             'bin2hex');
                self::mapMethodToFunction ('toChunk',           'chunk_split');
                self::mapMethodToFunction ('encryptIt',         'crypt');
                self::mapMethodToFunction ('chrToASCII',        'chr');
                self::mapMethodToFunction ('convertCYR',        'convert_cyr_string');
                self::mapMethodToFunction ('uDecode',           'convert_uudecode');
                self::mapMethodToFunction ('uEncode',           'convert_uuencode');
                self::mapMethodToFunction ('countChar',        	'count_chars');
                self::mapMethodToFunction ('toCRC32',           'crc32');
                self::mapMethodToFunction ('toHebrew',          'hebrev');
                self::mapMethodToFunction ('toNLHebrew',        'hebrevc');
                self::mapMethodToFunction ('entityDecode',      'html_entity_decode');
                self::mapMethodToFunction ('entityEncode',      'htmlentities');
                self::mapMethodToFunction ('charDecode',        'htmlspecialchars_decode');
                self::mapMethodToFunction ('charEncode',        'htmlspecialchars');
                self::mapMethodToFunction ('trimLeft',          'ltrim');
                self::mapMethodToFunction ('trimRight',         'rtrim');
                self::mapMethodToFunction ('toMD5File',         'md5_file');
                self::mapMethodToFunction ('toMD5',             'md5');
                self::mapMethodToFunction ('toMetaphoneKey',    'metaphone');
                self::mapMethodToFunction ('toMoneyFormat',     'money_format');
                self::mapMethodToFunction ('nL2BR',             'nl2br');
                self::mapMethodToFunction ('ordToASCII',        'ord');
                self::mapMethodToFunction ('qpDecode',          'quoted_printable_decode');
                self::mapMethodToFunction ('qpEncode',          'quoted_printable_encode');
                self::mapMethodToFunction ('toSHA1File',        'sha1_file');
                self::mapMethodToFunction ('toSHA1',            'sha1');
                self::mapMethodToFunction ('toSoundEx',         'soundex');
                self::mapMethodToFunction ('doCSV',             'str_getcsv');
                self::mapMethodToFunction ('replaceIToken',     'str_ireplace');
                self::mapMethodToFunction ('doPad',             'str_pad');
                self::mapMethodToFunction ('doRepeat',          'str_repeat');
                self::mapMethodToFunction ('doShuffle',         'str_shuffle');
                self::mapMethodToFunction ('toROT13',           'str_rot13');
                self::mapMethodToFunction ('doSplit',           'str_split');
                self::mapMethodToFunction ('toWordCount',       'str_word_count');
                self::mapMethodToFunction ('compareCaseTo',     'strcasecmp');
                self::mapMethodToFunction ('compareNCaseTo',    'strncasecmp');
                self::mapMethodToFunction ('compareTo',         'strcmp');
                self::mapMethodToFunction ('compareNTo',        'strncmp');
                self::mapMethodToFunction ('stripTags',         'strip_tags');
                self::mapMethodToFunction ('removeCStr',        'stripcslashes');
                self::mapMethodToFunction ('removeStr',         'stripslashes');
                self::mapMethodToFunction ('findIPos',          'stripos');
                self::mapMethodToFunction ('findPos',           'strpos');
                self::mapMethodToFunction ('findILPos',         'strripos');
                self::mapMethodToFunction ('findLPos',          'strrpos');
                self::mapMethodToFunction ('findIFirst',        'stristr');
                self::mapMethodToFunction ('findFirst',         'strstr');
                self::mapMethodToFunction ('findLast',          'strrchr');
                self::mapMethodToFunction ('doReverse',         'strrev');
                self::mapMethodToFunction ('toLength',          'strlen');
                self::mapMethodToFunction ('natCaseCmp',        'strnatcasecmp');
                self::mapMethodToFunction ('natCmp',            'strnatcmp');
                self::mapMethodToFunction ('charSearch',        'strpbrk');
                self::mapMethodToFunction ('doTokenize',        'strtok');
                self::mapMethodToFunction ('toLower',           'strtolower');
                self::mapMethodToFunction ('toUpper',           'strtoupper');
                self::mapMethodToFunction ('doTranslate',       'strtr');
                self::mapMethodToFunction ('doSubStr',          'substr');
                self::mapMethodToFunction ('doSubCompare',      'substr_compare');
                self::mapMethodToFunction ('doSubCount',        'substr_count');
                self::mapMethodToFunction ('doSubReplace',      'substr_replace');
                self::mapMethodToFunction ('wrapWords',         'wordwrap');
                self::mapMethodToFunction ('changeKeyCase',     'array_change_key_case');
                self::mapMethodToFunction ('doBZCompress',      'bzcompress');
                self::mapMethodToFunction ('doBZDecompress',    'bzdecompress');
                self::mapMethodToFunction ('doBZOpen',          'bzopen');
                self::mapMethodToFunction ('doLZFCompress',     'lzf_compress');
                self::mapMethodToFunction ('doLZFDecompress',   'lzf_decompress');
                self::mapMethodToFunction ('changeDirectory',   'chdir');
                self::mapMethodToFunction ('scanDirectory',     'scandir');
                self::mapMethodToFunction ('getCWorkingDir',    'getcwd');
                self::mapMethodToFunction ('stripSlashes',      'stripslashes');
                self::mapMethodToFunction ('eregReplace',       'ereg_replace');
                self::mapMethodToFunction ('fileGetContents',   'file_get_contents');
                self::mapMethodToFunction ('filePutContents',   'file_put_contents');
                self::mapMethodToFunction ('inArray',           'in_array');
                self::mapMethodToFunction ('fromStringToArray', 'explode');

                // Do return ...
                return new B (TRUE);
            } else {
                // Do an error;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (FATAL_ERROR),
                new S (FATAL_ERROR_CHECK_LOG),
                new S (FATAL_CANNOT_SET_MEMORY_LIMIT));
            }
        } else {
            // Do an error;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (FATAL_ERROR),
            new S (FATAL_ERROR_CHECK_LOG),
            new S (CANNOT_SET_PHP_INCLUDE_PATH));
        }
    }

    /**
     * Will return the server average loading. You can use this function if you need to determine the average load of the server for
	 * the lats 5, 10, 15 minutes, thus having a tool which could allow you to redirect users to other servers, depending on the
	 * current server LOAD. If by change the project is hosted on an Windows platform, this function will always return 0. Also, keep
	 * in mind that if in the error mechanism this integer is over SYSTEM_LOAD_MAX an error is automatically shown;
	 * <code>
	 * <?php
	 *		// If on a Linux server;
	 *		if (self::getRAPHPFrameworkAverageLoading ()->toInt () > 90) {
	 *			// Echo;
	 *			echo 'SERVER LOAD IS TOO HIGH!';
	 *		} else {
	 *			// Amaze me!
	 *		}
	 * ?>
	 * </code>
     *
     * @return I Returns the current load of the server
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 03_INI.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function getRAPHPFrameworkAverageLoading () {
        // Test to see if WIN == FALSE, or WIN == TRUE'.
        // If on WIN, will return 0;
        if (WIN == FALSE) {
            // Get the AVG;
            if ($averageLinuxLoad = new A (sys_getloadavg ())) {
                // Do return ...
                return new I ((int) $averageLinuxLoad[0]);
            } else {
                // Do an error;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (FATAL_ERROR),
                new S (FATAL_ERROR_CHECK_LOG),
                new S (CANNOT_RETURN_SYSTEM_LOAD_AVG));
            }
        } else {
            // It's like returning 0 (zero) so getLoadAverage > SYSTEM_LOAD_MAX on (WIN == FALSE) == FALSE;
            return new I (0);
        }
    }
}
?>
