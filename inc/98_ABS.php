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

############# Motto: 'The more abstract the truth you wish to teach, the more you must allure the senses to it!';
    /**
     * Abstract CLASS, that's used in ALL modules, parsing configuration data and keeping track of common configuration data;
     *
     * The base of our modules, this class will try to have common information stored in one place, and made available for every
     * module that's built on it. It helps keeping track of changes to common code just in one place, while different, specific
     * actions are taken for each module;
     */
abstract class ICommonExtension extends MOD implements IFaceCommonConfigExtension {
    // Set them protect;
    protected $objCookie                        = NULL;
    protected $objPathToModule                  = NULL;
    protected $objPathToSkin                    = NULL;
    protected $objPathToLang                    = NULL;
    protected $objPathToAdmin                   = NULL;
	protected $objDataArray						= NULL;

	// Set the statics;
    protected static $objAdministration         = NULL;
    protected static $objAuthenticationMech     = NULL;
    protected static $objFrontend               = NULL;

    /**
     * Will tie-in ALL common configuration data for ALL modules present in the framework;
     *
     * This method will take great care to parse any configuration data for any modules registered with the framework. This will
     * allow us to have an already existing working environment when we declare a CLASS that's part of a module. For the moment,
     * only base modules are supported, meaning that we can't have module descendant classes for the moment, until we can introduce
     * a mechanism that will tell us if the current instantiated class is a child or parent ...
     *
     * @return void Doesn't need to return anything;
     */
    public function tieInCommonConfiguration () {
        // Get a Cookie/Session storage object; By default,
        // every object should have it's own cookie;
        $this->objCookie = new CookieStorage ($this);

        // Get the SKIN information;
        if (self::checkSessionVar (new S ('skin'), new O (SKIN))->toBoolean () == FALSE)
        $objSkin = self::getSessionVar (new S ('skin')); else $objSkin = new S (SKIN);

        // Get the LANG information;
        if (self::checkSessionVar (new S ('language'), new O (LANGUAGE))->toBoolean () == FALSE)
        $objLang = self::getSessionVar (new S ('language')); else $objLang = new S (LANGUAGE);

        // Set some predefined defaults ...
		$this->objDataArray	   = new A;
        $this->objPathToModule = new FilePath (MOD_DIR . _S . $this->getObjectCLASS ()->toLower ());
        $this->objPathToAdmin  = new FilePath ($this->objPathToModule->toRelativePath () . _S . ADMIN_DIR . _S);
        $this->objPathToSkin   = new FilePath ($this->objPathToModule->toRelativePath () . _S . SKIN_DIR_DIR . _S . $objSkin . _S);
        $this->objPathToLang   = new FilePath ($this->objPathToModule->toRelativePath () . _S . LANGUAGE_DIR . _S . $objLang . _S);
    }

    /**
     * Will tie in with the Authentication mechanism, if we have one;
     *
     * This method will take care of making the bonds necessary with the authentication mechanism. We do this to allow loose-coupe
     * of framework modules, but this loose-coupling comes with a price, meaning that great care must be taken to provide the
     * least amount of compatibility between different modules;
     *
     * @param IFaceAuthentication $objAuthenticationMech The authentication mechanism;
     * @return void Doesn't need to return anything;
     */
    public function tieInWithAuthentication (IFaceAuthentication $objAuthenticationMech) {
        // Make the tie with authentication;
        self::$objAuthenticationMech = $objAuthenticationMech;

        // Set the proper ZONE;
        if (self::$objAuthenticationMech->checkZoneByName ($this->getObjectCLASS ())->toBoolean () == FALSE)
        self::$objAuthenticationMech->doMakeZone ($this->getObjectCLASS ());
        if (self::$objAuthenticationMech->checkAdministratorIsMappedToZone ($this->getObjectCLASS ())->toBoolean () == FALSE)
        self::$objAuthenticationMech->doMapAdministratorToZone ($this->getObjectCLASS ());
    }

    /**
     * Will tie in with the Administration mechanism, if we have one;
     *
     * This method will take care of making the bonds necessary with the administration mechanism. This allows us to have a
     * self-sustaining administrator interface, meaning that menus will be added or not shown, depending on user access to those
     * areas (taking in account the availability of the ACL system from the Authentication mechanism). These two method depend
     * deeply on one another, because of the centric ideea that our application has a backend and a frontend mechanism, which differ
     * both in design and just a little, in functionality;
     *
     * @param IFaceAdministration $objAdministrationMech The administration mechanism;
     * @return void Doesn't need to return anything;
     */
    public function tieInWithAdministration (IFaceAdministration $objAdministrationMech) {
        // Make the tie;
        self::$objAdministration = $objAdministrationMech;

        // Do something specific, LIKE getting the specific default .css file for a module;
        if (isset ($_GET[ADMIN_PAGE])) {
        	if ($_GET[ADMIN_PAGE] == $this->getObjectCLASS ($this)) {
        		// Append a default CSS file, must be present in ALL modules no matter what;
		        $this->manageCSS (new FilePath ($this->getPathToSkinCSS ()
		        ->toRelativePath () . 'default.css'), $this->getObjectCLASS ($this));
        	}
        }
    }

    /**
     * Will tie the current object with the frontend object;
     *
     * This method, given the Frontend object will tie the given object with it. It's a way we can make specific code execute
     * upon instantiating those objects in the frontend of the webiste. Almost the same principle we apply on the backend, but
     * with a minor twist in it (as we don't automatically do it for the front);
     *
     * @param $objFrontendObject The frontend object;
     * @return mixed God knows what ...
     */
    public function tieInWithFrontend (Frontend $objFrontendObject) {
        self::$objFrontend = $objFrontendObject;
    }

    /**
     * Will do a __CALL to some one-liner methods;
     *
     * We thought, that for the moment performance is not an issue, and we made it so that these virtual methods are called
     * using the PHP __call method, just to save some space, and to have a clearer code beyond this scope;
     *
     * @param string $objFunctionName The name of the called function;
     * @param array $objFunctionArgs The function arguments;
     * @return mixed Can return anything;
     */
    public function __CALL ($objFunctionName, $objFunctionArgs) {
        switch ($objFunctionName) {
            case 'getPathToModule':
                // Do return ...
                return $this->objPathToModule;
                break;

            case 'getPathToAdmin':
                // Do return ...
                return $this->objPathToAdmin;
                break;

            case 'getPathToSkin':
                // Do return ...
                return $this->objPathToSkin;
                break;

            case 'getPathToLanguage':
                // Do return ...
                return $this->objPathToLang;
                break;

            case 'getPathToSkinCSS':
                // Do return ...
                return new FilePath ($this->objPathToSkin
                ->toRelativePath () . SKIN_CSS_DIR . _S);
                break;

            case 'getPathToSkinJSS':
                // Do return ...
                return new FilePath ($this->objPathToSkin
                ->toRelativePath () . SKIN_JSS_DIR . _S);
                break;

            case 'getPathToSkinIMG':
                // Do return ...
                return new FilePath ($this->objPathToSkin
                ->toRelativePath () . SKIN_IMG_DIR . _S);
                break;

            case 'getHELP':
                // Set some requirements ...
                $objFileContent = new FileContent ($this->getPathToLanguage ()
                ->toRelativePath () . $objFunctionArgs[0] . HLP_EXTENSION);

                // Do return ...
                return new S ($objFileContent->toString ());
                break;
        }
    }

	/**
	 * Will set a virtual object property;
	 *
	 * This method is set so that dynamic of any project we have we can set object properties that can be shared and requested
	 * between multiple methods. It's an easier way than passing variables around;
	 *
	 * @param mixed $objKey The key to set;
	 * @param mixed $objVar The var to set at the key;
	 */
	public function __SET ($objKey, $objVar) {
		$this->objDataArray[$objKey] = $objVar;
	}

	/**
	 * Will retrieve a virtual object property if defined;
	 *
	 * This method independent of any project we have will retrieve a virtual object property if it was set. It gives us the unique
	 * posibillity to pass data around without the need of complicated methods;
	 *
	 * @param mixed $objKey The key to request;
	 * @return mixed Depends on what was set at first;
	 */
	public function __GET ($objKey) {
		if (isset ($this->objDataArray[$objKey])) {
			// Do return ...
			return $this->objDataArray[$objKey];
		} else {
		    if (DEBUG_UNDEFINED_MODULES == 0) {
		        // Return the BLANK ...
		        return new Nothing;
		    } else {
		        // Throws me ...
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (PROPERTY_NOT_SET),
                new S (PROPERTY_NOT_SET_FIX));
		    }
		}
	}
}
?>
