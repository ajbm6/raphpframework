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

############# Motto: 'There's great power in words, if you don't hitch too many togheter!';
/**
 * Concrete CLASS providing configuration parsing for our framework MODULES. This CLASS will parse php.ini LIKE files in the CFG_DIR
 * of our modules;
 *
 * @package RA-Mod-Configuration-Parsing
 * @category RA-Concrete-Core
 * @author Dumitru Alexandru <dumitru.alexandru@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class CNF extends FRM implements IFaceCNF {
    /**
     * @staticvar $objIdentificationString The object unique (__CLASS__) identification string, used to execute content only once.
     * @staticvar $objName An internal identifier to the object name, usually __CLASS__ :: RA PHP Framework;
     * @staticvar $objConfigModuleFile Name of the config file for ALL the modules. For the moment, we memorize it, platform-wide;
     * @staticvar $objConfigArray Configuration array for the current object.
     * @staticvar $objConfiguratArray Array from [INTERNAL] section of the current module!
    */
    protected static $objName                  	= 'CNF :: RA PHP Framework';
	protected $objIdentificationString  	    = __CLASS__;
	protected $objConfigurationFile             = NULL;
	protected $objConfigArray                   = NULL;

	// CONSTRUCT;
	public function __construct () {
	    // Make a parent::CALL;
		parent::__construct ();
	    $this->objConfigurationFile = new S;
	    $this->objConfigArray = new A;

	    // Auto-DB-Config:
	    $this->getQuery (new S ('CREATE TABLE IF NOT EXISTS `_T_configuration` (`k` varchar(255) NOT NULL, `v` longtext NOT NULL,
	    PRIMARY KEY  (`k`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;'));
	}

	/**
	 * Will set the name of the module configuration file. This method is used to set the default anme of the configuration file,
	 * which by default is the name of the __CONTROLLER__ appended with a .cfg extension;
     *
     * @param S $objModConfigFile The file to be processed for configuration parameters
     * @return void Will not return a thing
	 * @author Dumitru Alexandru <dumitru.alexandru@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 14_CNF.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @final
    */
	protected final function setModuleConfigFile (S $objModConfigFile) {
	    if ($this->objConfigArray->doCount ()->toInt () == 0) {
	        $this->objConfigurationFile = $objModConfigFile;
	    }
	}

	/**
	 * Will return a module configuration section. This method will return a section (either [INTERNAL] or [EXTERNAL]) of the
	 * configuration file of our module. We use such a schema to privide two types of configurations;
     *
     * @param S $moduleDirectoryOrName The path to the file to be processed
     * @param S $whatSectionToGet The name of the section to retrieve
     * @return A The section processed as an array
	 * @author Dumitru Alexandru <dumitru.alexandru@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 14_CNF.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Versino 1.0
	 * @access protected
	 * @final
    */
	protected final function getModuleConfigurationSection (FilePath $moduleDirectoryOrName, S $whatSectionToGet) {
	    if (isset ($this->objConfigArray[$moduleDirectoryOrName])) {
	        // Do return ...
	        return $this->objConfigArray[$moduleDirectoryOrName][$whatSectionToGet];
	    } else {
	        // Get the configuration;
	        $this->getModuleConfiguration ($moduleDirectoryOrName);

            // Do return ...
	        return $this->objConfigArray[$moduleDirectoryOrName][$whatSectionToGet];
	    }
	}

	/**
	 * Will read-out the whole module configuration file. This method will get and parse the configuration file of our module by using
	 * the PHP specific parse_ini_file function defined by PHP;
     *
     * @param S $moduleDirectoryOrName Path to the file to be processed
     * @return A The file processed as an array
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 14_CNF.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @final
    */
	protected final function getModuleConfiguration (FilePath $moduleDirectoryOrName) {
	    if (isset ($this->objConfigArray[$moduleDirectoryOrName])) {
	        // Do return ...
	        return $this->objConfigArray[$moduleDirectoryOrName];
	    } else {
	        // Do return ...
	        return $this->objConfigArray[$moduleDirectoryOrName] = new A (parse_ini_file ($moduleDirectoryOrName
	        ->toAbsolutePath () . _S . CFG_DIR . _S . $this->objConfigurationFile, TRUE));
	    }
	}

	/**
	 * Return a configuration key from the [INTERNAL] section of the module configuration file. This method also queries the
	 * configuration table where the keys are stored. Each module must have an unique key or conflicts may appear. The data stored
	 * in the table takes precedence over the one stored in the .ini file;
     *
     * @param S $objConfigKey The key to get the configuration parameter for
     * @return S The content of the key, returned as a string
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 14_CNF.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
    */
    public function getConfigKey (S $objConfigKey) {
        if (isset ($this->objConfigArray[$objConfigKey])) {
            // Do return ...
            return new S ($this->objConfigArray[$objConfigKey]);
        } else {
            // Do the query (once and cache it!);
            $q = $this->getQuery (_QS ('doSELECT')
            ->doToken ('%what', new S ('*'))->doToken ('%table', new S ('_T_configuration'))
            ->doToken ('%condition', new S ('WHERE k = "%key"'))->doToken ('%key', $objConfigKey));
            if ($q->doCount ()->toInt () != 0) {
                // Return the configuration option from the database, store it also ...
                return $this->objConfigArray[$objConfigKey->toString ()] = $q->offsetGet (0)->offsetGet ('v');
            } else {
                // Parse the configuration file, get all configuration keys;
                $this->objConfigArray = self::getModuleConfigurationSection ($this->objPathToModule, new S ('INTERNAL'));

                // Set all configuration keys that have not been set;
                foreach ($this->objConfigArray as $k => $v) {
                    // At FIRST, check to see if it's defined ...
                    if ($this->getQuery (_QS ('doSELECT')
                    ->doToken ('%what', new S ('*'))->doToken ('%table', new S ('_T_configuration'))
                    ->doToken ('%condition', new S ('WHERE k = "%key"'))
                    ->doToken ('%key', $k))->doCount ()->toInt () == 0) {

                        // No key, do INSERT it first ...
                        $this->getQuery (_QS ('doINSERT')
                        ->doToken ('%table', new S ('_T_configuration'))
                        ->doToken ('%condition', new S ('k = "%key", v = "%var"'))
                        ->doToken ('%key', $k)->doToken ('%var', $v));
                    }
                }
                // Do return ...
                return new S ($this->objConfigArray[$objConfigKey]);
            }
        }
    }

    /**
     * Will set a configuration key, even if it was not set. This method will also update the key in the configuration table so that
	 * the next time the getConfigKey method is called, the value in the DBs will take precedence over the value in the config file;
     *
     * @param S $objConfigKey The key to be set, for dynamic configuration parameters
     * @return O The current object configuration string
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 14_CNF.php 314 2009-10-09 14:24:35Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
    */
    public function setConfigKey (S $objConfigKey, S $objConfigVar) {
        if ($this->objConfigArray->doCount ()->toInt () != 0) {
            // Set the INTERNAL array of configuration parameters;
            $this->objConfigArray[$objConfigKey] = $objConfigVar;

            // Do an update on the configuration table also ...
            $this->getQuery (_QS ('doUPDATE')
            ->doToken ('%table', new S ('_T_configuration'))
            ->doToken ('%condition', new S ('v = "%var" WHERE k = "%key"'))
            ->doToken ('%key', $objConfigKey)->doToken ('%var', $objConfigVar));
        } else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CONFIG_ARRAY_NOT_SET),
            new S (CONFIG_ARRAY_NOT_SET_FIX));
        }
    }
}
?>
