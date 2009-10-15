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

############# Motto: 'If Microsoft ever does modules for me, it means I've won! (a variation on Linus Torvalds)';
/**
 * Concrete CLASS providing modules management, loading and instantiation. This CLASS will auto-scan the MOD_DIR directory,
 * and provide a set of rules (a sub-framework) of modules. Modules are conceptual groups of small functionalities that make up the
 * big picture;
 *
 * @package RA-Mod-Loading
 * @category RA-Concrete-Core
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class MOD extends CNF implements IFaceMOD {
    /**
     * @staticvar $objRegisteredModules An array containing all registered framework modules;
     * @var $objConfigArrayArray An array containing ALL registered configuration parameters of registered modules;
     */
	protected static $objName                  	= 'MOD :: RA PHP Framework';
	protected $objIdentificationString  	    = __CLASS__;
	protected $objConfigArrayArray              = NULL;
	protected static $objRegisteredModules      = NULL;
	protected static $objRegisteredClasses      = NULL;

	// CONSTRUCT;
	public function __construct () {
		parent::__construct ();
		// Set the module configuration file, based on the CLASS name, adding a .cfg;
		$this->setModuleConfigFile ($this->getObjectCLASS ()->appendString ('.cfg'));
		if (!TheFactoryMethodOfSingleton::checkHasInstance (__CLASS__)) {
			self::setExeTime (new S (__CLASS__));
			if ($this->objIdentificationString == __CLASS__) {
			    // Instantiate some types;
			    self::$objRegisteredModules = new A;
			    self::$objRegisteredClasses = new A;
			    // Scan for module directory, in the MOD_DIR dir;
			    if (!isset ($_SESSION[PROJECT_NAME]['RA_mod_reg'])) {
			        $scannedModuleDirectory = new FileDirectory (MOD_DIR);
                    $_SESSION[PROJECT_NAME]['RA_mod_reg']['scandir'] =
                    $scannedModuleDirectory->scanDirectory ($_SESSION[PROJECT_NAME]['RA_mod_reg']['f_count']);
			    }
			    // Do the FOR;
				for ($i = 0; $i < $_SESSION[PROJECT_NAME]['RA_mod_reg']['f_count']; ++$i) {
				    // Register new module;
					$this->registerModule (new FileDirectory (MOD_DIR . _S .
					$_SESSION[PROJECT_NAME]['RA_mod_reg']['scandir'][$i]));
				}
			}
		} else {
			// Return the instantiated object;
			return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
		}
	}

	/**
	 * Will activate the requested module, giving the relative path. This method will activate the module (requiring it once) and if
	 * the second argument is specified will return the object instance to be saved as a variable and used further;
	 *
	 * @param S $modNameToActivate The name of the module to activate
	 * @param B $modGetCLASS Should this method return the CLASS of the requested module, or a boolean value
	 * @return M Depending on the second parameter, can return boolean or an object
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 15_MOD.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
	 */
	public static final function activateModule (FilePath $modNameToActivate, B $modGetCLASS = NULL) {
	    // Set some requirements;
		self::requireLanguage ($modNameToActivate);
		self::requireIncludes ($modNameToActivate);
		// Do a switch ...
		switch ($modGetCLASS == TRUE) {
			case TRUE:
				// Return NEW CLASS;
				if (isset (self::$objRegisteredClasses[$modNameToActivate])) {
				    return self::$objRegisteredClasses[$modNameToActivate];
				} else {
				    self::$objRegisteredClasses[$modNameToActivate] = new self::$objRegisteredModules[$modNameToActivate]['obj'];
				    return self::$objRegisteredClasses[$modNameToActivate];
				}
			break;
			case FALSE:
				// Return TRUE, we've loaded;
				return new B (TRUE);
			break;
			default:
				// Return FALSE, we've had a problem;
				return new B (FALSE);
			break;
		}
    }

    /**
     * Will check if a module was registered with the framework. Given a string (to the path) of the module, it will check to see
	 * if that module was defined or not;
     *
     * @param FilePath $modNameToCheck The path of the module to check for
     * @return B Will return true if the module was registered with us
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 15_MOD.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function checkModuleIsRegistered (S $modNameToCheck) {
    	if (isset (self::$objRegisteredModules[$modNameToCheck->prependString (DOCUMENT_ROOT)])) {
    		// Do return ...
    		return new B (TRUE);
    	} else {
    		// Do return ...
    		return new B (FALSE);
    	}
    }

    /**
     * Will register the passed module inside our internal module array. This method, used by core developers will register the
	 * module inside our array if the module is set as activated;
     *
     * @param S $moduleDirectoryOrName The path to the module to register
     * @return void Doesn't need to return anything
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 15_MOD.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 * @final
     */
	private final function registerModule (FilePath $moduleDirectoryOrName) {
		$moduleName = $moduleDirectoryOrName->toString (TRUE)->doToken (DOCUMENT_ROOT, _NONE);
		$modConfigArray = $this->getModuleConfigurationSection ($moduleDirectoryOrName, new S ('EXTERNAL'));
		if (isset ($modConfigArray['mod_is_active']) && $modConfigArray['mod_is_active'] == 1) {
			self::$objRegisteredModules[$moduleName->toAbsolutePath ()]['dir'] = $moduleDirectoryOrName;
			self::$objRegisteredModules[$moduleName->toAbsolutePath ()]['obj'] = $modConfigArray['mod_use_class'];
		}
	}

	/**
	 * Will load the module language. This method will require_once every LANGUAGE definition of a module so as to have a simple
	 * way of pre-loading the necessary CONSTANTS for our modules;
	 *
	 * @param S $modNameOrDir The name of the registered module
	 * @return void Doesn't need to return anything
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 15_MOD.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 */
	protected static function requireLanguage (FilePath $modNameOrDir) {
		// At first, CLEAR the LANGUAGE cache ...
		if (self::getSessionVar (new S ('language')) != LANGUAGE) {
			// Make it the NEW, and empty the old;
			unset ($_SESSION[PROJECT_NAME]['RA_mod_lng'][$modNameOrDir->toString ()]);
			self::setSessionVar (new S ('language'), new O (LANGUAGE));
		}

        // Include ALL files in LANGUAGE_DIR, so we can speak some english;
        $scannedModuleDirectory = new FileDirectory (self::$objRegisteredModules[$modNameOrDir]['dir'] . _S. LANGUAGE_DIR . _S .
        self::getSessionVar (new S ('language')));
        if (!(isset ($_SESSION[PROJECT_NAME]['RA_mod_lng'][$modNameOrDir->toString ()]))) {
            // We need to order LANG_FILES, just for the sake of humanity;
            $_SESSION[PROJECT_NAME]['RA_mod_lng'][$modNameOrDir->toString ()]['scandir'] = $scannedModuleDirectory
            ->scanDirectory ($_SESSION[PROJECT_NAME]['RA_mod_lng'][$modNameOrDir->toString ()]['f_count']);
        }
        // Do the FOR;
        foreach ($_SESSION[PROJECT_NAME]['RA_mod_lng'][$modNameOrDir->toString ()]['scandir'] as $k => $v) {
            // Check the extension, to be PHP (cause we store other crap in LANG) ...
            if (_A (pathinfo ($v))->offsetGet ('extension') == 'php') {
                // Leave out .directories and .files;
                require_once $scannedModuleDirectory . _S . $v;
            }
        }
	}

	/**
	 * Will load the module requested includes. This will invoke the module CONTROLLER, after first processing the MODEL (schema) and
	 * proper initial configuration options;
	 *
	 * @param S $modNameOrDir The name of the registered module
	 * @return void Doesn't need to return anything
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 15_MOD.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
	 */
	private static function requireIncludes (FilePath $modNameOrDir) {
	    $scannedModuleDirectory = new FileDirectory (self::$objRegisteredModules[$modNameOrDir]['dir'] . _S . INCLUDE_DIR);
	    if (!(isset ($_SESSION[PROJECT_NAME]['RA_mod_inc'][$modNameOrDir->toString ()]))) {
            // Include ALL files in INCLUDE_DIR, so we can have some features working;
            $_SESSION[PROJECT_NAME]['RA_mod_inc'][$modNameOrDir->toString ()]['scandir'] =
            $includeFiles = $scannedModuleDirectory->scanDirectory ($_SESSION[PROJECT_NAME]
            ['RA_mod_inc'][$modNameOrDir->toString ()]['f_count']);
	    }
	    // Do the FOR;
        for ($i = 0; $i < $_SESSION[PROJECT_NAME]['RA_mod_inc'][$modNameOrDir->toString ()]['f_count']; ++$i) {
            require_once $scannedModuleDirectory . _S . $_SESSION[PROJECT_NAME]['RA_mod_inc']
            [$modNameOrDir->toString ()]['scandir'][$i];
        }
	}

	/**
	 * Will retrieve the array of registered modules. This is used for example for modules that need to know at run-time what modules
	 * have been registered with the framework before the try to provide an uniform way to access they're functionality;
	 *
	 * @return A The array of registered modules
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 15_MOD.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 */
	protected function & getRegisteredModules () {
	    // Do return ...
	    return self::$objRegisteredModules;
	}
}
?>
