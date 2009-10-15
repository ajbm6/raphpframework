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

############# Motto: 'Captain Kirk's Log: "Risk is our business" ...';
/**
 * Abstract class that implements the core mechanism for logging either using the PHP error_log mechanism or relying on our own. Also,
 * provides a method of inclusion for EXTERNAL framework plugins, outside the core error handling mechanism;
 *
 * @package RA-Logging-Mechanism-And-Plugins
 * @category RA-Abstract-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class LOG extends EXE {
    /**
     * @staticvar $objGeshiSyPath The path to the GeSHi plugin directory, used in RA PHP Framework to highlight PHP code;
     * @staticvar $objKrumoObjectDumper Container for the KRUMO plugin, we need for error screens;
     * @staticvar $objPHPUserAgentSniffer Container for the user agent sniffer, need to determine browser type;
    */
    protected static $objName                   = 'LOG :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    protected static $objGeshiSyPath            = NULL;
    protected static $objKrumoObjectDumper      = NULL;
    protected static $objPHPUserAgentSniffer    = NULL;

    /**
     * Sets the FilePath to the GeSHi plugin directory, used to highlighy PHP code parsed from files. The GeSHi syntax highlighter
	 * is a framework plugin we use to automatically highlight code that has been caught in the context of our error screen, using
	 * the debug_backtrage function from PHP which will return an array of FILE/LINEs where the error happened. The path to this
	 * plugin is set with this method, from inside the 'ERR' object where the error mechanism is initialized;
     *
     * @param FilePath $pathToGeshySy Set the path to the geshy plugin
     * @return B Will return true if the path was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_LOG.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setRAGeshiSyPath (FilePath $pathToGeshiSy) {
        // Just set the INTERNAL objGeshiSyPath ...
        if (self::$objGeshiSyPath = $pathToGeshiSy) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Sets the FilePath to the phpAgentSniffer plugin we use with RA to detect what kind of browser and what features are activated
	 * for that browser so we can determine a couple of things like: what CSS to feed, what features to enable if we can have JS or
	 * must we relly on degradeable code. To determine such things we use this external PLUGIN to detect such properties;
     *
     * @param FilePath $pathToUserAgentSniffer Set the path to the user agent sniffer plugin
     * @return B Will return true if the path was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_LOG.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setRAUserAgentSniffer (FilePath $pathToUserAgentSniffer) {
    	// Just set the INTERNAL objPHPUserAgentSniffer ...
        if (self::$objPHPUserAgentSniffer = $pathToUserAgentSniffer) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set the path to the KRUMO variable dumper. The KRUMO is an external plugin with it's own JS, that we use to dump system
	 * variables globally defined in a recursive manner, which allows a developer the content of variables when the error happened. In
	 * the development stage of a project, this advantage helps the developer debug faster and after fixing the problem, to concentrate
	 * more on the features he has to do, rather than on what to use and how to debug ...
     *
     * @param FilePath $pathToKrumo Set the path to the KRUMO plugin
     * @return B Will return true if the path was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_LOG.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setRAKrumoPath (FilePath $pathToKrumo) {
    	// Just set the INTERNAL objKrumoObjectDumbper;
    	if (self::$objKrumoObjectDumper = $pathToKrumo) {
            // Do return ...
    	    return new B (TRUE);
    	} else {
            // Do return ...
    	    return new B (FALSE);
    	}
    }

    /**
     * Loads all plugins that have been set by their respective methods. You need not to forget that setting the FilePath to an external
	 * plugin is not enough. You need to actually put a require_once in this method, for that plugin to get automagically autoloaded
	 * along with the rest of the framework. If you don't do that here, then you can expect to wonder around for hours asking yourself
	 * why your plugin isn't loaded.
     *
     * @return B Will return true if everything is OK
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_LOG.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function requireRAPHPFrameworkLOWLEVELPlugins () {
        // Get PLUGIN: GeSHi;
        if ((GESHI == TRUE) && (DEBUG >= 1) && (self::$objGeshiSyPath != NULL))
		require_once self::$objGeshiSyPath->toString ();

        // Require the PHPUserAgentSniffer;
        require_once self::$objPHPUserAgentSniffer->toString ();
        // Require the KrumoObjectPath;
        require_once self::$objKrumoObjectDumper->toString ();

        // Do return ...
        return new B (TRUE);
    }

    /**
     * Used as a wrapper for the PHP error_log function. This is a fallback function if our own logging fails. We usually have our own
	 * method (named: setLog) which logs everything we need about an error in the LOG_DIR. From time to time, depending on the context
	 * of the server hosting the project, or permissions that change without warning due to annoying admins, that function may fail. For
	 * such cases and others, we defined the 'phpLog' method, accepting a S only, used to call the PHP specific 'error_log' function;
	 * <code>
	 * <?php
	 * 		// Do an error_log in LOG_DIR/ERROR_LOG_FROM_PHP
	 *		self::phpLog (new S ('If we got here and logged, then something's not good!');
	 * ?>
	 * </code>
     *
     * @param S $stringToLog The string we want to log, for debugging purposes
     * @return B Returns true if it was able to write the file
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_LOG.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function phpLog (S $stringToLog) {
        // Do return ... [and LOG];
        return new B (error_log ($stringToLog));
    }

    /**
     * Writes the current __CLASS__ log. It determines the __CLASS__ in which it's used, and writes a LOG. In certain circumstances
	 * this function may have a problem and fail. Usually, if that doesn't happen, the logged string is written in a path given by
	 * the LOG_DIR and the second parameter (the $stringFile) - mainly the constant of the __CLASS__ the error happened in;
	 * <code>
	 * <?php
	 *		// Set a __CLASS__ logging file. Ex: do loggin to LOG_DIR/OBS
	 *		OBS::setLog (new S ('This is an error!'), new S (__CLASS__));
	 * ?>
	 * </code>
     *
     * @param S $stringLog The string to be logged
     * @param S $stringFile The name of the file we will write the log to
     * @return B Returns true if it was able to write the file
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_LOG.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setLog (S $stringLog, S $stringFile) {
        // Check if the LOG file exists;
        if (file_exists ($stringFile = DOCUMENT_ROOT . LOG_DIR . _S . $stringFile)) {
            $theLoggedFILEId = fopen ($stringFile, 'a');
            // Do LOG;
            fwrite ($theLoggedFILEId, date (DATE_STRING, time ())
            . _T_ . _T_ . __CLASS__
            . _T_ . _T_ . $stringLog . _N_);
            // Do return ...
            return new B (fclose ($theLoggedFILEId));
        } else {
            $theLoggedFILEId = fopen ($stringFile, 'w');
            // Do LOG;
            fwrite ($theLoggedFILEId, date (DATE_STRING, time ())
            . _T_ . _T_ . __CLASS__
            . _T_ . _T_ . $stringLog . _N_);
            // Do return ...
            return new B (fclose ($theLoggedFILEId));
        }
        // If executed and got here, return FALSE;
        return new B (FALSE);
    }
}
?>
