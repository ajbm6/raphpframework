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

############# Motto: "Spam means HAM in spammer's jargon!";
class Newsletter extends ICommonExtension implements IFaceCommonConfigExtension {
    /* OBJECT: Identity */
    protected static $objName                   = 'Newsletter :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* MPTT for categories */
    protected static $objMPTT                   = NULL;

    /* TABLE: Newsletter */
    public static $objLetterTable               = NULL;
    public static $objLetterTableFId            = NULL;
    public static $objLetterTableFEML           = NULL;
    public static $objLetterTableFFirstName     = NULL;
    public static $objLetterTableFLastName      = NULL;
    public static $objLetterTableFType          = NULL;
    public static $objLetterTableFConfirmed     = NULL;
    public static $objLetterTableFKey           = NULL;
    public static $objLetterTableFSubscribed    = NULL;
    public static $objLetterTableFCategoryId    = NULL;

    /* TABLE: Categories */
    public static $objCategoryTable             = NULL;
    public static $objCategoryTableFId          = NULL;
    public static $objCategoryTableFName        = NULL;
    public static $objCategoryTableFSEO         = NULL;
    public static $objCategoryTableFDescription = NULL;
    public static $objCategoryTableFDate        = NULL;

    /* REGEXPses */
    const REGEXP_PHP_CHECK_EMAIL                = '/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    const REGEXP_JS_CATEGORY                    = '[^a-zA-Z0-9 ,_\.\"\?\!\@\#\$\%\^\&\*\~\:\; \(\)\|\-]';

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent;
        parent::__construct ();
        // Do the tie ...
        $this->tieInCommonConfiguration ();

        // Set the proper configuration options, from the config file;
        self::$objLetterTable                   = $this->getConfigKey (new S ('newsletter_table'));
        self::$objLetterTableFId                = $this->getConfigKey (new S ('newsletter_table_field_id'));
        self::$objLetterTableFEML               = $this->getConfigKey (new S ('newsletter_table_field_email'));
        self::$objLetterTableFFirstName         = $this->getConfigKey (new S ('newsletter_table_field_first_name'));
        self::$objLetterTableFLastName          = $this->getConfigKey (new S ('newsletter_table_field_last_name'));
        self::$objLetterTableFType              = $this->getConfigKey (new S ('newsletter_table_field_email_type'));
        self::$objLetterTableFConfirmed         = $this->getConfigKey (new S ('newsletter_table_field_is_confirmed'));
        self::$objLetterTableFKey               = $this->getConfigKey (new S ('newsletter_table_field_confirmation_key'));
        self::$objLetterTableFSubscribed        = $this->getConfigKey (new S ('newsletter_table_field_date'));
        self::$objLetterTableFCategoryId        = $this->getConfigKey (new S ('newsletter_table_field_category_id'));

        // Categories ...
        self::$objCategoryTable                 = $this->getConfigKey (new S ('newsletter_category_table'));
        self::$objCategoryTableFId              = $this->getConfigKey (new S ('newsletter_category_table_id'));
        self::$objCategoryTableFName            = $this->getConfigKey (new S ('newsletter_category_table_name'));
        self::$objCategoryTableFSEO             = $this->getConfigKey (new S ('newsletter_category_table_seo'));
        self::$objCategoryTableFDescription     = $this->getConfigKey (new S ('newsletter_category_table_description'));
        self::$objCategoryTableFDate            = $this->getConfigKey (new S ('newsletter_category_table_date'));

        // Load'em defaults ... ATH, STG and others ...
        $this->ATH = MOD::activateModule (new FilePath ('mod/authentication'), new B (TRUE));
        $this->STG = MOD::activateModule (new FilePath ('mod/settings'), new B (TRUE));

        // DB: Auto-CREATE:
        $objQueryDB = new FileContent ($this->getPathToModule ()->toRelativePath () .
        _S . CFG_DIR . _S .  __CLASS__ . SCH_EXTENSION);

        // Make a FOREACH on each ...
        foreach (_S ($objQueryDB->toString ())
        ->fromStringToArray (RA_SCHEMA_HASH_TAG) as $k => $v) {
            // Make'em ...
            $this->_Q (_S ($v));
        }

        // Get an MPTT Object, build the ROOT, make sure the table is OK;
        self::$objMPTT = new MPTT (self::$objCategoryTable,
        MPTT::mpttAddUnique (new S (__CLASS__),
        new S ((string) $_SERVER['REQUEST_TIME'])));
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
        $objTokens[1]   = 'objLetterTable';
        $objTokens[2]   = 'objLetterTableFId';
        $objTokens[3]   = 'objLetterTableFEML';
        $objTokens[4]   = 'objLetterTableFFirstName';
        $objTokens[5]   = 'objLetterTableFLastName';
        $objTokens[6]   = 'objLetterTableFType';
        $objTokens[7]   = 'objLetterTableFConfirmed';
        $objTokens[8]   = 'objLetterTableFKey';
        $objTokens[9]   = 'objLetterTableFSubscribed';
        $objTokens[10]  = 'objCategoryTable';
        $objTokens[11]  = 'objCategoryTableFId';
        $objTokens[12]  = 'objCategoryTableFName';
        $objTokens[13]  = 'objCategoryTableFSEO';
        $objTokens[14]  = 'objLetterTableFCategoryId';
        $objTokens[15]  = 'objCategoryTableFDate';
        $objTokens[16]  = 'objCategoryTableFDescription';

        // Set the replacements;
        $objReplac      = new A;
        $objReplac[1]   = self::$objLetterTable;
        $objReplac[2]   = self::$objLetterTableFId;
        $objReplac[3]   = self::$objLetterTableFEML;
        $objReplac[4]   = self::$objLetterTableFFirstName;
        $objReplac[5]   = self::$objLetterTableFLastName;
        $objReplac[6]   = self::$objLetterTableFType;
        $objReplac[7]   = self::$objLetterTableFConfirmed;
        $objReplac[8]   = self::$objLetterTableFKey;
        $objReplac[9]   = self::$objLetterTableFSubscribed;
        $objReplac[10]  = self::$objCategoryTable;
        $objReplac[11]  = self::$objCategoryTableFId;
        $objReplac[12]  = self::$objCategoryTableFName;
        $objReplac[13]  = self::$objCategoryTableFSEO;
        $objReplac[14]  = self::$objLetterTableFCategoryId;
        $objReplac[15]  = self::$objCategoryTableFDate;
        $objReplac[16]  = self::$objCategoryTableFDescription;

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
        $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () . $this
        ->getConfigKey (new S ('newsletter_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (MANAGE_NEWSLETTER), $objMF,
        $this->getHELP (new S (MANAGE_NEWSLETTER)));

        // Set ACLs;
        $objACL   = new A;
        $objACL[] = new S ('Newsletter.Newsletter.Do.View');
        $objACL[] = new S ('Newsletter.Categories.Do.View');
        $objACL[] = new S ('Newsletter.Do.Operations');
        $objACL[] = new S ('Newsletter.Do.Configuration');

        // ONLY: Newsletter.Newsletter.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () . $this
            ->getConfigKey (new S ('newsletter_file_manage_newsletter')));
            self::$objAdministration->setSubMLink (new S (MANAGE_NEWSLETTER), $objMF,
            $this->getHELP (new S (MANAGE_NEWSLETTER)));
        }

        // ONLY: Newsletter.Newsletter.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () . $this
            ->getConfigKey (new S ('newsletter_file_manage_categories')));
            self::$objAdministration->setSubMLink (new S (MANAGE_NEWSLETTER_CATEGORIES), $objMF,
            $this->getHELP (new S (MANAGE_NEWSLETTER_CATEGORIES)));
        }

        // ONLY: Newsletter.Newsletter.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () . $this
            ->getConfigKey (new S ('newsletter_file_manage_operations')));
            self::$objAdministration->setSubMLink (new S (MANAGE_NEWSLETTER_OPERATIONS), $objMF,
            $this->getHELP (new S (MANAGE_NEWSLETTER_OPERATIONS)));
        }

        // ONLY: Newsletter.Newsletter.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[3])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () . $this
            ->getConfigKey (new S ('newsletter_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (MANAGE_NEWSLETTER_CONFIG), $objMF,
            $this->getHELP (new S (MANAGE_NEWSLETTER_CONFIG)));
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
        $objACL   = new A;
        $objACL[] = new S ('Newsletter.Newsletter.Do.View');
        $objACL[] = new S ('Newsletter.Categories.Do.View');
        $objACL[] = new S ('Newsletter.Do.Operations');
        $objACL[] = new S ('Newsletter.Do.Configuration');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if ($this->ATH->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMakeZone ($objACL[$k], $this->getObjectCLASS ());

            if ($this->ATH->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMapAdministratorToZone ($objACL[$k]);
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
        // Do a CALL to the parent;
        parent::tieInWithFrontend ($objFrontendObject);
    }

    /**
     * Will check to see if the address is unique, because otherwise it would be a waste of time, money and storage space to
     * actually store two single e-mail adresses that are not unique. Resources are expensive these days and we want to be sure
     * that we get the least amount of resource for the maximum amount of output;
     *
     * @param S $objSubscriberEML The subscriber EML;
     * @return boolean Will return true it's unique;
     */
    public function checkSubscriberAddressIsUnique (S $objSubscriberEML) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objLetterTableFEML'))->doToken ('%table', self::$objLetterTable)
        ->doToken ('%condition', new S ('WHERE %objLetterTableFEML = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objSubscriberEML))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check to see if the category URL is unique;
     *
     * This method will check that the category URL is unique, because we want to first make an unique SQL index on the URL
     * of categories, but due to the fact that we automatically used the category URL as the rewritten URL we need to make sure
     * that no two categories have the same URL. Also, two categories with the exact same name can be confusing for users at first,
     * and most importantly, for search engines at second;
     *
     * @param S $objCategoryURL The article to check for;
     * @return boolean Will return true if the article title is unique in the database;
     */
    public function checkCategoryURLIsUnique (S $objCategoryURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objCategoryTableFSEO'))->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the count of subscribers in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getSubscribers, and that should return
     * the number of subs. that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getSubscriberCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) AS count'))->doToken ('%table', self::$objLetterTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of categories in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getCategories, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of categories that matched the condition, if given;
     */
    public function getCategoryCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) AS count'))->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the articles, based on the specified condition defined in the database, by taking the passed SQL condition as
     * argument. If no condition is specified, then it will return ALL defined subscribers in the table;
     *
     * @param S $objSQLCondition The SQL condition passed for articles to get;
     * @return array The result array;
     */
    public function getSubscribers (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objLetterTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return information about a given subscriber id (the id is given as a string), while giving the corresponding field
     * as a parameter to the function. An easy way to get information about stored subscribers, without having too much of a
     * problem with getting information from them;
     *
     * @param S $objArticleId The article id to query for;
     * @param S $objFieldToGet The article field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getSubscriberInfoById (S $objSubscriberId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objLetterTable)
        ->doToken ('%condition', new S ('WHERE %objLetterTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objSubscriberId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the array of categories, if given a sub-category or not, with a given condition or not. The combination of these
     * two factors can make the array be returned empty, depends much on what the developer asks the MPTT object to do.
     *
     * @param S $objSubCategory The sub-category to start getting sub-categories for;
     * @param S $objSQLCondition The SQL condition, ASC or DESC ordering, or any other appended SQL condition;
     * @return array The array of returned categories/subcategories;
     */
    public function getCategories (S $objSQLCondition = NULL,
    S $objSubCategory = NULL) {
        // Do return ...
        return self::$objMPTT->mpttGetTree ($objSubCategory,
        $objSQLCondition);
    }

    /**
     * Will return information about a given category id, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories, without having too much of a problem with getting information about
     * them;
     *
     * @param S $objCategoryId The category id to query for;
     * @param S $objFieldToGet The category field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getCategoryInfoById (S $objCategoryId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given category name, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories without having too much of a problem with getting information from them;
     *
     * @param S $objCategoryName The category name to query for;
     * @param S $objFieldToGet The category field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getCategoryInfoByName (S $objCategoryName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given category URL, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories without having too much of a problem with getting information from them;
     *
     * @param S $objCategoryURL The category URL to query for;
     * @param S $objFieldToGet The category field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getCategoryInfoByURL (S $objCategoryURL, S $objFieldToGet) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryURL))->offsetGet (0)->offsetGet ($objFieldToGet);
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
            case 'manageNewsletter':
                // Do some work;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('newsletterEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('newsletterErase'));
                            break;
                    }
                } else {
                    // Redirect to DescBySubscribed ...
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescBySubscribed'))), new S ('Location'));

                    // Set some requirements ...
                    $objGetCondition = new S;

                    // Check for sorting ...
                    if (isset ($_GET[ADMIN_ACTION_BY])) {
			            // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_BY]) {

                        }

                        // Add LIKE searching ...
                        $objGetCondition->appendString (_SP)->appendString ('LIKE "%%Search%"')
                        ->doToken ('%Search', $_GET[ADMIN_ACTION_SEARCH]);

                        // Get the count, on SQL ...
                        $objSearchCount = $this->getSubscriberCount ($objGetCondition);
                    }

                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByEmail':
                            case 'DescByEmail':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLetterTableFEML');

                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByEmail':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByEmail':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByFirstName':
                            case 'DescByFirstName':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLetterTableFFirstName');

                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByFirstName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByFirstName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByLastName':
                            case 'DescByLastName':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLetterTableFLastName');

                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByLastName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByLastName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscBySubscribed':
                            case 'DescBySubscribed':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLetterTableFSubscribed');

                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscBySubscribed':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescBySubscribed':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objLetterTable      = $this->getSubscribers ($objGetCondition);
                    $objLetterTableCount = $this->getSubscriberCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageNewsletter.tp');
                    TPL::tpSet ($objLetterTable, new S ('newsletterTable'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objLetterTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objLetterTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('newsletterSearch'));
                    $this->renderForm (new S ('newsletterCreate'));
                }
                // Break out ...
                break;

            case 'manageCategories':
                // Add some requirements;
                TPL::manageJSS (new FilePath ($this->getPathToSkinJSS ()
                ->toRelativePath () . 'manageCategories.js'), new S ('manageCategories'));

                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch ...
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('categoryEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('categoryErase'));
                            break;

                        case ADMIN_ACTION_MOVE:
                            $this->renderForm (new S ('categoryMove'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;

                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByCategory':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ASC');
                                break;

                            case 'DescByCategory':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('DESC');
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objCategoryTreeCount = $this->getCategoryCount ();
                    $objCategoryTree = $this->getCategories (isset ($_GET[ADMIN_SHOW_ALL]) ? new S : $objGetCondition);

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageCategories.tp');
                    TPL::tpSet ($objCategoryTree, new S ('categoryTree'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination ...
                    if ($objCategoryTreeCount->toInt () > 10 && !isset ($_GET[ADMIN_SHOW_ALL]))
                    self::$objAdministration->setPagination ($objCategoryTreeCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('categoryCreate'));
                }
                // Break out ...
                break;

            case 'manageOperations':
                // Do the form, make it happen;
                $this->renderForm (new S ('categoryMoveOperation'));
                break;

            case 'manageConfiguration':
                // Do the form, make it happen ...
                if (isset ($_GET[ADMIN_ACTION])) { $this->renderForm ($_GET[ADMIN_ACTION]); }
                else { $this->renderForm (new S ('configurationEdit')); }
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
    public function renderForm (S $objFormToRender, A $objFormArray = NULL) {
        // Make them defaults ...
        if ($objFormArray == NULL) $objFormArray = new A;

        // Do a switch ...
        switch ($objFormToRender) {
            case 'newsletterSearch':
                break;

            case 'newsletterCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objLetterTableFEML)
                ->toBoolean () == TRUE) {
                    if ($this->checkSubscriberAddressIsUnique ($this
                    ->getPOST (self::$objLetterTableFEML))->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objLetterTableFEML,
                        new S (NEWSLETTER_EMAIL_MUST_BE_UNIQUE));
                    }

                    if ($this->getPOST (self::$objLetterTableFEML)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objLetterTableFEML,
                        new S (NEWSLETTER_FIELD_IS_EMPTY));
                    }
                }

                if ($this->checkPOST (self::$objLetterTableFFirstName)
                ->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objLetterTableFFirstName)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objLetterTableFFirstName,
                        new S (NEWSLETTER_FIELD_IS_EMPTY));
                    }
                }

                if ($this->checkPOST (self::$objLetterTableFLastName)
                ->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objLetterTableFLastName)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objLetterTableFLastName,
                        new S (NEWSLETTER_FIELD_IS_EMPTY));
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (NEWSLETTER_ADD))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objLetterTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objLetterTableFId)
                ->setExtraUpdateData (self::$objLetterTableFSubscribed, new S ((string) time ()))
                ->setExtraUpdateData (self::$objLetterTableFConfirmed, new S ('Y'));
                if ($this->checkPOST (self::$objLetterTableFEML)->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (new S (NEWSLETTER_ADD))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLetterTableFEML)
                ->setLabel (new S (NEWSLETTER_EMAIL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLetterTableFFirstName)
                ->setLabel (new S (NEWSLETTER_FIRSTNAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLetterTableFLastName)
                ->setLabel (new S (NEWSLETTER_LASTNAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objLetterTableFType)
                ->setLabel (new S (NEWSLETTER_TYPE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('nw_html'))
                ->setValue (new S ('HTML'))
                ->setLabel (new S (NEWSLETTER_HTML))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('nw_txt'))
                ->setValue (new S ('PLAIN'))
                ->setLabel (new S (NEWSLETTER_PLAIN))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setLabel (new S (NEWSLETTER_CATEGORY))
                ->setName (self::$objLetterTableFCategoryId)
                ->setContainerDiv (new B (TRUE));

                // Categories ...
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue ...
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'newsletterEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST (self::$objLetterTableFEML)
                ->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objLetterTableFEML) != $this
                    ->getSubscriberInfoById ($_GET[ADMIN_ACTION_ID], self::$objLetterTableFEML)) {
                        if ($this->checkSubscriberAddressIsUnique ($this
                        ->getPOST (self::$objLetterTableFEML))->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objLetterTableFEML,
                            new S (NEWSLETTER_EMAIL_MUST_BE_UNIQUE));
                        }
                    }

                    if ($this->getPOST (self::$objLetterTableFEML)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objLetterTableFEML,
                        new S (NEWSLETTER_FIELD_IS_EMPTY));
                    }
                }

                if ($this->checkPOST (self::$objLetterTableFFirstName)
                ->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objLetterTableFFirstName)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objLetterTableFFirstName,
                        new S (NEWSLETTER_FIELD_IS_EMPTY));
                    }
                }

                if ($this->checkPOST (self::$objLetterTableFLastName)
                ->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objLetterTableFLastName)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objLetterTableFLastName,
                        new S (NEWSLETTER_FIELD_IS_EMPTY));
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (NEWSLETTER_EDIT))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objLetterTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objLetterTableFId);
                if ($this->checkPOST (self::$objLetterTableFEML)->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (new S (NEWSLETTER_EDIT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLetterTableFEML)
                ->setLabel (new S (NEWSLETTER_EMAIL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLetterTableFFirstName)
                ->setLabel (new S (NEWSLETTER_FIRSTNAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLetterTableFLastName)
                ->setLabel (new S (NEWSLETTER_LASTNAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objLetterTableFType)
                ->setLabel (new S (NEWSLETTER_TYPE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('nw_html'))
                ->setValue (new S ('HTML'))
                ->setLabel (new S (NEWSLETTER_HTML))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('nw_txt'))
                ->setValue (new S ('PLAIN'))
                ->setLabel (new S (NEWSLETTER_PLAIN))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setLabel (new S (NEWSLETTER_CATEGORY))
                ->setName (self::$objLetterTableFCategoryId)
                ->setContainerDiv (new B (TRUE));

                // Categories ...
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue ...
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'newsletterErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objLetterTable)
                ->doToken ('%condition', new S ('%objLetterTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'categoryCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work ...
                if ($this->checkPOST (new S ('categories_show_all'))->toBoolean () == TRUE) {
                    // Redirect to proper ...
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_SHOW_ALL)),
                    new A (Array ('1'))), new S ('Location'));
                }

                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    $objToCheck = MPTT::mpttAddUnique ($this->getPOST (new S ('add_category')),
                    new S ((string) $_SERVER['REQUEST_TIME']));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (new S ('add_category'),
                        new S (NEWSLETTER_CATEGORY_NAME_IS_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (NEWSLETTER_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }

                        if ($this->checkCategoryURLIsUnique (URL::getURLFromString ($objToCheck))
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (NEWSLETTER_CATEGORY_URL_MUST_BE_UNIQUE));
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;

                        // Do a switch ...
                        switch ($this->getPOST (new S ('add_category_as_what'))) {
                            case NEWSLETTER_CATEGORY_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::FIRST_CHILD);
                                break;

                            case NEWSLETTER_CATEGORY_LAST_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::LAST_CHILD);
                                break;

                            case NEWSLETTER_CATEGORY_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::PREVIOUS_BROTHER);
                                break;

                            case NEWSLETTER_CATEGORY_NEXT_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::NEXT_BROTHER);
                                break;
                        }

                        // Add the node;
                        self::$objMPTT->mpttAddNode ($objToCheck,
                        $this->getPOST (new S ('add_category_parent_or_bro')), $objAddNodeAS);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (NEWSLETTER_ADD_CATEGORY))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (new S (NEWSLETTER_SHOW_ALL_CATEGORIES))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('add_category_submit'))
                ->setValue (new S (NEWSLETTER_ADD_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('add_category'))
                ->setLabel (new S (NEWSLETTER_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_as_what'))
                ->setLabel (new S (NEWSLETTER_AS_A))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_first_child'))
                ->setLabel (new S (NEWSLETTER_CATEGORY_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_last_child'))
                ->setLabel (new S (NEWSLETTER_CATEGORY_LAST_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_previous_brother'))
                ->setLabel (new S (NEWSLETTER_CATEGORY_BROTHER))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_next_brother'))
                ->setLabel (new S (NEWSLETTER_CATEGORY_NEXT_BROTHER))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_parent_or_bro'))
                ->setLabel (new S (NEWSLETTER_OF_CATEGORY));

                // Category parent or brother of this one ...
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objNameOfNode])
                    ->setValue ($v[self::$objMPTT->objNameOfNode])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique (CLONE $v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue, execute the form ...
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do validation and error on it if something goes wrong;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    $objToCheck = MPTT::mpttAddUnique ($this->getPOST (self::$objCategoryTableFName), $this
                    ->getCategoryInfoById ($_GET[ADMIN_ACTION_ID], self::$objCategoryTableFDate));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it. We don't allow empty group names;
                        $this->setErrorOnInput (self::$objCategoryTableFName,
                        new S (NEWSLETTER_CATEGORY_NAME_IS_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else if ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName) != $objToCheck) {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (self::$objCategoryTableFName,
                            new S (NEWSLETTER_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Make a form that will auto-insert; (because we have _SESSION['POST']);
                        $this->setMethod (new S ('POST'))
                        ->setEnctype (new S ('multipart/form-data'))
                        ->setSQLAction (new S ('update'))
                        ->setTableName (self::$objCategoryTable)
                        ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                        ->setUpdateField (self::$objCategoryTableFId)
                        ->setExtraUpdateData (self::$objCategoryTableFSEO,
                        URL::getURLFromString ($this->getPOST (self::$objCategoryTableFName)))
                        ->setName ($objFormToRender)
                        ->setRedirect ($objURLToGoBack)
                        ->setInputType (new S ('textarea'))
                        ->setName (self::$objCategoryTableFDescription)
                        ->setLabel (new S (NEWSLETTER_CATEGORY_DESCRIPTION))
                        ->setTinyMCETextarea (new B (TRUE))
                        ->setContainerDiv (new B (TRUE))
                        ->setFormEndAndExecute (new B (TRUE));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCategoryTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCategoryTableFId)
                ->setFieldset (new S (NEWSLETTER_EDIT_CATEGORY))
                ->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('edit_category_submit'))
                ->setValue (new S (NEWSLETTER_EDIT_CATEGORY))
                ->setInputType (new S ('text'))
                ->setName (self::$objCategoryTableFName)
                ->setMPTTRemoveUnique (new B (TRUE))
                ->setLabel (new S (NEWSLETTER_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCategoryTableFDescription)
                ->setLabel (new S (NEWSLETTER_CATEGORY_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Check if the category has items;
                if ($this->getSubscriberCount (_S ('WHERE %objLetterTableFCategoryId = "%Id"')
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))->toInt () != 0) {
                    // Set an error message;
                    self::$objAdministration->setErrorMessage (new S
                    (NEWSLETTER_CATEGORY_HAS_ARTICLES), $objURLToGoBack);
                } else {
                    // Do erase the group node from the table;
                    self::$objMPTT->mpttRemoveNode ($this
                    ->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName));

                    // Do a redirect, and get the user back where he belongs;
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // Break out ...
                break;

            case 'categoryMove':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_TO, ADMIN_ACTION_TYPE)));

                // Get names, as they are unique;
                $objThatIsMoved = $this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID], self::$objCategoryTableFName);
                $objWhereToMove = $this->getCategoryInfoById ($_GET[ADMIN_ACTION_TO], self::$objCategoryTableFName);

                // Get the node subtree, that's move, make sure the node we move to ain't a child;
                $objMovedNodeSubTree = self::$objMPTT->mpttGetTree ($objThatIsMoved);

                // Memorize;
                $objIsChild = new B (FALSE);
                foreach ($objMovedNodeSubTree as $k => $v) {
                     if ($v[self::$objMPTT->objNameOfNode] == $objWhereToMove) {
                         $objIsChild->switchType ();
                     }
                }

                // Check if it's a child or not;
                if ($objIsChild->toBoolean () == TRUE) {
                    // Set an error message;
                    self::$objAdministration->setErrorMessage (new S (NEWSLETTER_CATEGORY_MOVED_TO_CHILD),
                    $objURLToGoBack);
                } else {
                    // Move nodes;
                    self::$objMPTT->mpttMoveNode ($objThatIsMoved,
                    $objWhereToMove, $_GET[ADMIN_ACTION_TYPE]);
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                break;

            case 'categoryMoveOperation':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_SUBPAGE)), new A (Array (MANAGE_NEWSLETTER)));

                // Do some work;
                ($this->checkPOST ()->toBoolean () == TRUE) ?
                ($objOLDCategoryId = $this->getPOST (new S ('old_category_id'))) :
                ($objOLDCategoryId = new S ('0'));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (NEWSLETTER_MOVE_SUBSCRIBERS))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objLetterTable)
                ->setUpdateField (self::$objLetterTableFId)

                // Specific code here, need abstractization!
                ->setUpdateWhere ($this->doModuleToken (_S ('%objLetterTableFCategoryId = "%Id"')
                ->doToken ('%Id', $objOLDCategoryId)))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (NEWSLETTER_MOVE_SUBSCRIBERS))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('old_category_id'))
                ->setLabel (new S (NEWSLETTER_OLD_CATEGORY))
                ->setContainerDiv (new B (TRUE));

                // Cateories;
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Categories;
                $this->setInputType (new S ('select'))
                ->setName (self::$objLetterTableFCategoryId)
                ->setLabel (new S (NEWSLETTER_NEW_CATEGORY))
                ->setContainerDiv (new B (TRUE));
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue;
                $this->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
