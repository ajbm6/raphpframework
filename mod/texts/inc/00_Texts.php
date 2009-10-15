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

############# Motto: "Lorem ipsum dolor sit amet ...!";
class Texts extends ICommonExtension implements IFaceTexts {
    /* OBJECT: Identity */
    protected static $objName                       = 'Texts :: RA PHP Framework';
    protected $objIdentificationString              = __CLASS__;

    /* MPTT for categories */
    protected static $objMPTT                       = NULL;

    /* TABLE: Texts */
    public static $objTextTable                     = NULL;
    public static $objTextTableFId                  = NULL;
    public static $objTextTableFTitle               = NULL;
    public static $objTextTableFSEO                 = NULL;
    public static $objTextTableFContent             = NULL;
    public static $objTextTableFTags                = NULL;
    public static $objTextTableFCanComment          = NULL;
    public static $objTextTableFDatePublished       = NULL;
    public static $objTextTableFDateUpdated         = NULL;
    public static $objTextTableFAuthorId            = NULL;
    public static $objTextTableFCategoryId          = NULL;

    /* TABLE: Categories (sections) */
    public static $objCategoryTable                 = NULL;
    public static $objCategoryTableFId              = NULL;
    public static $objCategoryTableFName            = NULL;
    public static $objCategoryTableFSEO             = NULL;

    /* TABLE: Comments */
    public static $objCommentsTable                 = NULL;
    public static $objCommentsTableFId              = NULL;
    public static $objCommentsTableFName            = NULL;
    public static $objCommentsTableFEML             = NULL;
    public static $objCommentsTableFURL             = NULL;
    public static $objCommentsTableFRUId            = NULL;
    public static $objCommentsTableFComment         = NULL;
    public static $objCommentsTableFApproved        = NULL;
    public static $objCommentsTableFDate            = NULL;
    public static $objCommentsTableFTextId          = NULL;

    /* REGEXPses */
    const REGEXP_JS_TAGS                            = '[^a-zA-Z0-9 ,_-]';
    const REGEXP_JS_TITLE                           = '[^a-zA-Z0-9 ,_\.\"\?\!\@\#\$\%\^\&\*\~\:\; \(\)\|\-]';
    const REGEXP_JS_CATEGORY                        = '[^a-zA-Z0-9 ,_\.\"\?\!\@\#\$\%\^\&\*\~\:\; \(\)\|\-]';

    /* CONSTANTS: ALL */
    const XML_SITEMAP_PRIORITY                      = '0.3';
    const XML_SITEMAP_FREQUENCY                     = 'yearly';

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent, parse the configuration meanwhile;
        parent::__construct ();
        // Tie in common configuration data;
        $this->tieInCommonConfiguration ();

        // Get object configuration data, and set current object properties;
        self::$objTextTable                     = $this->getConfigKey (new S ('texts_table'));
        self::$objTextTableFId                  = $this->getConfigKey (new S ('texts_table_field_id'));
        self::$objTextTableFTitle               = $this->getConfigKey (new S ('texts_table_field_title'));
        self::$objTextTableFSEO                 = $this->getConfigKey (new S ('texts_table_field_seo'));
        self::$objTextTableFContent             = $this->getConfigKey (new S ('texts_table_field_content'));
        self::$objTextTableFTags                = $this->getConfigKey (new S ('texts_table_field_tags'));
        self::$objTextTableFCanComment          = $this->getConfigKey (new S ('texts_table_field_can_comment'));
        self::$objTextTableFDatePublished       = $this->getConfigKey (new S ('texts_table_field_date_published'));
        self::$objTextTableFDateUpdated         = $this->getConfigKey (new S ('texts_table_field_date_updated'));
        self::$objTextTableFAuthorId            = $this->getConfigKey (new S ('texts_table_field_author_id'));
        self::$objTextTableFCategoryId          = $this->getConfigKey (new S ('texts_table_field_category_id'));

        // Categories ...
        self::$objCategoryTable                 = $this->getConfigKey (new S ('texts_category_table'));
        self::$objCategoryTableFId              = $this->getConfigKey (new S ('texts_category_table_id'));
        self::$objCategoryTableFName            = $this->getConfigKey (new S ('texts_category_table_name'));
        self::$objCategoryTableFSEO             = $this->getConfigKey (new S ('texts_category_table_seo'));

        // Comments ...
        self::$objCommentsTable                 = $this->getConfigKey (new S ('texts_comments_table'));
        self::$objCommentsTableFId              = $this->getConfigKey (new S ('texts_comments_table_id'));
        self::$objCommentsTableFName            = $this->getConfigKey (new S ('texts_comments_table_name'));
        self::$objCommentsTableFEML             = $this->getConfigKey (new S ('texts_comments_table_email'));
        self::$objCommentsTableFURL             = $this->getConfigKey (new S ('texts_comments_table_website'));
        self::$objCommentsTableFRUId            = $this->getConfigKey (new S ('texts_comments_table_registered_user_id'));
        self::$objCommentsTableFComment         = $this->getConfigKey (new S ('texts_comments_table_comment'));
        self::$objCommentsTableFApproved        = $this->getConfigKey (new S ('texts_comments_table_approved'));
        self::$objCommentsTableFDate            = $this->getConfigKey (new S ('texts_comments_table_date'));
        self::$objCommentsTableFTextId          = $this->getConfigKey (new S ('texts_comments_table_text_id'));

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
        self::$objMPTT = new MPTT (self::$objCategoryTable, new S (__CLASS__));

        // Load'em defaults ... ATH, STG and others ...
        $this->ATH = MOD::activateModule (new FilePath ('mod/authentication'), new B (TRUE));
        $this->STG = MOD::activateModule (new FilePath ('mod/settings'), new B (TRUE));
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
        $objTokens[1]   = 'objTextTable';
        $objTokens[2]   = 'objTextTableFId';
        $objTokens[3]   = 'objTextTableFTitle';
        $objTokens[4]   = 'objTextTableFContent';
        $objTokens[5]   = 'objTextTableFDatePublished';
        $objTokens[6]   = 'objTextTableFDateUpdated';
        $objTokens[7]   = 'objTextTableFAuthorId';
        $objTokens[8]   = 'objTextTableFCategoryId';
        $objTokens[9]   = 'objTextTableFSEO';
        $objTokens[10]  = 'objTextTableFTags';
        $objTokens[11]  = 'objTextTableFCanComment';
        $objTokens[12]  = 'objCommentsTable';
        $objTokens[13]  = 'objCommentsTableFId';
        $objTokens[14]  = 'objCommentsTableFName';
        $objTokens[15]  = 'objCommentsTableFEML';
        $objTokens[16]  = 'objCommentsTableFURL';
        $objTokens[17]  = 'objCommentsTableFRUId';
        $objTokens[18]  = 'objCommentsTableFComment';
        $objTokens[19]  = 'objCommentsTableFApproved';
        $objTokens[20]  = 'objCommentsTableFDate';
        $objTokens[21]  = 'objCommentsTableFTextId';
        $objTokens[22]  = 'objCategoryTable';
        $objTokens[23]  = 'objCategoryTableFId';
        $objTokens[24]  = 'objCategoryTableFName';
        $objTokens[25]  = 'objCategoryTableFSEO';
        $objTokens[26]  = 'objAuthenticationUserTable';
        $objTokens[27]  = 'objAuthenticationUserTableFId';

        // Set the replacements;
        $objReplac      = new A;
        $objReplac[1]   = self::$objTextTable;
        $objReplac[2]   = self::$objTextTableFId;
        $objReplac[3]   = self::$objTextTableFTitle;
        $objReplac[4]   = self::$objTextTableFContent;
        $objReplac[5]   = self::$objTextTableFDatePublished;
        $objReplac[6]   = self::$objTextTableFDateUpdated;
        $objReplac[7]   = self::$objTextTableFAuthorId;
        $objReplac[8]   = self::$objTextTableFCategoryId;
        $objReplac[9]   = self::$objTextTableFSEO;
        $objReplac[10]  = self::$objTextTableFTags;
        $objReplac[11]  = self::$objTextTableFCanComment;
        $objReplac[12]  = self::$objCommentsTable;
        $objReplac[13]  = self::$objCommentsTableFId;
        $objReplac[14]  = self::$objCommentsTableFName;
        $objReplac[15]  = self::$objCommentsTableFEML;
        $objReplac[16]  = self::$objCommentsTableFURL;
        $objReplac[17]  = self::$objCommentsTableFRUId;
        $objReplac[18]  = self::$objCommentsTableFComment;
        $objReplac[19]  = self::$objCommentsTableFApproved;
        $objReplac[20]  = self::$objCommentsTableFDate;
        $objReplac[21]  = self::$objCommentsTableFTextId;
        $objReplac[22]  = self::$objCategoryTable;
        $objReplac[23]  = self::$objCategoryTableFId;
        $objReplac[24]  = self::$objCategoryTableFName;
        $objReplac[25]  = self::$objCategoryTableFSEO;
        $objReplac[26]  = Authentication::$objAuthUsersTable;
        $objReplac[27]  = Authentication::$objAuthUsersTableFId;

        // Do a CALL to the parent, make it tokenize;
        return parent::doModuleTokens ($objTokens, $objReplac, $objSQLParam);
    }

    /**
     * Will tie the current module with the administration module;
     *
     * This method will invoke the proper administration methods used to tie the current module with the administrator interface,
     * mainly, adding the proper links to the main menu and sub-menu. The necessary information is taken from the configuration
     * file, while the names of the links are taken from constants defined in the current module;
     *
     * @param IFaceAdministration $objAdministrationMech The administration mechanism to tie to;
     * @return void Doesn't need to return anything;
     */
    public function tieInWithAdministration (IFaceAdministration $objAdministrationMech) {
        // Do a CALL to the parent;
        parent::tieInWithAdministration ($objAdministrationMech);

        // Do the administration menu;
        $objWP = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
        $this->getConfigKey (new S ('texts_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (MANAGE_TEXTS), $objWP,
        $this->getHELP (new S (MANAGE_TEXTS)));

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Texts.Texts.Do.View');
        $objACL[] = new S ('Texts.Categories.Do.View');
        $objACL[] = new S ('Texts.Comments.Do.View');
        $objACL[] = new S ('Texts.Do.Operations');
        $objACL[] = new S ('Texts.Do.Configuration');

        // ONLY: Texts.Texts.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMT = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('texts_file_manage_articles')));
            self::$objAdministration->setSubMLink (new S (MANAGE_TEXTS),
            $objMT, $this->getHELP (new S (MANAGE_TEXTS)));
        }

        // ONLY: Texts.Categories.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('texts_file_manage_categories')));
            self::$objAdministration->setSubMLink (new S (MANAGE_TEXTS_CATEGORIES),
            $objMC, $this->getHELP (new S (MANAGE_TEXTS_CATEGORIES)));
        }

        // ONLY: Texts.Comments.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('texts_file_manage_comments')));
            self::$objAdministration->setSubMLink (new S (MANAGE_TEXTS_COMMENTS),
            $objMC, $this->getHELP (new S (MANAGE_TEXTS_COMMENTS)));
        }

        // ONLY: Texts.Do.Operations
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[3])->toBoolean () == TRUE) {
            $objMM = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('texts_file_manage_move_articles')));
            self::$objAdministration->setSubMLink (new S (MANAGE_TEXTS_MOVE),
            $objMM, $this->getHELP (new S (MANAGE_TEXTS_MOVE)));
        }

        // ONLY: Texts.Do.Configuration
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[4])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('texts_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (MANAGE_TEXTS_CONFIG),
            $objMF, $this->getHELP (new S (MANAGE_TEXTS_CONFIG)));
        }

        // WIDGET: Statistics for texts ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%c', $this->getCategoryCount ())
        ->doToken ('%t', $this->getTextCount ()));

        // WIDGET: Latest 10 texts ... no status query ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminWidgetLatest10')),
        new B (TRUE));

        // WIDGET: Statistics for comments ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminCommentStatistics'))
        ->doToken ('%a', $this->getCommentCount (new S ('WHERE %objCommentsTableFApproved = "Y"')))
        ->doToken ('%u', $this->getCommentCount (new S ('WHERE %objCommentsTableFApproved = "N"')))
        ->doToken ('%c', $this->getCommentCount ()));

        // WIDGET: Latest 10 comments ... no status query ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminWidgetLatest10Comments')),
        new B (TRUE));
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
        $objACL[] = new S ('Texts.Texts.Do.View');
        $objACL[] = new S ('Texts.Categories.Do.View');
        $objACL[] = new S ('Texts.Comments.Do.View');
        $objACL[] = new S ('Texts.Do.Operations');
        $objACL[] = new S ('Texts.Do.Configuration');

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

        // RSS ...
        TPL::manageLNK (new S ('RSS - LATEST 30 - Pages'), new S (Frontend::RSS_ALTERNATE),
        new S (Frontend::RSS_TYPE), URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
        FRONTEND_FEED_URL)), new A (Array (FRONTEND_RSS_XML, __CLASS__))));
    }

    /**
     * Will check to see if the article title is unique;
     *
     * This method will check that the article title is unique, because we want to first make an unique SQL index on the title
     * of articles, but due to the fact that we automatically used the article title as the rewritten URL we need to make sure
     * that no two articles have the same title. Also, two articles with the exact same name can be confusing for users at first,
     * and most importantly, for search engines at second;
     *
     * @param S $objArticleTitle The article to check for;
     * @return boolean Will return true if the article title is unique in the database;
     */
    public function checkTextTitleIsUnique (S $objTextTable) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objTextTableFTitle'))->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', new S ('WHERE %objTextTableFTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objTextTable))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check to see if the article URL is unique;
     *
     * This method will check that the article URL is unique, because we want to first make an unique SQL index on the URL
     * of articles, but due to the fact that we automatically used the article URL as the rewritten URL we need to make sure
     * that no two articles have the same URL. Also, two articles with the exact same name can be confusing for users at first,
     * and most importantly, for search engines at second;
     *
     * @param S $objArticleURL The article to check for;
     * @return boolean Will return true if the article title is unique in the database;
     */
    public function checkTextURLIsUnique (S $objTextURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objTextTableFSEO'))->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', new S ('WHERE %objTextTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objTextURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the count of articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getTexts, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getTextCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objTextTableFId) AS count'))->doToken ('%table', self::$objTextTable)
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
        ->doToken ('%what', new S ('COUNT(%objCategoryTableFId) AS count'))->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of comments defined in the database. An SQL condition can be passed, for example to limit the count
     * to a text identifier, or any other condition by that matter. We generally use this in the administration interface to
     * determine when we need to do pagination;
     *
     * @param S $objSQLCondition The passed SQL condition;
     */
    public function getCommentCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objCommentsTableFId) AS count'))->doToken ('%table', self::$objCommentsTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will check that the given text title exist, before trying to get data for it. This is the good wat to program a website,
     * where you leave the full administration for to the un-reliable user ...
     *
     * @param S $objTextTitle The title to check for ...
     * @return boolean will return true if the text exists ...
     */
    public function checkTextExistsByTitle (S $objTextTitle) {
        // Do return ...
        return $this->getTextInfoByTitle ($objTextTitle->entityEncode (ENT_QOTES),
        self::$objTextTableFSEO) instanceof A ? new B (FALSE) : new B (TRUE);
    }

    /**
     * Will check that a given text url exist, before trying to get data for it. This is the good way to program a website,
     * where you leave the full administration force to the un-reliable user ...
     *
     * @param S $objTextURL The URL to check ...
     * @return boolean Will return true if the text exists ...
     */
    public function checkTextExistsByURL (S $objTextURL) {
        // Do return ...
        return $this->getTextInfoByURL ($objTextURL->entityEncode (ENT_QUOTES),
        self::$objTextTableFTitle) instanceof A ? new B (FALSE) : new B (TRUE);
    }

    /**
     * Will check that the given category exists, by name, before trying to get the data for it. This is the good way to program
     * a website where you leave the full administration for to the un-reliable user ...
     *
     * @param S $objCategoryName The category name to check for ...
     * @param boolean Wil lreturn true if the category exists ...
     */
    public function checkCategoryExistsByName (S $objCategoryName) {
        // Do return ...
        return $this->getCategoryInfoByName ($objCategoryName->entityEncode (ENT_QUOTES),
        self::$objCategoryTableFName) instanceof A ? new B (FALSE) : new B (TRUE);
    }

    /**
     * Will check that a given category URL exists, before trying to get data for it. This is the good way to program a website
     * where you leave the full administration force to the un-reliable user ...
     *
     * @param S $objCategoryURL The category URL;
     * @return boolean Will return true if the category exists ...
     */
    public function checkCategoryExistsByURL (S $objCategoryURL) {
        // Do return ...
        return $this->getCategoryInfoByURL ($objCategoryURL->entityEncode (ENT_QUOTES),
        self::$objCategoryTableFName) instanceof A ? new B (FALSE) : new B (TRUE);
    }

    /**
     * Will return the articles, based on the specified condition;
     *
     * This method will return all the articles defined in the database, by taking the passed SQL condition as argument. If no
     * condition is specified, then it will return ALL defined articles in the table;
     *
     * @param S $objSQLCondition The SQL condition passed for articles to get;
     * @return array The result array;
     */
    public function getTexts (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return information about a given article id (the id is given as a string), while giving the corresponding field
     * as a parameter to the function. An easy way to get information about stored articles, without having too much of a
     * problem with getting information from them;
     *
     * @param S $objArticleId The article id to query for;
     * @param S $objFieldToGet The article field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getTextInfoById (S $objTextId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', new S ('WHERE %objTextTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objTextId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given article title, while giving the corresponding field as a parameter to the function.
     * An easy way to get information about stored articles, without having too much of a problem with getting information from
     * them;
     *
     * @param S $objArticleTitle The article title to query for;
     * @param S $objFieldToGet The article field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getTextInfoByTitle (S $objTextTitle, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', new S ('WHERE %objTextTableFTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objTextTitle))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given article URL, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored articles, without having too much of a problem with getting information for them;
     *
     * @param S $objTextURL The text URL;
     * @param S $objFieldToGet The field to get;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getTextInfoByURL (S $objTextURL, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', new S ('WHERE %objTextTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objTextURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return ALL texts by the given category id;
     *
     * This method will return all articles in a category id, giving us the possibility to return an array of all articles defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all articles are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined articles in that category;
     */
    public function getTextsByCategoryId (S $objCategoryId, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objTextTable)
        ->doToken ('%condition', new S ('WHERE %objTextTableFCategoryId = "%Id"'))
        ->doToken ('%Id', $objCategoryId)->appendString (_SP)
        ->appendString ($objSQLCondition == NULL ? new S : $objSQLCondition));
    }

    /**
     * Will return ALL texts by the given category name;
     *
     * This method will return all articles in a category name, giving us the possibility to returna an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category name;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getTextsByCategoryName (S $objCategoryName, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getTextsByCategoryId ($this
        ->getCategoryInfoByName ($objCategoryName,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return ALL texts defined by the given category URL; This can be used when displaying a category (section) of the
     * website and we need to take into consideration the SEO part of the URL, not the title which can contain quotes and other
     * things;
     *
     * @param S $objCategoryURL The category URL;
     * @return array Will return an array;
     */
    public function getTextsByCategoryURL (S $objCategoryURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getTextsByCategoryId ($this
        ->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId), $objSQLCondition);
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
     * easy way to get information about stored categories, without having too much of a problem with getting data from them;
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
     * Will return the category info by URL. URLs can differe from titles in the way that titles usually contain quotes,
     * which we don't want to happen here as it would cause SQL errors;
     *
     * @param S $objCategoryName The category name;
     * @param S $objFieldToGet The field to get;
     * @return array Will return an array that meets the sQL conditions;
     */
    public function getCategoryInfoByURL (S $objCategorySEO, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategorySEO))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return all defined comments, without taking in account a specific SQL condition. If passed, than this method will
     * return those comments that meet the specified SQL condition. We for example use this method in the administration interface
     * to show a DESC ordered list of last comments;
     *
     * @param S $objSQLCondition The passed SQL condition, if any;
     * @return array Will return the array of comments;
     */
    public function getComments (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objCommentsTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the field of information we requested for the specific comment. Can be used when showing information about
     * a comment in an array of retrieved comments. Quick way to figure out things as easy as possible;
     *
     * @param S $objCommentId The comment id;
     * @param S $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getCommentInfoById (S $objCommentId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCommentsTable)
        ->doToken ('%condition', new S ('WHERE %objCommentsTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCommentId))->offsetGet (0)->offsetGet ($objCommentId);
    }

    /**
     * Will return comments by a given text URL. We do this because of the fact that many and many people want SEO friendly URLs
     * we are actually concentrating on requesting information in a more specific manner, for example, by URL;
     *
     * @param S $objTextURL The text URL to query for;
     * @param S $objSQLCondition An SQL condition to be passed;
     * @return array Will return the array of comments defined for this text ...
     */
    public function getCommentsByTextURL (S $objTextURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objCommentsTable)
        ->doToken ('%condition', new S ('WHERE %objCommentsTableFTextId = "%Id"'))
        ->doToken ('%Id', $this->getTextInfoByURL ($objTextURL, self::$objTextTableFId))
        ->appendString (_SP)->appendString ($objSQLCondition == NULL ? new S : $objSQLCondition));
    }

    /**
     * Will return all approved comments for this URL. We do this because we need to moderate things a little before comments get
     * posted to a website.
     *
     * @param S $objTextURL The URL to query for;
     * @param S $objSQLCondition An appended SQL condition;
     * @return array Will return the array of approved comments defined for this text ...
     */
    public function getApprovedCommentsByTextURL (S $objTextURL, S $objSQLCondition = NULL) {
        // Set some requirements ...
        $objSQLCondition = $objSQLCondition == NULL ? new S : $objSQLCondition;

        // Do return ...
        return $this->getCommentsByTextURL ($objTextURL, $objSQLCondition
        ->prependString (_SP)->prependString ('AND %objCommentsTableFApproved = "Y"'));
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
                return parent::__CALL ($objFunctionName,
                $objFunctionArgs);
                break;
        }
    }

    /**
     * Will render a requested widget;
     *
     * This method is used to render a widget that usually is used in the frontend part of any website done with the help of this
     * platform. What are widgets you ask?! Well, it's quite simple. They are pieces of PHP code, usually tied to some
     * configuration options that control the way the widget functions or showns;
     *
     * Usually, configured widgets have enough power to be used in any way you want or need. For most of the times, the widgets
     * are called in the proper section of the frontend, but this method must permit the use of widgets, independent of the place
     * the developer needs them;
     *
     * @param $objW The widget to render;
     * @return mixed Depends on the widget;
     */
    public function renderWidget (S $objW, A $objWA = NULL) {
        // Make an empty array if NULL ...
        if ($objWA == NULL) $objWA = new A;

        // XML & RSS: Do a switch ...
        switch ($objW) {
            case 'widgetXML':
                // Yo man ... woohoooooo ...
                foreach ($this->getTexts (new S ('ORDER
                BY %objTextTableFDatePublished DESC')) as $k => $v) {
                    // Set some requirements ...
                    $objSEC = $this->getCategoryInfoById ($v[self::$objTextTableFCategoryId], self::$objCategoryTableFSEO);
                    $objDTE = date ('Y-m-d', (int) $v[self::$objTextTableFDatePublished]->toString ());
                    $objLOC = URL::staticURL (new A (Array (TEXTS_TEXTS_URL, FRONTEND_SECTION_URL)),
                    new A (Array ($v[self::$objTextTableFSEO], $objSEC)));

                    // Get the (INNER) CHILD of every young SEO freak ...
                    $objURL = $objWA['objXML']->addCHILD (Frontend::XML_URL);

                    // Set the XML Sitemap kids ...
                    $objURL->addCHILD (Frontend::XML_LOCATION, $objLOC);
                    $objURL->addCHILD (Frontend::XML_LAST_MOD, $objDTE);
                    $objURL->addCHILD (Frontend::XML_CHANGE_FREQ, self::XML_SITEMAP_FREQUENCY);
                    $objURL->addCHILD (Frontend::XML_PRIORITY, self::XML_SITEMAP_PRIORITY);
                }
                // BK;
                break;

            case 'widgetRSS':
                // Yo man ... woohoooooo ...
                if ($_GET[FRONTEND_FEED_URL] == __CLASS__) {
                    // Get'em 30 ...
                    foreach ($this->getTexts (new S ('ORDER BY %objTextTableFDatePublished
                    DESC LIMIT 0, 30')) as $k => $v) {
                        // Set some requirements ...
                        $objSEC = $this->getCategoryInfoById ($v[self::$objTextTableFCategoryId], self::$objCategoryTableFSEO);
                        $objDTE = date (DATE_RFC822, (int) $v[self::$objTextTableFDatePublished]->toString ());
                        $objLOC = URL::staticURL (new A (Array (TEXTS_TEXTS_URL, FRONTEND_SECTION_URL)),
                        new A (Array ($v[self::$objTextTableFSEO], $objSEC)));
                        $objDSC = $v[self::$objTextTableFContent]->entityEncode (ENT_QUOTES)
                        ->entityDecode (ENT_QUOTES)->stripTags ();
                        $objTTL = $v[self::$objTextTableFTitle];

                        // Get the (INNER) CHILD of every young SEO freak ...
                        $objURL = $objWA['objXML']->addCHILD (Frontend::RSS_ITEM);

                        // Set the RSS kids ...
                        $objURL->addCHILD (Frontend::RSS_TITLE, $objTTL);
                        $objURL->addCHILD (Frontend::RSS_LINK, $objLOC);
                        $objURL->addCHILD (Frontend::RSS_GUID, $objLOC);
                        $objURL->addCHILD (Frontend::RSS_PUBLISHED_DATE, $objDTE);
                        $objURL->addCHILD (Frontend::RSS_DESCRIPTION, $objDSC);
                    }
                }
                // BK;
                break;
        }

        // HTML: Do a switch ...
        switch ($objW) {
            case 'widgetCategoryList':
                // Set some requirements, if not set ...
                if ($objWA == NULL) { $objWA = new A; }

                // Get the category to start from ...
                if (isset ($objWA['start_from_category'])) {
                    // Get the category LIST;
                    $objCategoryList = $this->getCategories (NULL,
                    $objWA['start_from_category']);
                } else {
                    // Get the category LIST;
                    $objCategoryList = $this->getCategories (NULL,
                    NULL);
                }

                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                TPL::tpSet ($objCategoryList, new S ('objCategoryList'), $tpF);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($this, new S ('TXT'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'widgetList':
                // Do some checking ... before anything ...
                if (isset ($_GET[TEXTS_TEXTS_URL])) {
                    // Check or redirect ...
                    if ($this->checkTextExistsByURL ($_GET[TEXTS_TEXTS_URL])
                    ->toBoolean () == FALSE) {
                        // The text does not exist, than redirect to 404;
                        $this->setHeaderStr (new S (HDR::HEADER_MOVED_PERMANENTLY));
                        $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                        new A (Array ('404'))), new S ('Location'));
                    } else {
                        // Get the TEXT ...
                        $objTextCNT = $this->getTextInfoByURL ($_GET[TEXTS_TEXTS_URL], self::$objTextTableFContent);
                        $objTextTTL = $this->getTextInfoByURL ($_GET[TEXTS_TEXTS_URL], self::$objTextTableFTitle);
                        $objTextKEY = $this->getTextInfoByURL ($_GET[TEXTS_TEXTS_URL], self::$objTextTableFTags);
                        $objTextDSC = CLONE $objTextTTL;

                        // Set the title, keywords, description ...
                        TPL::manageTTL ($objTextTTL);
                        TPL::manageTAG (new S ('keywords'), $objTextKEY->entityEncode (ENT_QUOTES));
                        TPL::manageTAG (new S ('description'), $objTextDSC->entityEncode (ENT_QUOTES));

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . '-Item' . TPL_EXTENSION);
                        TPL::tpSet ($objTextTTL, new S ('objTextTTL'), $tpF);
                        TPL::tpSet ($objTextCNT, new S ('objTextCNT'), $tpF);
                        TPL::tpSet ($this, new S ('TXT'), $tpF);
                        TPL::tpExe ($tpF);
                    }
                } else {
                    // Get the CATEGORY ...
                    if ($this->checkCategoryExistsByURL ($_GET[FRONTEND_SECTION_URL])->toBoolean () == FALSE) {
                        // The category does not exist, than redirect to 404;
                        $this->setHeaderStr (new S (HDR::HEADER_MOVED_PERMANENTLY));
                        $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                        new A (Array ('404'))), new S ('Location'));
                    } else {
                        // Get something from db ...
                        $objCategoryArray = $this->getTextsByCategoryURL ($_GET[FRONTEND_SECTION_URL]);

                        // Check how many texts are defined for it ...
                        switch ($objCategoryArray->doCount ()->toInt ()) {
                            // If we have only ONE text, defined for a category,
                            // we then recommend moving to that text ...
                            case 1:
                                // MOVED ... permanently ...
                                $this->setHeaderStr (new S (HDR::HEADER_MOVED_PERMANENTLY));
                                $this->setHeaderKey (URL::rewriteURL (new A (Array (TEXTS_TEXTS_URL, FRONTEND_SECTION_URL)),
                                new A (Array ($objCategoryArray[0][self::$objTextTableFSEO],
                                $_GET[FRONTEND_SECTION_URL]))), new S ('Location'));
                                break;

                            // In case we don't have any text defined,
                            // we recommend showing a default message.
                            case 0:
                                break;

                            // And finally, if we have more than one, then
                            // we recommend showing a LIST, by default reversed ...
                            default:
                                // Set the title, keywords, description ...
                                TPL::manageTTL ($objCategoryNME = $this->getCategoryInfoByURL ($objCategoryURL =
                                $_GET[FRONTEND_SECTION_URL], self::$objCategoryTableFName));
                                TPL::manageTAG (new S ('description'), $objCategoryNME);

                                // Set the template file ...
                                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                                TPL::tpSet ($objCategoryArray->arrayReverse (), new S ('objCategoryArray'), $tpF);
                                TPL::tpSet ($objCategoryURL, new S ('objCategory'), $tpF);
                                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                                TPL::tpSet ($this, new S ('TXT'), $tpF);
                                TPL::tpExe ($tpF);
                                break;
                        }
                    }
                }
                // Break out ...
                break;

            case 'widgetComments':
                // Check if we have the proper URL enabled ...
                if (isset ($_GET[TEXTS_TEXTS_URL])) {
                    // Check if the comments are enabled ...
                    if ($this->getTextInfoByURL ($objURL = $_GET[TEXTS_TEXTS_URL],
                    self::$objTextTableFCanComment) == 'Y') {
                        // Set some requirements ...
                        $objCommentIsOk = new S;
                        $objComments = $this->getApprovedCommentsByTextURL ($_GET[TEXTS_TEXTS_URL],
                        new S ('ORDER BY %objCommentsTableFDate DESC'));

                        // Check for status ...
                        if (isset ($_GET[TEXTS_STATUS_URL])) {
                            if ($_GET[TEXTS_STATUS_URL] == TEXTS_STATUS_OK_URL) {
                                $objCommentIsOk = new S ($objWA['comment_has_been_added']);
                            }
                        }

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                        TPL::tpSet ($objCommentIsOk, new S ('objCommentIsOk'), $tpF);
                        TPL::tpSet ($objComments, new S ('objComments'), $tpF);
                        TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                        TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                        TPL::tpExe ($tpF);

                        // Set some requirements ...
                        $objShowFrm = new B (TRUE);

                        // Check if we're allowed to show the comment form ...
                        if ($this->getConfigKey (new S ('texts_settings_authenticated_to_comment')) == 'Y') {
                            if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                                $objShowFrm = new B (TRUE);
                            } else {
                                $objShowFrm = new B (FALSE);
                            }
                        }

                        // Do some work ...
                        if ($objShowFrm->toBoolean () == TRUE) {
                            if ($this->checkPOST (self::$objCommentsTableFComment)->toBoolean () == TRUE) {
                                if ($this->getPOST (self::$objCommentsTableFComment)->toLength ()->toInt () == 0) {
                                    $this->setErrorOnInput (self::$objCommentsTableFComment,
                                    new S ($objWA['error_no_comment']));
                                }
                            }

                            if ($this->checkPOST (self::$objCommentsTableFName)->toBoolean () == TRUE) {
                                if ($this->getPOST (self::$objCommentsTableFName)->toLength ()->toInt () == 0) {
                                    $this->setErrorOnInput (self::$objCommentsTableFName,
                                    new S ($objWA['error_no_name']));
                                }
                            }

                            if ($this->checkPOST (self::$objCommentsTableFEML)->toBoolean () == TRUE) {
                                if ($this->getPOST (self::$objCommentsTableFEML)->toLength ()->toInt () == 0) {
                                    $this->setErrorOnInput (self::$objCommentsTableFEML,
                                    new S ($objWA['error_no_email']));
                                }
                            }

                            // Make the form ... (ya, outside the box ...);
                            $this->setMethod (new S ('POST'))
                            ->setFieldset (new S ($objWA['comment_add']))
                            ->setSQLAction (new S ('update'))
                            ->setTableName (self::$objCommentsTable)
                            ->setUpdateId (new S ('#nextTableAutoIncrement'))
                            ->setUpdateField (self::$objCommentsTableFId);
                            if ($this->checkPOST (self::$objCommentsTableFComment)->toBoolean () == TRUE)
                            $this->setRedirect (URL::rewriteURL (new A (Array (TEXTS_STATUS_URL)),
                            new A (Array (TEXTS_STATUS_OK_URL))));
                            $this->setName (new S ('commentForm'))
                            ->setExtraUpdateData (self::$objCommentsTableFDate, new S ((string) time ()))
                            ->setExtraUpdateData (self::$objCommentsTableFTextId, $this
                            ->getTextInfoByURL ($_GET[TEXTS_TEXTS_URL], self::$objTextTableFId))
                            ->setInputType (new S ('submit'))
                            ->setValue (new S ($objWA['comment_submit']))
                            ->setName (new S ('submit'))
                            ->setContainerDiv (new B (TRUE));

                            // Check if the user is authenticated ...
                            if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                                // Set the RUId;
                                $this->setExtraUpdateData (self::$objCommentsTableFRUId, $this->ATH
                                ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId));
                            } else {
                                // Set the other infos;
                                $this->setInputType (new S ('text'))
                                ->setName (self::$objCommentsTableFName)
                                ->setLabel (new S ($objWA['comment_name']))
                                ->setContainerDiv (new B (TRUE))
                                ->setInputType (new S ('text'))
                                ->setName (self::$objCommentsTableFEML)
                                ->setLabel (new S ($objWA['comment_email']))
                                ->setContainerDiv (new B (TRUE))
                                ->setInputType (new S ('text'))
                                ->setName (self::$objCommentsTableFURL)
                                ->setLabel (new S ($objWA['comment_website']))
                                ->setContainerDiv (new B (TRUE));
                            }

                            // Continue ...
                            $this->setInputType (new S ('textarea'))
                            ->setName (self::$objCommentsTableFComment)
                            ->setLabel (new S ($objWA['comment_message']))
                            ->setRows (new S ('10'))
                            ->setTinyMCETextarea (new B (TRUE))
                            ->setClass (new S ('tinyMCESimple'))
                            ->setContainerDiv (new B (TRUE));

                            // Notify ...
                            if ($this->checkFormHasErrors()->toBoolean () == FALSE &&
                            $this->checkPOST (self::$objCommentsTableFComment)->toBoolean () == TRUE) {
                                if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                                    // Query the authentication ...
                                    $objUSR = $this->ATH
                                    ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFUName);
                                } else {
                                    // Or trust the FORM ...
                                    $objUSR = $this->getPOST (self::$objCommentsTableFName);
                                }

                                // Go and SP ... MAIL me ...
                                $objMAIL = new MAIL;
                                $objMAIL->doMAIL ($this->STG->getConfigKey (new S ('settings_website_notification_email')),
                                new S (TEXTS_COMMENT_HAS_BEEN_POSTED), $this->getHELP (new S ('widgetCommentsEMLNotifyAdmin'))
                                ->doToken ('%u', $objUSR));

                                // Go deeper and notify them users ...
                                $objCommentsForItem = $this->getCommentsByTextURL ($objURL);
                                foreach ($objCommentsForItem as $k => $v) {
                                    $objMAIL = new MAIL;
                                    $objMAIL->doMAIL ($this->ATH->getUserInfoById ($v[Texts::$objCommentsTableFRUId],
                                    Authentication::$objAuthUsersTableFEML), new S (TEXTS_COMMENT_HAS_BEEN_POSTED),
                                    $this->getHELP (new S ('widgetCommentsEMLNotifyUsers'))
                                    ->doToken ('%u', $objUSR)->doToken ('%k', URL::rewriteURL ()));
                                }
                            }

                            // End form and execute ...
                            $this->setFormEndAndExecute (new B (TRUE));
                        }
                    }
                }
                // Break out ...
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
            case 'manageTexts':
                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Switch between actions;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('textEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('textErase'));
                            break;
                    }
                } else {
                    // Redirect to DescByPublished ...
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescByPublished'))), new S ('Location'));

                    // Set some requirements ...
                    $objGetCondition = new S;

                    if (isset ($_GET[ADMIN_ACTION_BY])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_BY]) {
                            case TEXTS_SEARCH_TITLE:
                                $objGetCondition->appendString (_SP)
                                ->appendString ('WHERE %objTextTableFTitle');
                                break;

                            case TEXTS_SEARCH_CONTENT:
                                $objGetCondition->appendString (_SP)
                                ->appendString ('WHERE %objTextTableFContent');
                                break;
                        }

                        // Add LIKE searching ...
                        $objGetCondition->appendString (_SP)->appendString ('LIKE "%%Search%"')
                        ->doToken ('%Search', $_GET[ADMIN_ACTION_SEARCH]);

                        // Get the count ...
                        $objSearchCount = $this->getTextCount ($objGetCondition);
                    }

                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByTitle':
                            case 'DescByTitle':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objTextTableFTitle');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByCategory':
                            case 'DescByCategory':
                                if (isset ($_GET[ADMIN_ACTION_BY])) {
                                    // Make the ordered condition;
                                    $objGetCondition->doToken ('WHERE', _SP . 'AS t1 INNER JOIN %objCategoryTable AS t2
                                    ON t1.%objTextTableFCategoryId = t2.%objCategoryTableFId WHERE');
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('ORDER BY t2.%objCategoryTableFName');
                                } else {
                                    // Make the ordered condition;
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('AS t1 INNER JOIN %objCategoryTable AS t2
                                    ON t1.%objTextTableFCategoryId = t2.%objCategoryTableFId
                                    ORDER BY t2.%objCategoryTableFName');
                                }

                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByCategory':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByCategory':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByPublished':
                            case 'DescByPublished':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objTextTableFDatePublished');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByPublished':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByPublished':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByUpdated':
                            case 'DescByUpdated':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objTextTableFDateUpdated');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByUpdated':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByUpdated':
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

                    // Set some requirements;
                    $objTexts = $this->getTexts ($objGetCondition);
                    if (isset ($_GET[ADMIN_ACTION_BY])) { $objTextsCount = $objSearchCount; }
                    else { $objTextsCount = $this->getTextCount (); }

                    // Fix pagination when count is LESS than 10;
                    if (isset ($_GET[ADMIN_ACTION_BY]) && isset ($_GET[ADMIN_PAGINATION])) {
                        if ($objTextsCount->toInt () < 10) {
                            // Remove paging ... & redirect to proper ...
                            TPL::setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGINATION))), new S ('Location'));
                        } else {
                            if (CEIL ($objTextsCount->toInt () / 10) < (int) $_GET[ADMIN_PAGINATION]->toString ()) {
                                // Redirect to proper ...
                                TPL::setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGINATION)),
                                new A (Array (CEIL ($objTextsCount->toInt () / 10)))), new S ('Location'));
                            }
                        }
                    }

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageTexts.tp');
                    TPL::tpSet ($objTexts, new S ('articleTable'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpSet ($this, new S ('TXT'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination ...
                    if ($objTextsCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objTextsCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('textSearch'));
                    $this->renderForm (new S ('textCreate'));
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
                            $this->renderForm (new S ('textCategoryEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('textCategoryErase'));
                            break;

                        case ADMIN_ACTION_MOVE:
                            $this->renderForm (new S ('textCategoryMove'));
                            break;
                    }
                } else {
                    // Set some requirements;
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
                                $objGetCondition->appendString (_SP)
                                ->appendString ('DESC');
                                break;
                        }
                    }

                    // Add some LIMITs;
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objCategoryTreeCount = $this->getCategoryCount ();
                    $objCategoryTree = $this->getCategories (isset ($_GET[ADMIN_SHOW_ALL]) ? new S : $objGetCondition);

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageCategories.tp');
                    TPL::tpSet ($objCategoryTree, new S ('categoryTree'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination ...
                    if ($objCategoryTreeCount->toInt () > 10 && !isset ($_GET[ADMIN_SHOW_ALL]))
                    self::$objAdministration->setPagination ($objCategoryTreeCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('textCategoryCreate'));
                }
                // Break out ...
                break;

            case 'manageComments':
                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Switch between actions;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('commentEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('commentErase'));
                            break;
                    }
                } else {
                    // Do a redirect ...
                    if (!isset ($_GET[ADMIN_ACTION_SORT])) {
                        $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                        new A (Array ('DescByDate'))), new S ('Location'));
                    }

                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByName':
                            case 'DescByName':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objCommentsTableFName');
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
                                break;

                            case 'AscByTitle':
                            case 'DescByTitle':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('AS t1 LEFT JOIN %objTextTable
                                AS t2 ON t1.%objCommentsTableFTextId = t2.%objTextTableFId
                                ORDER BY t2.%objTextTableFTitle');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByApproved':
                            case 'DescByApproved':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objCommentsTableFApproved');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByApproved':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByApproved':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByDate':
                            case 'DescByDate':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objCommentsTableFDate');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByDate':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByDate':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objComments = $this->getComments ($objGetCondition);
                    $objCommentsCount = $this->getCommentCount ();

                    // Process them arrays;
                    foreach ($objComments as $k => $v) {
                        if ($v[self::$objCommentsTableFRUId] != new S ('0')) {
                            $v[self::$objCommentsTableFName] = $this->ATH
                            ->getUserInfoById ($v[self::$objCommentsTableFRUId],
                            Authentication::$objAuthUsersTableFUName);
                        }
                    }

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageComments.tp');
                    TPL::tpSet ($objComments, new S ('articleTable'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpSet ($this, new S ('TXT'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination ...
                    if ($objCommentsCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objCommentsCount);
                }
                // Break out ...
                break;

            case 'manageOperations':
                // Do the form, make it happen;
                $this->renderForm (new S ('categoryMoveOperation'));
                break;

            case 'manageConfiguration':
                // Do the form, make it happen;
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
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality best;
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderForm (S $objFormToRender, A $objFA = NULL) {
        // Make them defaults ...
        if ($objFA == NULL) $objFA = new A;

        // Do a switch ...
        switch ($objFormToRender) {
            case 'textSearch':
                // Get some predefines;
                if (isset ($_GET[ADMIN_ACTION_BY]))     { $objSearchBy  = $_GET[ADMIN_ACTION_BY]; } else { $objSearchBy = new S; }
                if (isset ($_GET[ADMIN_ACTION_SEARCH])) { $objSearchWas = $_GET[ADMIN_ACTION_SEARCH]; }
                else { $objSearchWas = new S;   }

                // Do some work;
                if ($this->checkPOST (new S ('search_submit'))->toBoolean () == TRUE) {
                    if ($this->getPOST (new S ('search_by'))->toLength ()->toInt () == 0) {
                        if (isset ($_GET[ADMIN_ACTION_SEARCH])) {
                            // Erase search terms ...
                            $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SEARCH,
                            ADMIN_ACTION_BY))), new S ('Location'));
                        }

                        // Set an input error;
                        $this->setErrorOnInput (new S ('search_by'),
                        new S (ARTICLE_SEARCH_FIELD_IS_EMPTY));

                        // Unset the post ...
                        $this->unsetPOST ();
                    } else {
                        // Get what to search and where ...
                        $objWhatToSearch  = $this->getPOST (new S ('search_by'));
                        $objWhereToSearch = $this->getPOST (new S ('search_field'));

                        // And go there ...
                        $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SEARCH, ADMIN_ACTION_BY)),
                        new A (Array ($objWhatToSearch, $objWhereToSearch))), new S ('Location'));
                    }
                }

                // Check the option that has been selected;
                $objWasSelected = new A (Array (new B ($objSearchBy == TEXTS_SEARCH_TITLE ? TRUE : FALSE),
                new B ($objSearchBy == TEXTS_SEARCH_CONTENT   ? TRUE : FALSE)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (TEXTS_SEARCH_BY))
                ->setName ($objFormToRender)
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('search_by'))
                ->setvalue ($objSearchWas)
                ->setLabel (new S (TEXTS_SEARCH_BY))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('search_field'))
                ->setContainerDiv (new B (TRUE))
                ->setLabel (new S (TEXTS_SEARCH_IN))
                ->setInputType (new S ('option'))
                ->setName (new S ('article_title'))
                ->setValue (new S (TEXTS_SEARCH_TITLE))
                ->setLabel (new S (TEXTS_SEARCH_TITLE))
                ->setSelected ($objWasSelected[0])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_content'))
                ->setValue (new S (TEXTS_SEARCH_CONTENT))
                ->setLabel (new S (TEXTS_SEARCH_CONTENT))
                ->setSelected ($objWasSelected[1])
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setValue (new S (TEXTS_SEARCH_BY))
                ->setName (new S ('search_submit'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'textCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objTextTableFTitle)
                ->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objTextTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objTextTableFTitle,
                        new S (TEXTS_TITLE_CANNOT_BE_EMPTY));
                    } else {
                        // Set some requirements ...
                        $objToCheck = $this->getPOST (self::$objTextTableFTitle);

                        // Check title is unique;
                        if ($this->checkTextTitleIsUnique ($objToCheck)
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objTextTableFTitle,
                            new S (TEXTS_TITLE_MUST_BE_UNIQUE));
                        }

                        // Check URL is unique;
                        if ($this->checkTextURLIsUnique (URL::getURLFromString (CLONE $objToCheck))
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objTextTableFTitle,
                            new S (TEXTS_URL_MUST_BE_UNIQUE));
                        }
                    }

                    if ($this->getPOST (self::$objTextTableFContent)
                    ->toLength ()->toInt () == 0)
                    $this->setErrorOnInput (self::$objTextTableFContent,
                    new S (TEXTS_CONTENT_CANNOT_BE_EMPTY));
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setFieldset (new S (TEXTS_ADD_ARTICLE))
                ->setRedirect ($objURLToGoBack)
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objTextTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objTextTableFId);

                if ($this->checkPOST (self::$objTextTableFTitle)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objTextTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objTextTableFTitle)));
                }

                // ONLY if != BIG-MAN;
                if ((int) $this->ATH->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId)
                ->toString () != 1) {
                    $this->setExtraUpdateData (self::$objTextTableAuthorId, $this->ATH
                    ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId));
                }

                // Add the SEO ...
                $this->setExtraUpdateData (self::$objTextTableFDatePublished, new S ((string) $_SERVER['REQUEST_TIME']))
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_ADD_ARTICLE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objTextTableFTags)
                ->setLabel (new S (TEXTS_TAGS))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TAGS))
                ->setContainerDiv (new B (TRUE));

                // If we're the BIG MAN, we can set the author of an entry;
                if ((int) $this->ATH->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId)
                ->toString () == 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objTextTableFAuthorId)
                    ->setLabel (new S (TEXTS_AUTHOR))
                    ->setContainerDiv (new B (TRUE));

                    // Categories ...
                    foreach ($this->ATH->getUsers () as $k => $v) {
                        $this->setInputType (new S ('option'))
                        ->setName  ($v[Authentication::$objAuthUsersTableFId])
                        ->setValue ($v[Authentication::$objAuthUsersTableFId])
                        ->setLabel ($v[Authentication::$objAuthUsersTableFUName]);
                    }
                }

                $this->setInputType (new S ('select'))
                ->setLabel (new S (TEXTS_CATEGORY_NAME_LABEL))
                ->setName (self::$objTextTableFCategoryId)
                ->setContainerDiv (new B (TRUE));

                // Categories;
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objCategoryTableFId])
                    ->setValue ($v[self::$objCategoryTableFId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int)
                    $v['depth']->toString ()) . $v[self::$objCategoryTableFName]));
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setName (self::$objTextTableFCanComment)
                ->setLabel (new S (TEXTS_CAN_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (TEXTS_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (TEXTS_CAN_COMMENT_YES))
                ->setInputType (new S ('text'))
                ->setName (self::$objTextTableFTitle)
                ->setLabel (new S (TEXTS_TITLE))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objTextTableFContent)
                ->setLabel (new S (TEXTS_CONTENT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'textEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objTextTableFTitle)
                    ->toLength ()->toInt () == 0)
                    $this->setErrorOnInput (self::$objTextTableFTitle,
                    new S (TEXTS_TITLE_CANNOT_BE_EMPTY));

                    if ($this->getPOST (self::$objTextTableFContent)
                    ->toLength ()->toInt () == 0)
                    $this->setErrorOnInput (self::$objTextTableFContent,
                    new S (TEXTS_CONTENT_CANNOT_BE_EMPTY));
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (TEXTS_EDIT_ARTICLE))
                ->setAJAXEnabledForm (new B (FALSE))
                ->setRedirect ($objURLToGoBack)
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objTextTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objTextTableFId)
                ->setName ($objFormToRender)
                ->setExtraUpdateData (self::$objTextTableFDateUpdated, new S ((string) $_SERVER['REQUEST_TIME']))
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_EDIT_ARTICLE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objTextTableFTags)
                ->setLabel (new S (TEXTS_TAGS))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TAGS));

                // Add the SEO ...
                if ($this->checkPOST (self::$objTextTableFTitle)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objTextTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objTextTableFTitle)));
                }

                // If we're the BIG MAN, we can set the author of an entry;
                if ((int) $this->ATH->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId)
                ->toString () == 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objTextTableFAuthorId)
                    ->setLabel (new S (TEXTS_AUTHOR))
                    ->setContainerDiv (new B (TRUE));

                    // Users ...
                    foreach ($this->ATH->getUsers () as $k => $v) {
                        $this->setInputType (new S ('option'))
                        ->setName  ($v[Authentication::$objAuthUsersTableFId])
                        ->setValue ($v[Authentication::$objAuthUsersTableFId])
                        ->setLabel ($v[Authentication::$objAuthUsersTableFUName]);
                    }
                }

                $this->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objTextTableFCategoryId)
                ->setLabel (new S (TEXTS_CATEGORY_NAME_LABEL))
                ->setContainerDiv (new B (TRUE));

                // Categories ...
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objCategoryTableFId])
                    ->setValue ($v[self::$objCategoryTableFId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int)
                    $v['depth']->toString ()) . $v[self::$objCategoryTableFName]));
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setName (self::$objTextTableFCanComment)
                ->setLabel (new S (TEXTS_CAN_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (TEXTS_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (TEXTS_CAN_COMMENT_YES))
                ->setInputType (new S ('text'))
                ->setName (self::$objTextTableFTitle)
                ->setLabel (new S (TEXTS_TITLE))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objTextTableFContent)
                ->setLabel (new S (TEXTS_CONTENT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'textErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objTextTable)
                ->doToken ('%condition', new S ('%objTextTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // And ALL associated comments ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objCommentsTable)
                ->doToken ('%condition', new S ('%objCommentsTableFTextId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'commentEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (TEXTS_EDIT_COMMENT))
                ->setAJAXEnabledForm (new B (FALSE))
                ->setRedirect ($objURLToGoBack)
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCommentsTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCommentsTableFId)
                ->setName ($objFormToRender)
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_EDIT_COMMENT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objCommentsTableFApproved)
                ->setLabel (new S (TEXTS_COMMENT_APPROVED))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (TEXTS_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (TEXTS_CAN_COMMENT_YES))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCommentsTableFComment)
                ->setLabel (new S (TEXTS_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'commentErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objCommentsTable)
                ->doToken ('%condition', new S ('%objCommentsTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'textCategoryCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work ...
                if ($this->checkPOST (new S ('categories_show_all'))->toBoolean () == TRUE) {
                    // Redirect to proper ...
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_SHOW_ALL)),
                    new A (Array ('1'))), new S ('Location'));
                }

                // Do some work;
                if ($this->checkPOST (new S ('add_category_submit'))
                ->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    if ($this->getPOST (new S ('add_category'))
                    ->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it;
                        $this->setErrorOnInput (new S ('add_category'),
                        new S (TEXTS_CATEGORY_NAME_CANNOT_BE_EMPTY));

                        // Set to memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($this
                        ->getPOST (new S ('add_category')))->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (TEXTS_CATEGORY_ALREADY_EXISTS));

                            // Set to memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;
                        switch ($this->getPOST (new S ('add_category_as_what'))) {
                            case TEXTS_CATEGORY_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::FIRST_CHILD);
                                break;

                            case TEXTS_CATEGORY_LAST_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::LAST_CHILD);
                                break;

                            case TEXTS_CATEGORY_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::PREVIOUS_BROTHER);
                                break;

                            case TEXTS_CATEGORY_NEXT_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::NEXT_BROTHER);
                                break;
                        }

                        // Add the node;
                        self::$objMPTT->mpttAddNode ($this->getPOST (new S ('add_category')),
                        $this->getPOST (new S ('add_category_parent_or_bro')), $objAddNodeAS);

                        // Redirect back;
                        $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (TEXTS_ADD_CATEGORY))
                ->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (new S (TEXTS_SHOW_ALL_CATEGORIES))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_submit'))
                ->setValue (new S (TEXTS_ADD_CATEGORY))
                ->setInputType (new S ('text'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category'))
                ->setLabel (new S (TEXTS_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_CATEGORY))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_as_what'))
                ->setLabel (new S (TEXTS_AS_A))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_child'))
                ->setLabel (new S (TEXTS_CATEGORY_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_child'))
                ->setLabel (new S (TEXTS_CATEGORY_LAST_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_brother'))
                ->setLabel (new S (TEXTS_CATEGORY_BROTHER))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_brother'))
                ->setLabel (new S (TEXTS_CATEGORY_NEXT_BROTHER))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_parent_or_bro'))
                ->setLabel (new S (TEXTS_OF_CATEGORY));

                // Categories ...
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objCategoryTableFName])
                    ->setValue ($v[self::$objCategoryTableFName])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int)
                    $v['depth']->toString ()) . $v[self::$objCategoryTableFName]));
                }

                // Execute the form;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'textCategoryEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    if ($this->getPOST (self::$objCategoryTableFName)
                    ->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it;
                        $this->setErrorOnInput (self::$objCategoryTableFName,
                        new S (CATEGORY_NAME_CANNOT_BE_EMPTY));

                        // Set to memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($this
                        ->getPOST (self::$objCategoryTableFName))->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (self::$objCategoryTableFName,
                            new S (TEXTS_CATEGORY_ALREADY_EXISTS));

                            // Set to memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();
                } else {
                    // Do nothing ...
                    $objFormHappened = new B (TRUE);
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCategoryTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCategoryTableFId)
                ->setFieldset (new S (TEXTS_EDIT_CATEGORY));
                if ($objFormHappened->toBoolean () == FALSE)
                $this->setRedirect ($objURLToGoBack, new S ('Location'));
                $this->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE));

                // Add the SEO ...
                if ($this->checkPOST (self::$objCategoryTableFName)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objCategoryTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objCategoryTableFName)));
                }

                $this->setInputType (new S ('submit'))
                ->setName (new S ('edit_category_submit'))
                ->setValue (new S (TEXTS_EDIT_CATEGORY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objCategoryTableFName)
                ->setLabel (new S (TEXTS_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'textCategoryErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Memorize if it has kids;
                $objNodeHasKids = new B (FALSE);
                if ($this->getTextCount (_S ('WHERE %objTextTableFCategoryId = "%Id"')
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))->toInt () != 0) {
                    // Set an error message;
                    self::$objAdministration->setErrorMessage (new S (TEXTS_CANNOT_DELETE_CATEGORY_WA), $objURLToGoBack);
                } else {
                    // Do erase the group node from the table;
                    self::$objMPTT->mpttRemoveNode ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName));

                    // Do a redirect, and get the user back where he belongs;
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                break;

            case 'textCategoryMove':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_TO, ADMIN_ACTION_TYPE)));

                // Get names, as they are unique;
                $objThatIsMoved = $this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID], self::$objCategoryTableFName);
                $objWhereToMove = $this->getCategoryInfoById ($_GET[ADMIN_ACTION_TO], self::$objCategoryTableFName);
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
                    self::$objAdministration->setErrorMessage (new S (TEXTS_CATEGORY_MOVED_TO_CHILD), $objURLToGoBack);
                } else {
                    // Move nodes;
                    self::$objMPTT->mpttMoveNode ($objThatIsMoved, $objWhereToMove, $_GET[ADMIN_ACTION_TYPE]);
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // Break out ...
                break;

            case 'categoryMoveOperation':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_SUBPAGE)), new A (Array (MANAGE_TEXTS)));

                // Do some work;
                ($this->checkPOST ()->toBoolean () == TRUE) ?
                ($objOLDCategoryId = $this->getPOST (new S ('old_category_id'))) :
                ($objOLDCategoryId = new S ('0'));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (TEXTS_MOVE_ARTICLE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objTextTable)
                ->setUpdateField (self::$objTextTableFId)
                ->setUpdateWhere ($this->doModuleToken (_S ('%objTextTableFCategoryId = "%Id"')
                ->doToken ('%Id', $objOLDCategoryId)))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_MOVE_ARTICLE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('old_category_id'))
                ->setLabel (new S (TEXTS_OLD_CATEGORY))
                ->setContainerDiv (new B (TRUE));

                // Categories ...
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) $v['depth']->toString ()) .
                    $v[self::$objMPTT->objNameOfNode]));
                }

                // Continue ...
                $this->setInputType (new S ('select'))
                ->setName (self::$objTextTableFCategoryId)
                ->setLabel (new S (TEXTS_NEW_CATEGORY))
                ->setContainerDiv (new B (TRUE));

                // Categories, again ...
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int)
                    $v['depth']->toString ()) . $v[self::$objMPTT->objNameOfNode]));
                }

                // Continue ...
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit':
                // Set the URL to go back too;
                $objURLToGoBack = new S;

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)),
                    new A (Array ($this->getPOST (new S ('what')))));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (MANAGE_TEXTS_CONFIG))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_CONFIG_DO))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S (TEXTS_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEditMustBeAuthToComment'))
                ->setValue (new S ('configurationEditMustBeAuthToComment'))
                ->setLabel (new S (TEXTS_USER_MUST_BE_LOGGED_TO_COMMENT))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEditTextsPerCategory'))
                ->setValue (new S ('configurationEditTextsPerCategory'))
                ->setLabel (new S (TEXTS_PER_CATEGORY))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEditMustBeAuthToComment':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (TEXTS_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('texts_settings_authenticated_to_comment'))
                ->setLabel (new S (TEXTS_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S ('Yes'))
                ->setSelected ($this
                ->getConfigKey (new S ('texts_settings_authenticated_to_comment'))
                == 'Y' ? new B (TRUE) : new B (FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue (new S ('N'))
                ->setLabel (new S ('No'))
                ->setSelected ($this
                ->getConfigKey (new S ('texts_settings_authenticated_to_comment'))
                == 'N' ? new B (TRUE) : new B (FALSE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEditTextsPerCategory':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (TEXTS_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (TEXTS_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('texts_settings_per_page'))
                ->setLabel (new S (TEXTS_PER_PAGE))
                ->setValue ($this->getConfigKey (new S ('texts_settings_per_page')))
                ->setRegExpType (new S ('ereg'))
                ->setRegExpErrMsg (new S (TEXTS_CONFIG_PER_PAGE_ERROR))
                ->setPHPRegExpCheck (new S ('[0-9]'))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
