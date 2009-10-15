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

############# Motto: "With great power comes great tax reduction!";
class Administration extends ICommonExtension implements IFaceAdministration {
	/* OBJECT: Identity */
	protected static $objName                   = 'Administration :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

	/* OTHERs */
    protected static $objMenuArray              = NULL;
    protected static $objSubMArray              = NULL;
    protected static $objWidgetArray			= NULL;
    protected static $objLogOutLink             = NULL;
    protected static $objSwitcherLink			= NULL;
    protected static $objHeaderText             = NULL;
    protected static $objFooterText             = NULL;

    /**
     * Constructs the administration object, taking the authentication object as a parameter and storing it for further use;
     *
     * This method will construct the administration object, which automatically takes an authentication object as a parameter. Thus
     * we can have separate authentication mechanisms (besides the implemented MySQL authentication) that can be used with our
     * administration mechanism, as long as the proper parameters respect the IFaceAuthentication interface;
     *
     * @param IFaceAuthentication $objAuthMech The authentication object, passed as a parameter;
     * @return void Doesn't need to return anything (being a constructor);
     */
    public function __construct (IFaceAuthentication $objAuthMech) {
        // Construct any possible parent, parse the configuration meanwhile;
    	parent::__construct ();

    	// Set the execution time start;
    	self::setExeTime (new S ('administration_start'));
    	// Tie in common configuration data;
    	$this->tieInCommonConfiguration ();

    	// Tie in with the authentication mechanism;
        $this->tieInWithAuthenticationMechanism ($objAuthMech);

        // Set some requirements ...
        $objPathToSkinJSS = $this->getPathToSkinJSS ()->toRelativePath ();
        $objPathToSkinCSS = $this->getPathToSkinCSS ()->toRelativePath ();

        // Auto-LogOut the user if we detect the proper action ...
        if (isset ($_GET[ADMIN_INTERFACE_ACTION])) {
        	// Switch ...
        	switch ($_GET[ADMIN_INTERFACE_ACTION]) {
        		case ADMIN_LOG_OUT:
        			self::$objAuthenticationMech->doLogOut ();
        			URL::doCleanURLPath ();
        			break;

        		case ADMIN_SWITCH_THEME:
        			if ($this->objCookie->checkKey (new S ('admin_css'))->toBoolean () == TRUE) {
        				switch ($this->objCookie->getKey (new S ('admin_css'))) {
        					case 'default.css':
        						$this->objCookie->setKey (new S ('admin_css'),
        						new S ('default_inverted.css'), new B (TRUE));
        						break;

        					default:
        						$this->objCookie->setKey (new S ('admin_css'),
        						new S ('default.css'), new B (TRUE));
        						break;
        				}
                    } else {
                    	// Set the INVERTED, at first ...
                    	$this->objCookie->setKey (new S ('admin_css'),
                    	new S ('default_inverted.css'), new B (TRUE));
                    }

                    // Get out ...
                    $this->setHeaderKey (URL::rewriteURL (new A (Array
                    (ADMIN_INTERFACE_ACTION))), new S ('Location'));
                    break;
        	}
        }

        // Set the required JS dependencies ...
        TPL::manageCSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQUI.css'), new S ('jQUICSS'));
        TPL::manageCSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQContextMenu.css'), new S ('jQCM'));
        TPL::manageCSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQFancybox.css'), new S ('jQFancybox'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQ.js'), new S ('jQ'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQUI.js'), new S ('jQUI'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQClock.js'), new S ('jQClock'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQBind.js'), new S ('jQBind'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQUICheckbox.js'), new S ('jQUICR'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQFileStyle.js'),new S ('jQFStyle'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQEasing.js'), new S ('jQEasing'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQFancybox.js'), new S ('jQFancybox'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQToolTip.js'), new S ('jQTT'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQWidget.js'), new S ('jQWidget'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQContextMenu.js'), new S ('jQCM'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQMasked.js'), new S ('jQMasked'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQTypeFace.js'), new S ('jQTF'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQKabelTypeFace.js'), new S ('jQTK'));
        TPL::manageJSS (new FilePath ($objPathToSkinJSS . 'jQ' . _S . 'jQExe.js'), new S ('jQExe'));

        // Add the default CSS, either black or white ...
        if ($this->objCookie->checkKey (new S ('admin_css'))->toBoolean () == TRUE) {
        	// Set the proper CSS, acording to _SESSION;
        	TPL::manageCSS (new FilePath ($objPathToSkinCSS . $this
        	->objCookie->getKey (new S ('admin_css'))), new S (__CLASS__));
        } else {
        	// Set the default to BLACK;
        	TPL::manageCSS (new FilePath ($objPathToSkinCSS
        	. 'default.css'), new S (__CLASS__));
        }

        // Safari, Google Chrome and maybe others on WebKIT ...
        if ($this->getUserAgentProperty (new S ('browser')) == 'sf') {
            // Add'em fixes ...
            TPL::manageCSS (new FilePath ($objPathToSkinCSS .
            'default_fixed.css'), new S ('safari-css-fix'));
        }

        // Get the proper configuration options stored in the object;
        self::$objHeaderText = $this->getConfigKey (new S ('administration_header_text'));
        self::$objFooterText = $this->getConfigKey (new S ('administration_footer_text'));

        // Do some actions, based on user information;
    	if (self::$objAuthenticationMech->checkIfUserIsLoggedIn ()->toBoolean () == TRUE and
            self::$objAuthenticationMech->checkCurrentUserZoneACL (new S (__CLASS__))->toBoolean () == TRUE) {
            // Redirect to the dashboard page;
            if (!isset ($_GET[ADMIN_PAGE])) $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGE)),
            new A (Array (ADMIN_DASHBOARD))), new S ('Location'));

            // Do a CALL to ALL registered administrator interfaces;
            $this->tieALLRegisteredAdminInterfaces ();
	    } else {
	        // Echo an error, for our dear friend, the Internet Explorer (MSIE);
	        if ($this->getUserAgentProperty (new S ('browser')) == 'ie') {
	            self::renderScreenOfDeath (new S (__CLASS__),
	            new S (ADMIN_IE_NOT_ALLOWED),
	            new S (ADMIN_IE_NOT_ALLOWED_FIX));
	        } else {
                // Safari, Google Chrome and maybe others on WebKIT ...
                if ($this->getUserAgentProperty (new S ('browser')) == 'sf') {
                    // Add'em fixes ...
                    TPL::manageCSS (new FilePath ($objPathToSkinCSS .
                    'default_fixed_adm.css'), new S ('safari-fix'));
                }

    	        // Do the authentication screen;
    	        self::$objAuthenticationMech->renderForm (new S ('adminLoginScreen'));

    	        // Set some predefines ...
    	        self::$objMenuArray		= new A;
    	        self::$objSubMArray		= new A;
    	        self::$objLogOutLink	= new S;
    	        self::$objSwitcherLink	= new S;

    	        // After we know all the details, execute the viewer whit these parameters;
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'administration.tp');
                TPL::tpSet (self::$objHeaderText, new S ('objHeaderText'), $tpF);
                TPL::tpSet (self::$objFooterText, new S ('objFooterText'), $tpF);
                TPL::tpSet (self::$objMenuArray,  new S ('objMenuArray'), $tpF);
                TPL::tpSet (self::$objSubMArray,  new S ('objSubMArray'), $tpF);
                TPL::tpSet (self::$objLogOutLink, new S ('objLogOutLink'), $tpF);
                TPL::tpSet (self::$objSwitcherLink, new S ('objSwitcherLink'), $tpF);
                TPL::tpExe ($tpF);
	        }
	    }
    }

    /**
     * Will add the first dashboard, which is the dashboard of the administration module;
     *
     * This method, simple in it's contents, will just add the first menu link, that goes to the dashboard of the administration
     * module. Why? Well, because we really need to have such a dashboard, from where the administrator can see statistics, or
     * information, before he can go further;
     *
     * @param IFaceAdministration $objAdminMech Pass the administration object as a parameter;
     * @return void Doesn't need to return anything;
     */
    public function tieInWithAdministration (IFaceAdministration $objAdminMech) {
        // Do the administration menu;
        $objDD = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
		$this->getConfigKey (new S ('administration_file_dashboard')));
        $objAdminMech->setMenuLink (new S (ADMIN_DASHBOARD), $objDD,
		$this->getHELP (new S (ADMIN_WELCOME_PAGE)));

        $objWP = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
		$this->getConfigKey (new S ('administration_file_welcome_page')));
        $objAdminMech->setSubMLink (new S (ADMIN_WELCOME_PAGE), $objWP,
		$this->getHELP (new S (ADMIN_WELCOME_PAGE)));

        // Set the widgets;
        $this->setWidget ($this->getHELP (new S ('adminWelcomeThere'))
        ->doToken ('%u', self::$objAuthenticationMech
        ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFUName)));
    }

    /**
     * Will bind with the authentication mechanism;
     *
     * This method will take the authentication object as a parameter, as long as that object implements the IFaceAuthentication
     * interface. Thus we allow for different authentication schemes, without too much headache. Also, it will check that the
     * current administration zone exists in the database, because we need a proper mapping scheme;
     *
     * @param IFaceAuthentication $objAuthMech The authentication object, passed as a parameter;
     * @return void Doesn't return a thing. It will probably error if something goes wrong;
     */
    public function tieInWithAuthenticationMechanism (IFaceAuthentication $objAuthMech) {
        // Tie the authentication object with me;
        self::$objAuthenticationMech = $objAuthMech;
        // Get the administrative 'Log Out' <a href=";
        self::$objLogOutLink 	= URL::rewriteURL (new A (Array (ADMIN_INTERFACE_ACTION)), new A (Array (ADMIN_LOG_OUT)));
        self::$objSwitcherLink 	= URL::rewriteURL (new A (Array (ADMIN_INTERFACE_ACTION)), new A (Array (ADMIN_SWITCH_THEME)));

        // Set the proper ZONE;
        if (self::$objAuthenticationMech->checkZoneByName ($this->getObjectCLASS ())->toBoolean () == FALSE)
        self::$objAuthenticationMech->doMakeZone ($this->getObjectCLASS ());
        if (self::$objAuthenticationMech->checkAdministratorIsMappedToZone ($this->getObjectCLASS ())->toBoolean () == FALSE)
        self::$objAuthenticationMech->doMapAdministratorToZone ($this->getObjectCLASS ());
    }

    /**
     * Will set a main menu link, that's used to identify a big module;
     *
     * This method will set the menu name, and path to the dashboard file for that module. It's a quick way to generate the
     * administrator interface, from pre-existing installed modules without having a big headache; We will process this information
     * in the proper .tp file, but for the moment, we just store it; Calling this method with the same parameters over and over
     * again rewrites the same key. We don't do any checking, considering that people are smart enough to realize that;
     *
     * @param string $objMenuName The name of the menu link, which will be seen;
     * @param string $objPathToIncludedFile The path (relative) to the dashboard file that will be called;
     * @return void Will not return a thing, it will probably error if something goes wrong;
     */
    public function setMenuLink (S $objMenuName, FilePath $objPathToIncludedFile, S $objLinkString = NULL) {
        // Set some predefines ...
        if (self::$objMenuArray == NULL) self::$objMenuArray = new A;
        self::$objMenuArray[$objMenuName] = new A (Array ('path' => $objPathToIncludedFile,
        'text' => $objLinkString->entityEncode (ENT_QUOTES)));
    }

    /**
     * Will set a submenu link, to the last added menu;
     *
     * This method will find out the index of the menu array, and will add a submenu link to the last added menu key. Why? Well,
     * the idea is that the tieInWithAdministration method defined as an interface method will do the tieing on a per-module basis,
     * meaning that the last added menu key, will be the proper key for that current module. Tricky, no?
     *
     * @param string $objSubMenuName The submenu name of the link;
     * @param string $objPathToIncludedFile The path (relative) to the file that will be included when calling the submenu link;
     */
    public function setSubMLink (S $objSubMenuName, FilePath $objPathToIncludedFile, S $objLinkString = NULL) {
        // Set some predefines ...
        if (self::$objSubMArray == NULL) self::$objSubMArray = new A;
        if ($objLinkString == NULL) $objLinkString = $objSubMenuName;
        foreach (current (self::$objMenuArray) as $k => $v) $objCurrentIndex = $k;
        self::$objSubMArray[$objCurrentIndex][$objSubMenuName]['name'] = $objSubMenuName;
        self::$objSubMArray[$objCurrentIndex][$objSubMenuName]['path'] = $objPathToIncludedFile;
        self::$objSubMArray[$objCurrentIndex][$objSubMenuName]['text'] = $objLinkString->entityEncode (ENT_QUOTES);
    }

    /**
     * Will invoke the needed pagination code (present only in admin for easy maintainance);
     *
     * This method will invoke the necesarry code to display the pagination links. We have kept a separate copy of this file
     * everywhere, but now we're actually building this specific method just to clean the other module directories of useless,
     * redundant code, that we can compact here, at the cost of an extra function call ...
     *
     * @param I $objItemCount The count of TOTAL items ...
     */
    public function setPagination (I $objItemCount) {
        // Set the template file ...
        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'itemPagination.tp');
        TPL::tpSet ($objItemCount, new S ('objArticleTableCount'), $tpF);
        TPL::tpExe ($tpF);
    }

    /**
     * Will set the administration widget title and content;
     *
     * This method will set an widget to be shown on the 'Welcome' page of the administration page. We need to be able to show
     * some widgets with statistics on the front-page, for the pure information of the administrator of the website;
     *
     * @param S $objWidgetTitle The widget title;
     * @param S $objWidgetWText The widget text to be shown;
     */
    public function setWidget (S $objWidgetWText, B $objEVAL = NULL) {
        // Set some predefines ...
    	if (self::$objWidgetArray == NULL) self::$objWidgetArray = new A;
    	if ($objEVAL == NULL) $objEVAL = new B (FALSE);
    	foreach (current (self::$objMenuArray) as $k => $v) $objCurrentIndex = $k;
    	self::$objWidgetArray[] = new A (Array ('wtext' => $objWidgetWText, 'wEVAL' => $objEVAL));
    }

    /**
     * Will return the selected widget, or the whole array if no title is passed;
     *
     * This method will return an widget with it's title and content, or the whole array if nothing is passed. It can be used
     * when displaying the 'Welcome' page to retrieve all defined widgets 'till the time it was invoked;
     *
     * @param S $objWidgetTitle The widget title to retrieve;
     * @return array Will return an array containing the title and content of the widget;
     */
    public function getWidget (S $objWidgetTitle = NULL) {
    	// If NULL, return the array;
    	if (self::$objWidgetArray == NULL) return new A;
    	if ($objWidgetTitle == NULL) return self::$objWidgetArray;

    	// ELSE ...
    	foreach (self::$objWidgetArray as $k => $v) {
    		if ($v['title'] == $objWidgetTitle) {
    			return self::$objWidgetArray[$k];
    			break;
    		}
    	}
    }

    /**
     * Will trigger the default administration error message;
     *
     * This method will take the error message to show and the URL where to go back, and will display the now default error message
     * indicating the user that he doesn't have the access to do a specific action. We're trying to standardize as much as we can
     * from the redundant administration code we use in our administration pages;
     *
     * @param S $objErrorMessage The error message to show;
     * @param S $objURLToGoBack The URL to go back afterwards;
     */
    public function setErrorMessage (S $objErrorMessage, S $objURLToGoBack) {
        // Set the template file ...
        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'error_cannotDoAction.tp');
        TPL::tpSet ($objErrorMessage->entityEncode (ENT_QUOTES), new S ('actionErrorMessage'), $tpF);
        TPL::tpSet ($objURLToGoBack, new S ('objURLToGoBack'), $tpF);
        TPL::tpExe ($tpF);
    }

    /**
     * Will tie in ALL registered administrator interfaces in the framework;
     *
     * This method will do a scan of all modules defined in the framework, and WILL do a CALL to each bonding method of those
     * respective methods; For the Authentication and Administration module, proper code is implemented, because of the strict
     * bonding between these two modules;

     * @return void Will not return a thing, but probably error if something goes wrong;
     */
    private function tieALLRegisteredAdminInterfaces () {
        // Set some predefined defaults, just to be sure we have something;
        TPL::manageTTL (self::$objHeaderText);
        TPL::switchTTL ();

        // Add the current page, or subpage to the <title> ...
        TPL::manageTTL ((isset ($_GET[ADMIN_PAGE]) ? $_GET[ADMIN_PAGE] : new S));
        TPL::manageTTL ((isset ($_GET[ADMIN_SUBPAGE]) ? $_GET[ADMIN_SUBPAGE] : new S));

        // Do a for-each on ALL registered modules within' the framework;
        $objRegisteredModules = $this->getRegisteredModules ();
        foreach ($objRegisteredModules as $k => $v) {
            // IGNORE: Administration, because it's manually tied;
            if ($this->getPathToModule ()->toRelativePath () == $v['dir']->toString ()) {
                $this->tieInWithAdministration ($this);
                continue;
            }

            // IGNORE: Authentication, because it's manually tied;
            if ($this->getObjectCLASS (self::$objAuthenticationMech) == $v['obj']) {
                if (self::$objAuthenticationMech->checkCurrentUserZoneACL (
                $this->getObjectCLASS (self::$objAuthenticationMech))->toBoolean () == TRUE)
                self::$objAuthenticationMech->tieInWithAdministration ($this);
                continue;
            } else {
                // Make the object, and store it;
                $objMod = $this->activateModule (new FilePath ($v['dir']), new B (TRUE));
                $objMod->tieInWithAuthentication (self::$objAuthenticationMech);

                if (self::$objAuthenticationMech->checkCurrentUserZoneACL
                ($this->getObjectCLASS ($objMod))->toBoolean () == TRUE) {
                    // Do the tie;
                    $objMod->tieInWithAdministration ($this);
                }
            }
        }

        // Set some predefines;
        if (self::$objMenuArray == NULL) self::$objMenuArray = new A;
        if (self::$objSubMArray == NULL) self::$objSubMArray = new A;

        // Set the script end;
        self::setExeTime (new S ('administration_end'));

        // After we know all the details, execute the viewer whit these parameters;
        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'administration.tp');
        TPL::tpSet (new F (self::getExeTime (new S ('administration_start'),
        new S ('administration_end'))), new S ('objExeTime'), $tpF);
        TPL::tpSet (self::$objSwitcherLink, new S ('objSwitcherLink'), $tpF);
        TPL::tpSet (self::$objHeaderText, new S ('objHeaderText'), $tpF);
        TPL::tpSet (self::$objFooterText, new S ('objFooterText'), $tpF);
        TPL::tpSet (self::$objMenuArray,  new S ('objMenuArray'), $tpF);
        TPL::tpSet (self::$objSubMArray,  new S ('objSubMArray'), $tpF);
        TPL::tpSet (self::$objLogOutLink, new S ('objLogOutLink'), $tpF);
        TPL::tpExe ($tpF);
    }

    /**
     * Will render a specified form, the name of the form given by the first parameter;
     *
     * This method will render one of the forms for our object, invoked by giving the proper form identifier to the current form.
     * We have chosen this method of invoking forms, because we just had too many this->renderSomethingMethod (), which really had
     * an impact on code massiveness. Also, having code organized in switch/case statements leads us to be able to share common
     * settings between different forms, as we've done with the methods defined in the __CALL method above;
     *
     * For example, if we wanted to share some common configuration between a create and an edit form, we could have introduced
     * two switches in this method, one that would have set the common options, and the second, would have just passed through
     * again, and get the already set configuration options, using them. This means that if we needed to change behavior of
     * some interconnected forms, that would mean modifying the needed code one place only, which is a big advantage over
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality;
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderBackendPage (S $objPageToRender) {
        // Get a specific CSS file for this controller ...
        TPL::manageCSS (new FilePath ($this->getPathToSkinCSS ()
        ->toRelativePath () . $objPageToRender . CSS_EXTENSION), $objPageToRender);

        // Do pagination ...
        if (isset ($_GET[ADMIN_PAGINATION])) {
            $objLowerLimit = (int) $_GET[ADMIN_PAGINATION]->toString () * 10 - 10;
            $objUpperLimit = 10;
        } else {
            $objLowerLimit = 0;
            $objUpperLimit = 10;
        }

        // Do a switch on the rendered page ...
        switch ($objPageToRender) {

        }
    }
}
?>
