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

############# Motto: "Not everything is a coincidence ...";
class Settings extends ICommonExtension implements IFaceCommonConfigExtension {
    /* OBJECT: Identity */
    protected static $objName                   = 'Settings :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* TABLE: Countries */
    public static $objSettingsCountryTable      = NULL;
    public static $objSettingsCountryTableFIso  = NULL;
    public static $objSettingsCountryTableFName = NULL;
    public static $objSettingsCountryTableFPrnt = NULL;
    public static $objSettingsCountryTableFIsoT = NULL;
    public static $objSettingsCountryTableFCode = NULL;

    /* TABLE: Errors */
    public static $objSettingsErrTable          = NULL;
    public static $objSettingsErrTableFId       = NULL;
    public static $objSettingsErrTableFCode     = NULL;
    public static $objSettingsErrTableFTitle    = NULL;
    public static $objSettingsErrTableFContent  = NULL;

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent;
        parent::__construct ();
        // Do the tie ...
        $this->tieInCommonConfiguration ();

        // Set the proper configuration options, from the config file;
        self::$objSettingsCountryTable          = $this->getConfigKey (new S ('settings_country_table'));
        self::$objSettingsCountryTableFIso      = $this->getConfigKey (new S ('settings_country_table_field_iso'));
        self::$objSettingsCountryTableFName     = $this->getConfigKey (new S ('settings_country_table_field_name'));
        self::$objSettingsCountryTableFPrnt     = $this->getConfigKey (new S ('settings_country_table_field_printable_name'));
        self::$objSettingsCountryTableFIsoT     = $this->getConfigKey (new S ('settings_country_table_field_iso3'));
        self::$objSettingsCountryTableFCode     = $this->getConfigKey (new S ('settings_country_table_field_numcode'));

        // Errors ...
        self::$objSettingsErrTable              = $this->getConfigKey (new S ('settings_error_pages_table'));
        self::$objSettingsErrTableFId           = $this->getConfigKey (new S ('settings_error_pages_table_field_id'));
        self::$objSettingsErrTableFCode         = $this->getConfigKey (new S ('settings_error_pages_table_error_code'));
        self::$objSettingsErrTableFTitle        = $this->getConfigKey (new S ('settings_error_pages_table_title'));
        self::$objSettingsErrTableFContent      = $this->getConfigKey (new S ('settings_error_pages_table_content'));

        // DB: Auto-CREATE:
        $objQueryDB = new FileContent ($this->getPathToModule ()->toRelativePath () .
        _S . CFG_DIR . _S .  __CLASS__ . SCH_EXTENSION);

        // Make a FOREACH on each ...
        foreach (_S ($objQueryDB->toString ())
        ->fromStringToArray (RA_SCHEMA_HASH_TAG) as $k => $v) {
            // Make'em ...
            $this->_Q (_S ($v));
        }
    }

    /**
     * Will replace module tokens (also named table fields) that can be used independent of the table structure;
     *
     * This method, will replace a series of table field tokens, with their respective fields. This is to allow a better way to
     * write SQL conditions from the front-end of the module, that will interact perfectly with the back-end of the current module,
     * which should make the MVC pattern as pure as possible;
     *
     * @param S $objSQLParam The SQL string to be processed;
     * @return S Will return the current SQL string with modified tokens;
     */
    public function doModuleToken (S $objSQLParam) {
        // Set the tokens to be replaced;
        $objTokens      = new A;
        $objTokens[1]   = 'objSettingsCountryTable';
        $objTokens[2]   = 'objSettingsCountryTableFIso';
        $objTokens[3]   = 'objSettingsCountryTableFName';
        $objTokens[4]   = 'objSettingsCountryTableFPrnt';
        $objTokens[5]   = 'objSettingsCountryTableFIsoT';
        $objTokens[6]   = 'objSettingsCountryTableFCode';
        $objTokens[7]   = 'objSettingsErrTable';
        $objTokens[8]   = 'objSettingsErrTableFId';
        $objTokens[9]   = 'objSettingsErrTableFCode';
        $objTokens[10]  = 'objSettingsErrTableFTitle';
        $objTokens[11]  = 'objSettingsErrTableFContent';

        // Set the replacements;
        $objReplac      = new A;
        $objReplac[1]   = self::$objSettingsCountryTable;
        $objReplac[2]   = self::$objSettingsCountryTableFIso;
        $objReplac[3]   = self::$objSettingsCountryTableFName;
        $objReplac[4]   = self::$objSettingsCountryTableFPrnt;
        $objReplac[5]   = self::$objSettingsCountryTableFIsoT;
        $objReplac[6]   = self::$objSettingsCountryTableFCode;
        $objReplac[7]   = self::$objSettingsErrTable;
        $objReplac[8]   = self::$objSettingsErrTableFId;
        $objReplac[9]   = self::$objSettingsErrTableFCode;
        $objReplac[10]  = self::$objSettingsErrTableFTitle;
        $objReplac[11]  = self::$objSettingsErrTableFContent;


        // Do a CALL to the parent, make it tokenize;
        return parent::doModuleTokens ($objTokens, $objReplac, $objSQLParam);
    }

    /**
     * Will add the administration menu;
     *
     * This method will tie in the current module with the administration module, while adding the proper administrator links. The
     * files to be required by the administration module are set in the configuration file of this module.
     *
     * @param IFaceAdministration $objAdministrationMech The administration object;
     * @return void Doesn't need to return anything;
     */
    public function tieInWithAdministration (IFaceAdministration $objAdministrationMech) {
        // Do the tie ...
        parent::tieInWithAdministration ($objAdministrationMech);

        // Do the administration menu;
        $objWP = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
        $this->getConfigKey (new S ('settings_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (SETTINGS_ADMIN), $objWP,
        $this->getHELP (new S (SETTINGS_MANAGE_SETTINGS)));

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Settings.Do.Configuration');
        $objACL[] = new S ('Settings.CountryList.Do.View');
        $objACL[] = new S ('Settings.ErrorPages.Do.View');

        // ONLY: Settings.Do.Configuration
        if (self::$objAuthenticationMech->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMS = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('settings_file_manage_settings')));
            self::$objAdministration->setSubMLink (new S (SETTINGS_MANAGE_SETTINGS),
            $objMS, $this->getHELP (new S (SETTINGS_MANAGE_SETTINGS)));
        }

        // ONLY: Settings.CountryList.Do.View
        if (self::$objAuthenticationMech->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('settings_file_manage_countries')));
            self::$objAdministration->setSubMLink (new S (SETTINGS_MANAGE_COUNTRIES),
            $objMC, $this->getHELP (new S (SETTINGS_MANAGE_COUNTRIES)));
        }

        // ONLY: Settings.ErrorPages.Do.View
        if (self::$objAuthenticationMech->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('settings_file_manage_error_pages')));
            self::$objAdministration->setSubMLink (new S (SETTINGS_MANAGE_ERROR_PAGES),
            $objMC, $this->getHELP (new S (SETTINGS_MANAGE_ERROR_PAGES)));
        }
    }

    /**
     * Will tie in with the authentication mechanism;
     *
     * This method will tie in with the authentication mechanism, making necessary zones, and binding this module with the
     * authentication module making necessary linkes along the way. For most of the time it's used as a mapping tool for the
     * default administrator group and necessary zones;
     *
     * @param IFaceAuthentication $objAuthenticationMech The authentication mechanism;
     */
    public function tieInWithAuthentication (IFaceAuthentication $objAuthenticationMech) {
    	// Do a CALL to the parent ...
        parent::tieInWithAuthentication ($objAuthenticationMech);

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Settings.Do.Configuration');
        $objACL[] = new S ('Settings.CountryList.Do.View');
        $objACL[] = new S ('Settings.ErrorPages.Do.View');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if (self::$objAuthenticationMech->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            self::$objAuthenticationMech->doMakeZone ($objACL[$k], $this->getObjectCLASS ());
            if (self::$objAuthenticationMech->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            self::$objAuthenticationMech->doMapAdministratorToZone ($objACL[$k]);
        }
    }

    /**
     * Will check that the given error code is unique in the system;
     *
     * To avoid double errors, this method will check that the given error code is unique. We do this because conceptually, error
     * codes are unique. No code can be assigned to two errors at a time, and this is also the case here. Use this upon editing and
     * upon creating an error page ...
     *
     * @param S $objErrorPagecode The error page to check for;
     * @return biikeab Will return true if the error code is unique;
     */
    public function checkErrorPageCodeIsUnique (S $objErrorPagecode) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objSettingsErrTableFId'))->doToken ('%table', self::$objSettingsErrTable)
        ->doToken ('%condition', new S ('WHERE %objSettingsErrTableFCode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objErrorPagecode))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the count of all defined error pages;
     *
     * This method will return the count of all the error pages in the system that meet a certain criteria. For example, we could
     * sort error pages by their code, or by any other means, case in which we need a way to get the proper count for the proper
     * SQL condition;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer The count of error pages that meet the criteria;
     */
    public function getErrorPageCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objSettingsErrTableFId) AS count'))
        ->doToken ('%table', self::$objSettingsErrTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of countries that meet the SQL criteria;
     *
     * This method will return the count of countries that meet the SQL criteria. This is a good way to determine if we need
     * pagination support or not, for that current page. Also, it can be used for other purposes like generating reports ...
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer The count of countries that have met the criteria;
     */
    public function getCountryCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objSettingsCountryTableFIso) AS count'))
        ->doToken ('%table', self::$objSettingsCountryTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return an array of all defined error pages;
     *
     * This method, given an SQL condition will return all the error pages defined in the system. Error pages are used for example,
     * to show an information when the user has requested something that caused Apache (for example) to output an error. This way,
     * we can bettern inform the user better than what the default error messages do;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return array The result array;
     */
    public function getErrorPages (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objSettingsErrTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return an array containing all countries defined in the system that meet the SQL criteria;
     *
     * This method, given an SQL condition, will return an array cointaining all the countries that match that search criteria. In
     * short, that helps us in other modules like authentication or in democraphycal data,
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return array The array of countries that match the SQL criteria;
     */
    public function getCountries (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objSettingsCountryTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return information about an error by its id;
     *
     * This method, will return all info associated to an error id, thus for example, we can echo this information when needed,
     * when an error occurs and the needed info needs to be shown. Sometimes, it can happen that SOME errors don't get shown,
     * because the underlying PHP code can't execut
     *
     * @param S $objErrorId The error id to query for;
     * @param S $objFieldToGet The field to return;
     * @return mixed Depends on what was requested;
     */
    public function getErrorPageById (S $objErrorId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objSettingsErrTable)
        ->doToken ('%condition', new S ('WHERE %objSettingsErrTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objErrorId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about an error by its code;
     *
     * This method, will return all info associated to an error code, thus for example, we can echo this information when needed,
     * when an error occurs and the needed info needs to be shown. Sometimes, it can happen that SOME errors don't get shown,
     * because the underlying PHP code can't execute;
     *
     * @param S $objErrorCode The code to get information for;
     * @param S $objFieldToGet The field to return;
     * @return mixed Depends on what was requested;
     */
    public function getErrorPageByCode (S $objErrorCode, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objSettingsErrTable)
        ->doToken ('%condition', new S ('WHERE %objSettingsErrTableFCode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objErrorCode))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will __CALL the proper methods;
     *
     * This method will call the proper methods, that aren't defined as self-standing methods, but are defined as cases in a
     * switch statement in this method. We do this because some of the functions here, due to our style of programming, share
     * many repetitive configuration options, meaning that for an action, we only write the code once, and if we need to
     * modify something somewhere, than we know exactly where to find it;
     *
     * Another argument is that for the moment no inheritance is really needed. Besides that, we could simulate inheritance by
     * doing a default CALL to the parent method, which means that if there wasn't any case that matched the current object scope,
     * than the calling function will be passed up the parent. It's a nice way of having extremely organized code, while keeping
     * the advantages of an almost full OOP programming style, without loss in performance;
     */
    public function __CALL ($objFunctionName, $objFunctionArgs) {
        switch ($objFunctionName) {
            default:
                return parent::__CALL ($objFunctionName, $objFunctionArgs);
                break;
        }
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
            case 'manageConfiguration':
                if (isset ($_GET[ADMIN_ACTION])) { $this->renderForm ($_GET[ADMIN_ACTION]); }
                else { $this->renderForm (new S ('configurationEdit')); }
                break;

            case 'manageCountries':
                // Do some work;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('countryEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('countryErase'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByName':
                            case 'DescByName':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objSettingsCountryTableFName');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)
                    ->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objCountries = $this->getCountries ($objGetCondition);
                    $objCountriesCount = $this->getCountryCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageCountries.tp');
                    TPL::tpSet ($objCountries, new S ('articleTable'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objCountriesCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objCountriesCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('countryCreate'));
                }
                // Break out ...
                break;

            case 'manageErrorPages':
                // Do some work;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('errorPageEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('errorPageErase'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByErrorCode':
                            case 'DescByErrorCode':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objSettingsErrTableFCode');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByErrorCode':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByErrorCode':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByErrorPageTitle':
                            case 'DescByErrorPageTitle':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objSettingsErrTableFTitle');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByErrorPageTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByErrorPageTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)
                    ->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objCountries = $this->getErrorPages ($objGetCondition);
                    $objCountriesCount = $this->getErrorPageCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageErrorPages.tp');
                    TPL::tpSet ($objCountries, new S ('articleTable'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objCountriesCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objCountriesCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('errorPageCreate'));
                }
                // Break out ...
                break;
        }
    }

    /**
     * Will render a specified form, the name of the form given by the first parameter;
     *
     * This method will render one of the forms for our object, invoked by giving the proper form identifier to the current form. We
     * have chosen this method of invoking forms, because we just had too many this->renderSomethingMethod (), which really had
     * an impact on code massiveness. Also, having code organized in switch/case statements leads us to be able to share common
     * settings between different forms, as we've done with the methods defined in the __CALL method above;
     *
     * For example, if we wanted to share some common configuration between a create and an edit form, we could have introduced
     * two switches in this method, one that would have set the common options, and the second, would have just passed through
     * again, and get the already set configuration options, using them. This means that if we needed to change behavior of
     * some interconnected forms, that would mean modifying the needed code one place only, which is a big advantage over
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality better;
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderForm (S $objFormToRender, A $objFormArray = NULL) {
        // Make them defaults ...
        if ($objFormArray == NULL) $objFormArray = new A;

        // Do a switch ...
        switch ($objFormToRender) {
            case 'countryCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_ADD_COUNTRY))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objSettingsCountryTable);

                // Make the ISO ...
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $this->setUpdateId ($this->getPOST (self::$objSettingsCountryTableFIso))
                    ->setExtraUpdateData (self::$objSettingsCountryTableFName,
                    $this->getPOST (self::$objSettingsCountryTableFPrnt)->toUpper ());
                }

                // Continue;
                $this->setUpdateField (self::$objSettingsCountryTableFIso)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_ADD_COUNTRY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFIso)
                ->setLabel (new S (SETTINGS_COUNTRY_ISO))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFPrnt)
                ->setLabel (new S (SETTINGS_COUNTRY_NAME))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFIsoT)
                ->setJSRegExpReplace (new S ('[^A-Z]'))
                ->setLabel (new S (SETTINGS_COUNTRY_ISO3))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFCode)
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setLabel (new S (SETTINGS_COUNTRY_CODE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'countryEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_EDIT_COUNTRY))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objSettingsCountryTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID]);

                // Set the printable country name;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $objCountryName = clone $this->getPOST (self::$objSettingsCountryTableFPrnt);
                    $this->setExtraUpdateData (self::$objSettingsCountryTableFName,
                    $objCountryName->toUpper ());
                }

                // Continue;
                $this->setUpdateField (self::$objSettingsCountryTableFIso)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_EDIT_COUNTRY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFPrnt)
                ->setLabel (new S (SETTINGS_COUNTRY_NAME))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFIsoT)
                ->setJSRegExpReplace (new S ('[^A-Z]'))
                ->setLabel (new S (SETTINGS_COUNTRY_ISO3))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsCountryTableFCode)
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setLabel (new S (SETTINGS_COUNTRY_CODE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'countryErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Set some requirements;
                $objSQLConditionForUsers = new S ('WHERE %objAuthUsersTableFCountry = "%cId"');

                if (self::$objAuthenticationMech->getUserCount ($objSQLConditionForUsers
                ->doToken ('%cId', $_GET[ADMIN_ACTION_ID]))->toInt () == 0) {
                    // Do ERASE;
                    $this->_Q (_QS ('doDELETE')
                    ->doToken ('%table', self::$objSettingsCountryTable)
                    ->doToken ('%condition', new S ('%objSettingsCountryTableFIso = "%sId"'))
                    ->doToken ('%sId', $_GET[ADMIN_ACTION_ID]));

                    // Redirect;
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                } else {
                    // Do me, ERROR;
                    self::$objAdministration->setErrorMessage (new S
                    (SETTINGS_CANNOT_DELETE_COUNTRY), $objURLToGoBack);
                }
                break;

            case 'errorPageCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Check CODE is unique;
                if ($this->checkPOST (self::$objSettingsErrTableFCode)->toBoolean () == TRUE) {
                    if ($this->checkErrorPageCodeIsUnique ($this
                    ->getPOST (self::$objSettingsErrTableFCode))->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objSettingsErrTableFCode,
                        new S (SETTINGS_ERROR_CODE_MUST_BE_UNIQUE));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_ADD_ERROR_PAGE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objSettingsErrTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objSettingsErrTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_ADD_ERROR_PAGE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsErrTableFCode)
                ->setLabel (new S (SETTINGS_ERROR_PAGE_CODE))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsErrTableFTitle)
                ->setLabel (new S (SETTINGS_ERROR_PAGE_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objSettingsErrTableFContent)
                ->setLabel (new S (SETTINGS_ERROR_PAGE_CONTENT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'errorPageEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Check CODE is unique;
                if ($this->checkPOST (self::$objSettingsErrTableFCode)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objSettingsErrTableFCode) != $this
                    ->getErrorPageById ($_GET[ADMIN_ACTION_ID], self::$objSettingsErrTableFCode)) {
                        if ($this->checkErrorPageCodeIsUnique ($this
                        ->getPOST (self::$objSettingsErrTableFCode))->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objSettingsErrTableFCode,
                            new S (SETTINGS_ERROR_CODE_MUST_BE_UNIQUE));
                        }
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_EDIT_ERROR_PAGE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objSettingsErrTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objSettingsErrTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_EDIT_ERROR_PAGE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsErrTableFCode)
                ->setLabel (new S (SETTINGS_ERROR_PAGE_CODE))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objSettingsErrTableFTitle)
                ->setLabel (new S (SETTINGS_ERROR_PAGE_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objSettingsErrTableFContent)
                ->setLabel (new S (SETTINGS_ERROR_PAGE_CONTENT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'errorPageErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Make the erase, than redirect;
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objSettingsErrTable)
                ->doToken ('%condition', new S ('%objSettingsErrTableFId = "%sId"'))
                ->doToken ('%sId', $_GET[ADMIN_ACTION_ID]));

                // Redirect;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

        	case 'configurationEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)),
                    new A (Array ($this->getPOST (new S ('what')))));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_MANAGE_CONFIG))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_DO))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S (SETTINGS_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEditAdminApplicationTitle'))
                ->setValue (new S ('configurationEditAdminApplicationTitle'))
                ->setLabel (new S (SETTINGS_ADMIN_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEditApplicationTitle'))
                ->setValue (new S ('configurationEditApplicationTitle'))
                ->setLabel (new S (SETTINGS_FRONTEND_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEditWebsiteNotificationEmail'))
                ->setValue (new S ('configurationEditWebsiteNotificationEmail'))
                ->setLabel (new S (SETTINGS_WEBSITE_NOTIFICATION_EMAIL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEditDefaultDateType'))
                ->setValue (new S ('configurationEditDefaultDateType'))
                ->setLabel (new S (SETTINGS_DEFAULT_DATE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEditAdminApplicationTitle':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST() as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_UPDATE_SETTINGS))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_UPDATE_SETTINGS))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('administration_header_text'))
                ->setLabel (new S (SETTINGS_ADMIN_APPLICATION_TITLE))
                ->setValue ($this->getConfigKey (new S ('administration_header_text')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEditApplicationTitle':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST() as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_UPDATE_SETTINGS))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_UPDATE_SETTINGS))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('settings_website_default_title'))
                ->setLabel (new S (SETTINGS_ADMIN_APPLICATION_TITLE))
                ->setValue ($this->getConfigKey (new S ('settings_website_default_title')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEditWebsiteNotificationEmail':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST() as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_UPDATE_SETTINGS))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_UPDATE_SETTINGS))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('settings_website_notification_email'))
                ->setLabel (new S (SETTINGS_NOTIFICATION_EMAIL_LABEL))
                ->setValue ($this->getConfigKey (new S ('settings_website_notification_email')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEditDefaultDateType':
            	// The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST() as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Get the date format used for now ...
                $objDateFormat = $this->getConfigKey (new S ('settings_default_date_format'));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (SETTINGS_UPDATE_SETTINGS))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (SETTINGS_UPDATE_SETTINGS))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('settings_default_date_format'))
                ->setLabel (new S (SETTINGS_DEFAULT_DATE_FORMAT))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_1'))
                ->setLabel (new S (date ('F j, Y, g:i a')))
                ->setValue (new S ('F j, Y, g:i a'))
                ->setSelected (new B ($objDateFormat ==
                new S ('F j, Y, g:i a') ? TRUE : FALSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_2'))
                ->setLabel (new S (date ('D M j G:i:s T Y')))
                ->setValue (new S ('D M j G:i:s T Y'))
                ->setSelected (new B ($objDateFormat ==
                new S ('D M j G:i:s T Y') ? TRUE : FALSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_3'))
                ->setLabel (new S (date ('r')))
                ->setValue (new S ('r'))
                ->setSelected (new B ($objDateFormat ==
                new S ('r') ? TRUE : FALSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_4'))
                ->setLabel (new S (date ('d-m-Y H:i:s')))
                ->setValue (new S ('d-m-Y H:i:s'))
                ->setSelected (new B ($objDateFormat ==
                new S ('d-m-Y H:i:s') ? TRUE : FALSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_5'))
                ->setLabel (new S (date ('Y-m-d H:i:s')))
                ->setValue (new S ('Y-m-d H:i:s'))
                ->setSelected (new B ($objDateFormat ==
                new S ('Y-m-d H:i:s') ? TRUE : FALSE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
            	break;
        }
    }
}
?>