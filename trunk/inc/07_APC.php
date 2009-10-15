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

############# Motto: 'What's the difference between an Apache or a Cheyenne!? ...';
/**
 * Abstract CLASS providing a way to control the .htaccess file, the same file that allows us to set PHP_INI_PERDIR options on our
 * execution environment, options like the auto_append/prepend_file, max_upload and others;
 *
 * @package RA-Apache-Management
 * @category RA-Abstrat-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class HTA extends SES {
    /**
     * @staticvar $objHT An internal array, holding the .htaccess lines that need to be written to the file.
     * @staticvar $objHTWriteIt Check to see if the .htaccess writing was OK;
    */
    protected static $objName                   = 'HTA :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    protected static $objHT                     = NULL;
    protected static $objHTWriteIt              = NULL;

    /**
     * Adds a HTACCESS Line to be written at the end of the script. This method will add an .htaccess LINE at the end of the already
	 * automatically generated .htaccess file the framework renders with some defaults. This gives us the possibility to have dinamically
	 * generated .htaccess files and also to set some PHP_INI_PERDIR configuration options we can't set from inside the script;
	 * <code>
	 * <?php
	 *		// Set another RewriteRule, for ex:
	 *		APC::rewriteHTLine (new S ('RewriteRule ... something here ...'));
	 * ?>
	 * </code>
     *
     * @param S $htString The string to be added to the file array
     * @return B Will return true if the line was added
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 07_APC.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function rewriteHTLine (S $htString) {
        if (self::$objHT[] = $htString) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Writes the .htaccess file only if there is a difference between the current imploded string and the file source. This method is
	 * a private method used to write the .htaccess file only if there is a difference between the imploded string (from the internal
	 * htArray variable) and the file source. If the two strings differ, then we rewrite the .htaccess file.
     *
     * @param S $htString The string, parsed from file array to be written
     * @return B Will return true if the file was written
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 07_APC.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function writeHTFile (S $htString) {
    	// Do a quick .htaccess write;
        if ($htFile = fopen (DOCUMENT_ROOT . '.htaccess', 'w')) {
            fwrite ($htFile, $htString->toString ());
            fclose ($htFile);
            // Do return ...
            return new B (TRUE);
        } else {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_WRITE_HTACCESS),
            new S (CANNOT_WRITE_HTACCESS_FIX));
        }
    }

    /**
     * Will check the current .htaccess file and the array containing the .htaccess lines to be written and if there's a difference
	 * it will check to see if it can touch the file or if it exists - so it can decide what to do with it. This method is triggered
	 * by the initialization mechanism to prepare the PHP environment for our script execution;
     *
     * @return B Will return true if the .htaccess file was written
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 07_APC.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setHTFile () {
    	if (self::$objHTWriteIt instanceof I) {
	        if (self::$objHTWriteIt->toInt () == 1) {
	            // Do the Array thing!;
	            if (sizeof (self::$objHT) > 0) {
	                // Implode the array in a string;
	                $htString = new S (implode (_N_, self::$objHT->toArray ()));
	                // Check if the file exists!
	                if (file_exists (DOCUMENT_ROOT . '.htaccess')) {
	                    if ($htString != file_get_contents (DOCUMENT_ROOT . '.htaccess') ||
	                        strlen ($htString) != filesize (DOCUMENT_ROOT . '.htaccess')) {
	                        self::writeHTFile ($htString);
                            // Do return ...
	                        return new B (TRUE);
	                    }
	                } else {
	                    // Touch the HTACCESS;
	                	if (touch (DOCUMENT_ROOT . '.htaccess')) {
	                	    self::writeHTFile ($htString);
	                	    // Do return ...
	                	    return new B (TRUE);
	                	}
	                }
	            } else {
	                return new B (FALSE);
	            }
	        }
    	}
    }

    /**
     * Sets some automatic RA PHP Framework .htaccess options that really drive the core of the framework. These include some Apache
	 * specific settings, or some of the PHP_INI_PERDIR configuration options that can be set only through the .htaccess file. Also,
	 * the auto_append_file and auto_prepend_file settings are automatically written here so the developer is cleared having to
	 * think what's included and where, as we provide an autoloading mechanism for this framework;
     *
     * @return B Will return true if everything was fine and dandy
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 07_APC.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setHTAutoPHP () {
        self::$objHTWriteIt = new I (1);
        if (is_writeable (DOCUMENT_ROOT)) {
        	// Make them URLs VALID && the 401 error URL, needs to be LOCAL ...
        	define ('ERROR_DOC_HOST', _S (DOCUMENT_HOST)->doToken (_SP, '%20')->doToken ('www.', _NONE));
        	define ('ERROR_401_HOST', _S (DOCUMENT_HOST)->doToken (_SP, '%20')->doToken (DOCUMENT_HOST, _NONE));

            // We need some .htaccess defaults;
            self::$objHT[] = 'IndexIgnore *';
            self::$objHT[] = 'AddType text/x-component .htc';
            self::$objHT[] = 'SetEnv TZ '                           . DATE_TIMEZONE;
            self::$objHT[] = 'Options All '                         . APACHE_OPTIONS;
            self::$objHT[] = 'AddDefaultCharset '                   . DEFAULT_CHARSET;
            self::$objHT[] = 'ServerSignature '                     . APACHE_SERVER_SIGNATURE;
            self::$objHT[] = 'php_value auto_prepend_file "'        . DOCUMENT_ROOT . INCLUDE_DIR . _S . AUTO_PREPEND_FILE  . '"';
            self::$objHT[] = 'php_value auto_append_file  "'        . DOCUMENT_ROOT . INCLUDE_DIR . _S . AUTO_APPEND_FILE   . '"';
            self::$objHT[] = 'php_value upload_max_filesize '       . UPLOAD_MAX_FILESIZE;
            self::$objHT[] = 'php_value post_max_size '             . POST_MAX_SIZE;
            self::$objHT[] = 'php_value asp_tags '                  . ASP_TAGS;
            self::$objHT[] = 'php_value register_long_arrays '      . REGISTER_LONG_GPC;
            self::$objHT[] = 'php_value short_open_tag '            . SHORT_OPEN_TAG;
            self::$objHT[] = 'php_value max_input_time '            . MAX_INPUT_TIME;
            self::$objHT[] = 'php_value tidy.clean_output '         . TIDY_CLEAN_OUTPUT;
            self::$objHT[] = 'php_value session.gc_maxlifetime '    . SESSIONCACHEEXPIRE;
            self::$objHT[] = 'php_value session.use_trans_sid '     . SESSION_USE_TRANSPARENT_ID;
            self::$objHT[] = 'ErrorDocument 400 '                  	. ERROR_DOC_HOST . 'Error/400';
            self::$objHT[] = 'ErrorDocument 401 '                  	. ERROR_401_HOST . 'Error/401';
            self::$objHT[] = 'ErrorDocument 402 '                  	. ERROR_DOC_HOST . 'Error/402';
            self::$objHT[] = 'ErrorDocument 403 '                  	. ERROR_DOC_HOST . 'Error/403';
            self::$objHT[] = 'ErrorDocument 404 '                  	. ERROR_DOC_HOST . 'Error/404';
            self::$objHT[] = 'ErrorDocument 405 '                  	. ERROR_DOC_HOST . 'Error/405';
            self::$objHT[] = 'ErrorDocument 406 '                  	. ERROR_DOC_HOST . 'Error/406';
            self::$objHT[] = 'ErrorDocument 407 '                  	. ERROR_DOC_HOST . 'Error/407';
            self::$objHT[] = 'ErrorDocument 408 '                  	. ERROR_DOC_HOST . 'Error/408';
            self::$objHT[] = 'ErrorDocument 409 '                  	. ERROR_DOC_HOST . 'Error/409';
            self::$objHT[] = 'ErrorDocument 410 '                  	. ERROR_DOC_HOST . 'Error/410';
            self::$objHT[] = 'ErrorDocument 411 '                  	. ERROR_DOC_HOST . 'Error/411';
            self::$objHT[] = 'ErrorDocument 412 '                  	. ERROR_DOC_HOST . 'Error/412';
            self::$objHT[] = 'ErrorDocument 413 '                  	. ERROR_DOC_HOST . 'Error/413';
            self::$objHT[] = 'ErrorDocument 414 '                  	. ERROR_DOC_HOST . 'Error/414';
            self::$objHT[] = 'ErrorDocument 415 '                  	. ERROR_DOC_HOST . 'Error/415';
            self::$objHT[] = 'ErrorDocument 416 '                  	. ERROR_DOC_HOST . 'Error/416';
            self::$objHT[] = 'ErrorDocument 417 '                  	. ERROR_DOC_HOST . 'Error/417';
            self::$objHT[] = 'ErrorDocument 422 '                  	. ERROR_DOC_HOST . 'Error/422';
            self::$objHT[] = 'ErrorDocument 423 '                  	. ERROR_DOC_HOST . 'Error/423';
            self::$objHT[] = 'ErrorDocument 424 '                  	. ERROR_DOC_HOST . 'Error/424';
            self::$objHT[] = 'ErrorDocument 426 '                  	. ERROR_DOC_HOST . 'Error/426';
            self::$objHT[] = 'ErrorDocument 500 '                  	. ERROR_DOC_HOST . 'Error/500';
            self::$objHT[] = 'ErrorDocument 501 '                  	. ERROR_DOC_HOST . 'Error/501';
            self::$objHT[] = 'ErrorDocument 502 '                  	. ERROR_DOC_HOST . 'Error/502';
            self::$objHT[] = 'ErrorDocument 503 '                  	. ERROR_DOC_HOST . 'Error/503';
            self::$objHT[] = 'ErrorDocument 504 '                  	. ERROR_DOC_HOST . 'Error/504';
            self::$objHT[] = 'ErrorDocument 505 '                  	. ERROR_DOC_HOST . 'Error/505';
            self::$objHT[] = 'ErrorDocument 506 '                  	. ERROR_DOC_HOST . 'Error/506';
            self::$objHT[] = 'ErrorDocument 507 '                  	. ERROR_DOC_HOST . 'Error/507';
            self::$objHT[] = 'ErrorDocument 510 '                  	. ERROR_DOC_HOST . 'Error/510';

            // Add CSS/JS PHP interpretation;
            if (PHPIZE_JSS_FILES == TRUE) { self::$objHT[] = 'AddHandler application/x-httpd-php .js';  }
            if (PHPIZE_CSS_FILES == TRUE) { self::$objHT[] = 'AddHandler application/x-httpd-php .css'; }

            // Check the mod_rewrite is ON;
            if (in_array ('mod_rewrite', apache_get_modules ())) {
                // What should we do with the REWRITE_ENGINE
                REWRITE_ENGINE == TRUE                              ?
                self::rewriteHTLine (new S ('RewriteEngine On'))    :
                self::rewriteHTLine (new S ('RewriteEngine Off'));

                // ONLY, if the rewrite is OK;
                if (REWRITE_ENGINE == TRUE) {
                    // Protect US from BOTS;
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^BlackWidow [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Bot\ mailto:craftbot@yahoo.com [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^ChinaClaw [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Custo [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^DISCo [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Download\ Demon [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^eCatch [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EirGrabber [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EmailSiphon [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EmailWolf [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Express\ WebPictures [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^ExtractorPro [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EyeNetIE [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebGo\ IS [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebFetch [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebCopier [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebAuto [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Web\ Sucker [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Web\ Image\ Collector [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^VoidEYE [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Teleport\ Pro [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^tAkeOut [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Surfbot [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SuperHTTP [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SuperBot [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SmartDownload [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SiteSnagger [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^ReGet [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^RealDownload [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^pcBrowser [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^pavuk [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Papa\ Foto [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^PageGrabber [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Offline\ Navigator [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Offline\ Explorer [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Octopus [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NetZIP [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Net\ Vampire [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NetSpider [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NetAnts [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NearSite [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Navroad [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Mister\ PiX [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^MIDown\ tool [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Mass\ Downloader [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^LeechFTP [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^larbin [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^JOC\ Web\ Spider [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^JetCar [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Internet\ Ninja [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^InterGET [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} Indy\ Library [NC,OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Image\ Sucker [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Image\ Stripper [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} HTTrack [NC,OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^HMView [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Grafula [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^GrabNet [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Go-Ahead-Got-It [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^GetWeb! [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Go!Zilla [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^GetRight [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^FlashGet [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebLeacher [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebSauger [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Website\ eXtractor [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Website\ Quester [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebStripper [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebWhacker [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebZIP [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Wget [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Widow [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WWWOFFLE [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Xaldon\ WebSpider [OR]'));
                    self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Zeus'));
                    self::rewriteHTLine (new S ('RewriteRule ^.* - [F,L]'));

                    // Make'em SEO friendly, the mod_rewrite way (non-PHP);
                    self::rewriteHTLine (new S ('RewriteCond %{REQUEST_FILENAME} !-f'));
                    self::rewriteHTLine (new S ('RewriteCond %{REQUEST_FILENAME} !-d'));
                    self::rewriteHTLine (new S ('RewriteRule ^((.*)/(.*))$ index.php/$1 [L]'));
                }
            }
        } else {
            // CHECK: We can write the .htaccess file;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_WRITE_HTACCESS_FILE),
            new S (CANNOT_WRITE_HTACCESS_FILE_FIX));
        }

        // CHECK: The CCH directory is writeable ...
        if ((!is_writeable (DOCUMENT_ROOT . CACHE_DIR))) {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_WRITE_CACHE_DIR),
            new S (CANNOT_WRITE_FIX_PERMISSIONS));
        }

        // CHECK: The LOG directory is writeable ...
        if ((!is_writeable (DOCUMENT_ROOT . LOG_DIR))) {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_WRITE_LOG_DIR),
            new S (CANNOT_WRITE_FIX_PERMISSIONS));
        }

        // CHECK: The UPD directory is writeable ...
        if ((!is_writeable (DOCUMENT_ROOT . UPLOAD_DIR))) {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_WRITE_UPLOAD_DIR),
            new S (CANNOT_WRITE_FIX_PERMISSIONS));
        } else {
            if ((!is_writeable (DOCUMENT_ROOT . UPLOAD_DIR . _S . TEMP_DIR))) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CANNOT_WRITE_TEMP_DIR),
                new S (CANNOT_WRITE_FIX_PERMISSIONS));
            }
        }

        // Do return ...
        return new B (TRUE);
    }
}
?>
