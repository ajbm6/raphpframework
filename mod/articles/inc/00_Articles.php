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

############# Motto: "Your news, 24h per day, 7 days per week, 365 days per year!";
class Articles extends ICommonExtension implements IFaceArticles {
    /* OBJECT: Identity */
    protected static $objName                       = 'Articles :: RA PHP Framework';
    protected $objIdentificationString              = __CLASS__;

    /* TABLE: Articles */
    public static $objArticleTable                  = NULL;
    public static $objArticleTableFId               = NULL;
    public static $objArticleTableFTitle            = NULL;
    public static $objArticleTableFSEO              = NULL;
    public static $objArticleTableFExcerpt          = NULL;
    public static $objArticleTableFTags             = NULL;
    public static $objArticleTableFContent          = NULL;
    public static $objArticleTableFDatePublished    = NULL;
    public static $objArticleTableFDateUpdated      = NULL;
    public static $objArticleTableFState            = NULL;
    public static $objArticleTableFAuthorId         = NULL;
    public static $objArticleTableFCategoryId       = NULL;
    public static $objArticleTableFCanComment       = NULL;
    public static $objArticleTableFViews            = NULL;

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
    public static $objCommentsTableFArticleId       = NULL;

    /* TABLE: Categories */
    public static $objCategoryTable                 = NULL;
    public static $objCategoryTableFId              = NULL;
    public static $objCategoryTableFName            = NULL;
    public static $objCategoryTableFSEO             = NULL;
    public static $objCategoryTableFDescription     = NULL;
    public static $objCategoryTableFDate            = NULL;

    /* TABLE: Configuration && MPTT */
    public static $objItemsPerPage                  = NULL;
    protected static $objMPTT                       = NULL;
    
	/* STATEs */
    const STATE_DRAFT                               = 1;
    const STATE_PUBLISHED                           = 2;
    const STATE_PENDING_REVIEW                      = 3;
    const STATE_STICKY                              = 4;

    /* REGEXPses */
    const REGEXP_JS_TAGS                            = '[^a-zA-Z0-9 ,_-]';
    const REGEXP_JS_TITLE                           = '[^a-zA-Z0-9 ,_\.\"\?\!\@\#\$\%\^\&\*\~\:\; \(\)\|\-]';
    const REGEXP_JS_CATEGORY                        = '[^a-zA-Z0-9 ,_\.\"\?\!\@\#\$\%\^\&\*\~\:\; \(\)\|\-]';

    /* CONSTANTS: ALL */
    const XML_SITEMAP_PRIORITY                      = '0.4';
    const XML_SITEMAP_FREQUENCY                     = 'monthly';

    // CONSTRUCT;
    public function __construct () {
        // Construct any possible parent, parse the configuration meanwhile;
        parent::__construct ();

        // Tie in common configuration;
        $this->tieInCommonConfiguration ();

        // Articles ...
        self::$objArticleTable                  = $this->getConfigKey (new S ('articles_table'));
        self::$objArticleTableFId               = $this->getConfigKey (new S ('articles_table_field_id'));
        self::$objArticleTableFTitle            = $this->getConfigKey (new S ('articles_table_field_title'));
        self::$objArticleTableFSEO              = $this->getConfigKey (new S ('articles_table_field_seo'));
        self::$objArticleTableFExcerpt          = $this->getConfigKey (new S ('articles_table_field_excerpt'));
        self::$objArticleTableFTags             = $this->getConfigKey (new S ('articles_table_field_tags'));
        self::$objArticleTableFContent          = $this->getConfigKey (new S ('articles_table_field_content'));
        self::$objArticleTableFDatePublished    = $this->getConfigKey (new S ('articles_table_field_date_published'));
        self::$objArticleTableFDateUpdated      = $this->getConfigKey (new S ('articles_table_field_date_updated'));
        self::$objArticleTableFState            = $this->getConfigKey (new S ('articles_table_field_state'));
        self::$objArticleTableFAuthorId         = $this->getConfigKey (new S ('articles_table_field_author_id'));
        self::$objArticleTableFCategoryId       = $this->getConfigKey (new S ('articles_table_field_category_id'));
        self::$objArticleTableFCanComment       = $this->getConfigKey (new S ('articles_table_field_can_comment'));
        self::$objArticleTableFViews            = $this->getConfigKey (new S ('articles_table_field_views'));

        // Comments ...
        self::$objCommentsTable                 = $this->getConfigKey (new S ('articles_comments_table'));
        self::$objCommentsTableFId              = $this->getConfigKey (new S ('articles_comments_table_id'));
        self::$objCommentsTableFName            = $this->getConfigKey (new S ('articles_comments_table_name'));
        self::$objCommentsTableFEML             = $this->getConfigKey (new S ('articles_comments_table_email'));
        self::$objCommentsTableFURL             = $this->getConfigKey (new S ('articles_comments_table_website'));
        self::$objCommentsTableFRUId            = $this->getConfigKey (new S ('articles_comments_table_registered_user_id'));
        self::$objCommentsTableFComment         = $this->getConfigKey (new S ('articles_comments_table_comment'));
        self::$objCommentsTableFApproved        = $this->getConfigKey (new S ('articles_comments_table_approved'));
        self::$objCommentsTableFDate            = $this->getConfigKey (new S ('articles_comments_table_date'));
        self::$objCommentsTableFArticleId       = $this->getConfigKey (new S ('articles_comments_table_article_id'));

        // Categories ...
        self::$objCategoryTable                 = $this->getConfigKey (new S ('articles_category_table'));
        self::$objCategoryTableFId              = $this->getConfigKey (new S ('articles_category_table_id'));
        self::$objCategoryTableFName            = $this->getConfigKey (new S ('articles_category_table_name'));
        self::$objCategoryTableFSEO             = $this->getConfigKey (new S ('articles_category_table_seo'));
        self::$objCategoryTableFDescription     = $this->getConfigKey (new S ('articles_category_table_description'));
        self::$objCategoryTableFDate            = $this->getConfigKey (new S ('articles_category_table_date'));

        // Configuration ...
        self::$objItemsPerPage                  = $this->getConfigKey (new S ('articles_per_page'));

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

        // Get an MPTT Object, build the ROOT ...
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
        $objTokens[1]   = 'objArticleTable';
        $objTokens[2]   = 'objArticleTableFId';
        $objTokens[3]   = 'objArticleTableFTitle';
        $objTokens[4]   = 'objArticleTableFSEO';
        $objTokens[5]   = 'objArticleTableFContent';
        $objTokens[6]   = 'objArticleTableFDatePublished';
        $objTokens[7]   = 'objArticleTableFDateUpdated';
        $objTokens[8]   = 'objArticleTableFAuthorId';
        $objTokens[9]   = 'objArticleTableFCategoryId';
        $objTokens[10]  = 'objCategoryTable';
        $objTokens[11]  = 'objCategoryTableFId';
        $objTokens[12]  = 'objCategoryTableFName';
        $objTokens[13]  = 'objCategoryTableFSEO';
        $objTokens[14]  = 'objArticleTableFExcerpt';
        $objTokens[15]  = 'objCategoryTableFDescription';
        $objTokens[16]  = 'objArticleTableFState';
        $objTokens[17]  = 'objArticleTableFTags';
        $objTokens[18]  = 'objCommentsTable';
        $objTokens[19]  = 'objCommentsTableFId';
        $objTokens[20]  = 'objCommentsTableFName';
        $objTokens[21]  = 'objCommentsTableFEML';
        $objTokens[22]  = 'objCommentsTableFURL';
        $objTokens[23]  = 'objCommentsTableFRUId';
        $objTokens[24]  = 'objCommentsTableFComment';
        $objTokens[25]  = 'objCommentsTableFApproved';
        $objTokens[26]  = 'objCommentsTableFDate';
        $objTokens[27]  = 'objCommentsTableFArticleId';
        $objTokens[28]  = 'objArticleTableFViews';
		$objTokens[29]	= 'objArticleTableFCanComment';
        $objTokens[30]  = 'objCategoryTableFDate';
        $objTokens[31]  = 'objAuthenticationUserTable';
        $objTokens[32]  = 'objAuthenticationUserTableFId';

        // Set the replacements;
        $objReplac      = new A;
        $objReplac[1]   = self::$objArticleTable;
        $objReplac[2]   = self::$objArticleTableFId;
        $objReplac[3]   = self::$objArticleTableFTitle;
        $objReplac[4]   = self::$objArticleTableFSEO;
        $objReplac[5]   = self::$objArticleTableFContent;
        $objReplac[6]   = self::$objArticleTableFDatePublished;
        $objReplac[7]   = self::$objArticleTableFDateUpdated;
        $objReplac[8]   = self::$objArticleTableFAuthorId;
        $objReplac[9]   = self::$objArticleTableFCategoryId;
        $objReplac[10]  = self::$objCategoryTable;
        $objReplac[11]  = self::$objCategoryTableFId;
        $objReplac[12]  = self::$objCategoryTableFName;
        $objReplac[13]  = self::$objCategoryTableFSEO;
        $objReplac[14]  = self::$objArticleTableFExcerpt;
        $objReplac[15]  = self::$objCategoryTableFDescription;
        $objReplac[16]  = self::$objArticleTableFState;
        $objReplac[17]  = self::$objArticleTableFTags;
        $objReplac[18]  = self::$objCommentsTable;
        $objReplac[19]  = self::$objCommentsTableFId;
        $objReplac[20]  = self::$objCommentsTableFName;
        $objReplac[21]  = self::$objCommentsTableFEML;
        $objReplac[22]  = self::$objCommentsTableFURL;
        $objReplac[23]  = self::$objCommentsTableFRUId;
        $objReplac[24]  = self::$objCommentsTableFComment;
        $objReplac[25]  = self::$objCommentsTableFApproved;
        $objReplac[26]  = self::$objCommentsTableFDate;
        $objReplac[27]  = self::$objCommentsTableFArticleId;
        $objReplac[28]  = self::$objArticleTableFViews;
		$objReplac[29]	= self::$objArticleTableFCanComment;
        $objReplac[30]  = self::$objCategoryTableFDate;
        $objReplac[31]  = Authentication::$objAuthUsersTable;
        $objReplac[32]  = Authentication::$objAuthUsersTableFId;

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
        $this->getConfigKey (new S ('articles_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (ARTICLES_MENU), $objWP,
        $this->getHELP (new S (ARTICLES_MANAGE_ARTICLES)));

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Articles.Articles.Do.View');
        $objACL[] = new S ('Articles.Categories.Do.View');
        $objACL[] = new S ('Articles.Comments.Do.View');
        $objACL[] = new S ('Articles.Do.Operations');
        $objACL[] = new S ('Articles.Do.Configuration');

        // ONLY: Articles.Articles.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMA = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('articles_file_manage_articles')));
            self::$objAdministration->setSubMLink (new S (ARTICLES_MANAGE_ARTICLES),
            $objMA, $this->getHELP (new S (ARTICLES_MANAGE_ARTICLES)));
        }

        // ONLY: Articles.Categories.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('articles_file_manage_categories')));
            self::$objAdministration->setSubMLink (new S (ARTICLES_MANAGE_CATEGORIES),
            $objMC, $this->getHELP (new S (ARTICLES_MANAGE_CATEGORIES)));
        }

        // ONLY: Articles.Comments.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('articles_file_manage_comments')));
            self::$objAdministration->setSubMLink (new S (ARTICLES_MANAGE_COMMENTS),
            $objMC, $this->getHELP (new S (ARTICLES_MANAGE_COMMENTS)));
        }

        // ONLY: Articles.Do.Operations
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[3])->toBoolean () == TRUE) {
            $objMM = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('articles_file_manage_move_articles')));
            self::$objAdministration->setSubMLink (new S (ARTICLES_MANAGE_ARTICLES_MOVE),
            $objMM, $this->getHELP (new S (ARTICLES_MANAGE_ARTICLES_MOVE)));
        }

        // ONLY: Articles.Do.Configuration
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[4])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('articles_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (ARTICLES_MANAGE_ARTICLES_CONFIG),
            $objMF, $this->getHELP (new S (ARTICLES_MANAGE_ARTICLES_CONFIG)));
        }

        // WIDGET: Statistics for articles ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%r', $this->getPendingReviewArticleCount ())
        ->doToken ('%p', $this->getPublishedArticleCount ())
        ->doToken ('%a', $this->getArticleCount ())
        ->doToken ('%d', $this->getDraftArticleCount ())
        ->doToken ('%s', $this->getStickyArticleCount ()));

        // WIDGET: Latest 10 articles ... no status query ...
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
        $objACL[] = new S ('Articles.Articles.Do.View');
        $objACL[] = new S ('Articles.Categories.Do.View');
        $objACL[] = new S ('Articles.Comments.Do.View');
        $objACL[] = new S ('Articles.Do.Operations');
        $objACL[] = new S ('Articles.Do.Configuration');

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
        TPL::manageLNK (new S (ARTICLES_RSS_FEED), new S (Frontend::RSS_ALTERNATE),
        new S (Frontend::RSS_TYPE), URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
        FRONTEND_FEED_URL)), new A (Array (FRONTEND_RSS_XML, __CLASS__))));
    }

    /**
     * Will check to see if the article title is unique because we want to first make an unique SQL index on the title
     * of articles, but due to the fact that we automatically used the article title as the rewritten URL we need to make sure
     * that no two articles have the same title. Also, two articles with the exact same name can be confusing for users at first,
     * and most importantly, for search engines at second;
     *
     * @param S $objArticleTitle The article to check for;
     * @return boolean Will return true if the article title is unique in the database;
     */
    public function checkArticleTitleIsUnique (S $objArticleTitle) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objArticleTableFTitle'))->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', new S ('WHERE %objArticleTableFTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objArticleTitle))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check to see if the article URL is unique because we want to first make an unique SQL index on the URL
     * of articles, but due to the fact that we automatically used the article URL as the rewritten URL we need to make sure
     * that no two articles have the same URL. Also, two articles with the exact same name can be confusing for users at first,
     * and most importantly, for search engines at second;
     *
     * @param S $objArticleURL The article to check for;
     * @return boolean Will return true if the article title is unique in the database;
     */
    public function checkArticleURLIsUnique (S $objArticleURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objArticleTableFSEO'))->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', new S ('WHERE %objArticleTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objArticleURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check to see if the category URL is unique because we want to first make an unique SQL index on the URL
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
     * Will return the count of articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getArticleCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objArticleTableFTitle) AS count'))->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of published articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getPublishedArticleCount (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticleCount (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_PUBLISHED)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of published articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getPublishedArticleCountForCategoryURL (S $objCategoryURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticleCount (_S ('WHERE %objArticleTableFState = "%Id" AND %objArticleTableFCategoryId = "%cId"')
        ->doToken ('%Id', self::STATE_PUBLISHED)->doToken ('%cId', $this->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId))->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of draft articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getDraftArticleCount (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticleCount (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_DRAFT)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of pending articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getPendingReviewArticleCount (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticleCount (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_PENDING_REVIEW)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of sticky articles in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of articles that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of articles that matched the condition, if given;
     */
    public function getStickyArticleCount (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticleCount (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_STICKY)->appendString (_SP)
        ->appendString ($objSQLCondition));
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
        ->doToken ('%what', new S ('COUNT(%objCategoryTableFName) AS count'))->doToken ('%table', self::$objCategoryTable)
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
     * Will return the articles, based on the specified condition;
     *
     * This method will return all the articles defined in the database, by taking the passed SQL condition as argument. If no
     * condition is specified, then it will return ALL defined articles in the table;
     *
     * @param S $objSQLCondition The SQL condition passed for articles to get;
     * @return array The result array;
     */
    public function getArticles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the array of published articles;
     *
     * This method allows us to return the array of published articles, thus allowing us to write less SQL code in the frontend
     * and more SQL code in the backend/CLASS code. This allows us to get a little more productive in the way we represent our
     * code to the users;
     *
     * @param $objSQLCondition The appended SQL condition;
     * @return array The result array;
     */
    public function getPublishedArticles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_PUBLISHED)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return the array of draft articles;
     *
     * This method allows us to return the array of draft articles, thus allowing us to write less SQL code in the frontend
     * and more SQL code in the backend/CLASS code. This allows us to get a little more productive in the way we represent our
     * code to the users;
     *
     * @param $objSQLCondition The appended SQL condition;
     * @return array The result array;
     */
    public function getDraftArticles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_DRAFT)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return the array of pending review articles;
     *
     * This method allows us to return the array of pending articles, thus allowing us to write less SQL code in the frontend
     * and more SQL code in the backend/CLASS code. This allows us to get a little more productive in the way we represent our
     * code to the users;
     *
     * @param $objSQLCondition The appended SQL condition;
     * @return array The result array;
     */
    public function getPendingReviewArticles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_PENDING_REVIEW)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return the array of sticky articles;
     *
     * This method allows us to return the array of published sticky, thus allowing us to write less SQL code in the frontend
     * and more SQL code in the backend/CLASS code. This allows us to get a little more productive in the way we represent our
     * code to the users;
     *
     * @param $objSQLCondition The appended SQL condition;
     * @return array The result array;
     */
    public function getStickyArticles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_STICKY)->appendString (_SP)
        ->appendString ($objSQLCondition));
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
    public function getArticleInfoById (S $objArticleId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', new S ('WHERE %objArticleTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objArticleId))->offsetGet (0)->offsetGet ($objFieldToGet);
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
    public function getArticleInfoByTitle (S $objArticleTitle, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', new S ('WHERE %objArticleTableFTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objArticleTitle))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given article seo, while giving the corresponding field as a parameter to the function.
     * An easy way to get information about stored articles, without having too much of a problem with getting information from
     * them. Also, by getting the SEO of that article, we are certain that the string is URL compatible;
     *
     * @param S $objArticleSEO The article seo URL to query for;
     * @param S $objFieldToGet The article field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getArticleInfoByURL (S $objArticleURL, S $objFieldToGet) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', new S ('WHERE %objArticleTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objArticleURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about the previous article, if given the current article id. It's a good way to have a
     * next/previous widget, that will facilitate navigation in the context of our application.
     *
     * @param $objArticleId The given article id;
     * @param $objFieldToGet The field to retrieve;
     * @return mixed Depends on what was requested;
     */
    public function getPreviousArticleInfoById (S $objArticleId, S $objFieldToGet) {
        // Return ...
        return $this->getPublishedArticles (_S ('AND %objArticleTableFId < %currentArticleId
        ORDER BY %objArticleTableFId DESC LIMIT 1')->doToken ('%currentArticleId',
        $this->getArticleInfoById ($objArticleId, self::$objArticleTableFId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about the previous article, if given the current article title. It's a good way to have a
     * next/previous widget, that will facilitate navigation in the context of our application.
     *
     * @param $objArticleTitle The given article title;
     * @param $objFieldToGet The field to retrieve;
     * @return mixed Depends on what was requested;
     */
    public function getPreviousArticleInfoByTitle (S $objArticleTitle, S $objFieldToGet) {
        // Return ...
        return $this->getPublishedArticles (_S ('AND %objArticleTableFId < %currentArticleId
        ORDER BY %objArticleTableFId DESC LIMIT 1')->doToken ('%currentArticleId',
        $this->getArticleInfoByTitle ($objArticleTitle, self::$objArticleTableFId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about the previous article, if given the current article URL. It's a good way to have a
     * next/previous widget, that will facilitate navigation in the context of our application.
     *
     * @param $objArticleSEO The given article URL;
     * @param $objFieldToGet The field to retrieve;
     * @return mixed Depends on what was requested;
     */
    public function getPreviousArticleInfoByURL (S $objArticleSEO, S $objFieldToGet) {
        // Return ...
        return $this->getPublishedArticles (_S ('AND %objArticleTableFId < %currentArticleId
        ORDER BY %objArticleTableFId DESC LIMIT 1')->doToken ('%currentArticleId',
        $this->getArticleInfoByURL ($objArticleSEO, self::$objArticleTableFId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about the next article, if given the crrent article id. It's a good way to have a next/previous
     * widget that will facilitate navigation in the context of oru application.
     *
     * @param $objArticleId The passed article id;
     * @param $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getNextArticleInfoById (S $objArticleId, S $objFieldToGet) {
        // Return ...
        return $this->getPublishedArticles (_S ('AND %objArticleTableFId > %currentArticleId
        ORDER BY %objArticleTableFId ASC LIMIT 1')->doToken ('%currentArticleId',
        $this->getArticleInfoById ($objArticleId, self::$objArticleTableFId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about the next article, if given the crrent article title. It's a good way to have a next/previous
     * widget that will facilitate navigation in the context of oru application.
     *
     * @param $objArticleTitle The passed article title;
     * @param $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getNextArticleInfoByTitle (S $objArticleTitle, S $objFieldToGet) {
        // Return ...
        return $this->getPublishedArticles (_S ('AND %objArticleTableFId > %currentArticleId
        ORDER BY %objArticleTableFId ASC LIMIT 1')->doToken ('%currentArticleId',
        $this->getArticleInfoByTitle ($objArticleTitle, self::$objArticleTableFId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about the next article, if given the crrent article URL. It's a good way to have a next/previous
     * widget that will facilitate navigation in the context of oru application.
     *
     * @param $objArticleSEO The passed article URL;
     * @param $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getNextArticleInfoByURL (S $objArticleSEO, S $objFieldToGet) {
        // Return ...
        return $this->getPublishedArticles (_S ('AND %objArticleTableFId > %currentArticleId
        ORDER BY %objArticleTableFId ASC LIMIT 1')->doToken ('%currentArticleId',
        $this->getArticleInfoByURL ($objArticleSEO, self::$objArticleTableFId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return articles by the given category id;
     *
     * This method will return all articles in a category id, giving us the possibility to return an array of all articles defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all articles are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined articles in that category;
     */
    public function getArticlesByCategoryId (S $objCategoryId, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objArticleTable)
        ->doToken ('%condition', _S ('WHERE %objArticleTableFCategoryId = "%Id"')
        ->doToken ('%Id', $objCategoryId))->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return published articles by the given category id;
     *
     * This method will return all articles in a category id, giving us the possibility to return an array of all articles defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all articles are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined articles in that category;
     */
    public function getPublishedArticlesByCategoryId (S $objCategoryId, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_PUBLISHED)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return draft articles by the given category id;
     *
     * This method will return all articles in a category id, giving us the possibility to return an array of all articles defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all articles are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined articles in that category;
     */
    public function getDraftArticlesByCategoryId (S $objCategoryId,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_DRAFT)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return pending review articles by the given category id;
     *
     * This method will return all articles in a category id, giving us the possibility to return an array of all articles defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all articles are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined articles in that category;
     */
    public function getPendingArticlesByCategoryId (S $objCategoryId,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_PENDING_REVIEW)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return sticky articles by the given category id;
     *
     * This method will return all articles in a category id, giving us the possibility to return an array of all articles defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all articles are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined articles in that category;
     */
    public function getStickyArticlesByCategoryId (S $objCategoryId,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Id"')
        ->doToken ('%Id', self::STATE_STICKY)->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Will return articles by the given category name;
     *
     * This method will return all articles in a category name, giving us the possibility to returna an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category name;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getArticlesByCategoryName (S $objCategoryName,
    S $objSQLCondition = NULL) {
        // Do return ...
    	return $this->getArticlesByCategoryId ($this
    	->getCategoryInfoByName ($objCategoryName,
    	self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return published articles by the given category name;
     *
     * This method will return all articles in a category name, giving us the possibility to returna an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category name;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getPublishedArticlesByCategoryName (S $objCategoryName,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getPublishedArticlesByCategoryId ($this
        ->getCategoryInfoByName ($objCategoryName,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return draft articles by the given category name;
     *
     * This method will return all articles in a category name, giving us the possibility to returna an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category name;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getDraftArticlesByCategoryName (S $objCategoryName,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getDraftArticlesByCategoryId ($this
        ->getCategoryInfoByName ($objCategoryName,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return pending articles by the given category name;
     *
     * This method will return all articles in a category name, giving us the possibility to returna an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category name;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getPendingArticlesByCategoryName (S $objCategoryName,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getPendingArticlesByCategoryId ($this
        ->getCategoryInfoByName ($objCategoryName,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return sticky articles by the given category name;
     *
     * This method will return all articles in a category name, giving us the possibility to returna an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category name;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getStickyArticlesByCategoryName (S $objCategoryName,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getStickyArticlesByCategoryId ($this
        ->getCategoryInfoByName ($objCategoryName,
        self::$objCategoryTableFId), $objSQLCondtion);
    }

    /**
     * Will return articles by the given category URL;
     *
     * This method will return all articles in a category URL, giving us the possibility to return an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category URL;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getArticlesByCategoryURL (S $objCategoryURL,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($this
        ->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return published articles by the given category URL;
     *
     * This method will return all articles in a category URL, giving us the possibility to return an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category URL;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getPublishedArticlesByCategoryURL (S $objCategoryURL,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getPublishedArticlesByCategoryId ($this
        ->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return draft articles by the given category URL;
     *
     * This method will return all articles in a category URL, giving us the possibility to return an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category URL;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getDraftArticlesByCategoryURL (S $objCategoryURL,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getDraftArticlesByCategoryId ($this
        ->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return pending articles by the given category URL;
     *
     * This method will return all articles in a category URL, giving us the possibility to return an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category URL;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getPendingArticlesByCategoryURL (S $objCategoryURL,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getPendingArticlesByCategoryId ($this
        ->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return sticky articles by the given category URL;
     *
     * This method will return all articles in a category URL, giving us the possibility to return an array of all articles
     * defined in a category, which in turn gives the possibility to build blog-like pages in our sites, where all articles are
     * listed for a given category URL;
     *
     * @param S $objCategoryName The given category name;
     * @return array An array containing all defined articles in that category;
     */
    public function getStickyArticlesByCategoryURL (S $objCategoryURL,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getStickyArticlesByCategoryId ($this
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
     * Will return articles by page. This doesn't take in consideration for example categories, or any other criteria;
     *
     * This method will return articles of the current page, from all categories. It doesn't take in consideration no criteria
     * whatsoever, and the default ordering type is ASC/DESC by the auto-incremented identifier. Like saying: "get me the last N
     * articles I've published, means the biggest 10 Id's in the table ...";
     *
     * @param S $objPageInt The page to get articles for;
     * @param S $objOrderType For when we need ASC sorting;
     * @return array The result array;
     */
    public function getArticlesByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticles (_S ('ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return published articles by page. This doesn't take in consideration for example categories, or any other criteria;
     *
     * This method will return articles of the current page, from all categories. It doesn't take in consideration no criteria
     * whatsoever, and the default ordering type is ASC/DESC by the auto-incremented identifier. Like saying: "get me the last N
     * articles I've published, means the biggest 10 Id's in the table ...";
     *
     * @param S $objPageInt The page to get articles for;
     * @param S $objOrderType For when we need ASC sorting;
     * @return array The result array;
     */
    public function getPublishedArticlesByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PUBLISHED));
    }

    /**
     * Will return draft articles by page. This doesn't take in consideration for example categories, or any other criteria;
     *
     * This method will return articles of the current page, from all categories. It doesn't take in consideration no criteria
     * whatsoever, and the default ordering type is ASC/DESC by the auto-incremented identifier. Like saying: "get me the last N
     * articles I've published, means the biggest 10 Id's in the table ...";
     *
     * @param S $objPageInt The page to get articles for;
     * @param S $objOrderType For when we need ASC sorting;
     * @return array The result array;
     */
    public function getDraftArticlesByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_DRAFT));
    }

    /**
     * Will return pending articles by page. This doesn't take in consideration for example categories, or any other criteria;
     *
     * This method will return articles of the current page, from all categories. It doesn't take in consideration no criteria
     * whatsoever, and the default ordering type is ASC/DESC by the auto-incremented identifier. Like saying: "get me the last N
     * articles I've published, means the biggest 10 Id's in the table ...";
     *
     * @param S $objPageInt The page to get articles for;
     * @param S $objOrderType For when we need ASC sorting;
     * @return array The result array;
     */
    public function getPendingArticlesByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PENDING_REVIEW));
    }

    /**
     * Will return sticky articles by page. This doesn't take in consideration for example categories, or any other criteria;
     *
     * This method will return articles of the current page, from all categories. It doesn't take in consideration no criteria
     * whatsoever, and the default ordering type is ASC/DESC by the auto-incremented identifier. Like saying: "get me the last N
     * articles I've published, means the biggest 10 Id's in the table ...";
     *
     * @param S $objPageInt The page to get articles for;
     * @param S $objOrderType For when we need ASC sorting;
     * @return array The result array;
     */
    public function getStickyArticlesByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticles (_S ('WHERE %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_STICKY));
    }

    /**
     * Will return articles by the given category id, page and sorting;
     *
     * This method will return all articles in the given combination of category id and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryId The category id;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getArticlesByCategoryIdAndPage (S $objCategoryId, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId,
        _S ('ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return published articles by the given category id, page and sorting;
     *
     * This method will return all articles in the given combination of category id and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryId The category id;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getPublishedArticlesByCategoryIdAndPage (S $objCategoryId, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PUBLISHED));
    }

    /**
     * Will return draft articles by the given category id, page and sorting;
     *
     * This method will return all articles in the given combination of category id and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryId The category id;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getDraftArticlesByCategoryIdAndPage (S $objCategoryId, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_DRAFT));
    }

    /**
     * Will return pending review articles by the given category id, page and sorting;
     *
     * This method will return all articles in the given combination of category id and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryId The category id;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getPendingArticlesByCategoryIdAndPage (S $objCategoryId, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PENDING_REVIEW));
    }

    /**
     * Will return sticky articles by the given category id, page and sorting;
     *
     * This method will return all articles in the given combination of category id and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryId The category id;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getStickyArticlesByCategoryIdAndPage (S $objCategoryId, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryId ($objCategoryId, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_STICKY));
    }

    /**
     * Will return articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryName The category name;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getArticlesByCategoryNameAndPage (S $objCategoryName, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryName ($objCategoryName,
        _S ('ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return published articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryName The category name;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getPublishedArticlesByCategoryNameAndPage (S $objCategoryName, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryName ($objCategoryName, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PUBLISHED));
    }

    /**
     * Will return draft articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryName The category name;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getDraftArticlesByCategoryNameAndPage (S $objCategoryName, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryName ($objCategoryName, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_DRAFT));
    }

    /**
     * Will return pending review articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryName The category name;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getPendingArticlesByCategoryNameAndPage (S $objCategoryName, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryName ($objCategoryName, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PENDING_REVIEW));
    }

    /**
     * Will return sticky articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryName The category name;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getStickyArticlesByCategoryNameAndPage (S $objCategoryName, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryName ($objCategoryName, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_STICKY));
    }

    /**
     * Will return articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryURL The category URL;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getArticlesByCategoryURLAndPage (S $objCategoryURL, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryURL ($objCategoryURL,
        _S ('ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return published articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryURL The category URL;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getPublishedArticlesByCategoryURLAndPage (S $objCategoryURL, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryURL ($objCategoryURL, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PUBLISHED));
    }

    /**
     * Will return draft articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryURL The category URL;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getDraftArticlesByCategoryURLAndPage (S $objCategoryURL, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryURL ($objCategoryURL, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_DRAFT));
    }

    /**
     * Will return pending articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryURL The category URL;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getPendingArticlesByCategoryURLAndPage (S $objCategoryURL, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryURL ($objCategoryURL, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_PENDING_REVIEW));
    }

    /**
     * Will return sticky articles by the given category name, page and sorting;
     *
     * This method will return all articles in the given combination of category name and page limits. It can be used to display
     * articles in a category like manner by just giving two parameters obligatory. It can be used for article pages, where a
     * string of articles is needed to be displayed;
     *
     * @param S $objCategoryURL The category URL;
     * @param S $objPageInt The page to limit to;
     * @param S $objOrdering What kind of ordering;
     * @return array The result array;
     */
    public function getStickyArticlesByCategoryURLAndPage (S $objCategoryURL, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getArticlesByCategoryURL ($objCategoryURL, _S ('AND %objArticleTableFState = "%Sd"
        ORDER BY %objArticleTableFDatePublished %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Sd', self::STATE_STICKY));
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
    public function getCommentsByArticleURL (S $objArticleURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objCommentsTable)
        ->doToken ('%condition', new S ('WHERE %objCommentsTableFArticleId = "%Id"'))
        ->doToken ('%Id', $this->getArticleInfoByURL ($objArticleURL, self::$objArticleTableFId))
        ->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Will return all approved comments for this URL. We do this because we need to moderate things a little before comments get
     * posted to a website.
     *
     * @param S $objTextURL The URL to query for;
     * @param S $objSQLCondition An appended SQL condition;
     * @return array Will return the array of approved comments defined for this text ...
     */
    public function getApprovedCommentsByArticleURL (S $objArticleURL, S $objSQLCondition = NULL) {
        // Set some requirements ...
        $objSQLCondition = $objSQLCondition == NULL ? new S : $objSQLCondition;

        // Do return ...
        return $this->getCommentsByArticleURL ($objArticleURL, $objSQLCondition
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
                return parent::__CALL ($objFunctionName, $objFunctionArgs);
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
                foreach ($this->getPublishedArticles (new S ('ORDER
                BY %objArticleTableFDatePublished DESC')) as $k => $v) {
                    // Set some requirements ...
                    $objDTE = date ('Y-m-d', (int) $v[self::$objArticleTableFDatePublished]->toString ());
                    $objLOC = URL::staticURL (new A (Array (ARTICLES_ARTICLES_URL, FRONTEND_SECTION_URL)),
                    new A (Array ($v[self::$objArticleTableFSEO], FRONTEND_ARTICLE_URL)));

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
                    foreach ($this->getPublishedArticles (new S ('ORDER BY %objArticleTableFDatePublished
                    DESC LIMIT 0, 30')) as $k => $v) {
                        // Set some requirements ...
                        $objDTE = date (DATE_RFC822, (int) $v[self::$objArticleTableFDatePublished]->toString ());
                        $objLOC = URL::staticURL (new A (Array (ARTICLES_ARTICLES_URL, FRONTEND_SECTION_URL)),
                        new A (Array ($v[self::$objArticleTableFSEO], FRONTEND_ARTICLE_URL)));
                        $objDSC = $v[self::$objArticleTableFExcerpt]->entityEncode (ENT_QUOTES)
                        ->entityDecode (ENT_QUOTES)->stripTags ();
                        $objTTL = $v[self::$objArticleTableFTitle];

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
                // Set the template file ...
                if ($cId = TPL::tpIni ($tpF = new FilePath ($this->getPathToSkin ()
				->toRelativePath () . $objW . TPL_EXTENSION), new I (180))) {
                	// Get the category to start from ...
                	if (isset ($objWA['start_from_category'])) {
                    	// Get the category LIST;
                    	$objCategoryList = $this->getCategories (NULL,
                    	$objWA['start_from_category']);
                	} else {
                    	// Get the category LIST;
                    	$objCategoryList = $this->getCategories (NULL, NULL);
                	}

                	// Set the template file ...
                	TPL::tpSet ($objCategoryList, new S ('objCategoryList'), $tpF);
                	TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                	TPL::tpSet ($this, new S ('ART'), $tpF);
                	TPL::tpExe ($tpF);
					TPL::tpEnd ($cId);
				}
				// BK;
                break;

            case 'widgetList':
                // Check some needed requirements ...
                if ($_GET[FRONTEND_SECTION_URL] == FRONTEND_ARTICLE_URL) {
                    // Set some requirements ...
                    $objPag = isset ($_GET[ARTICLES_PAGE_URL]) ? $_GET[ARTICLES_PAGE_URL]: new S ((string) 1);

                    if (isset ($_GET[ARTICLES_ARTICLES_URL])) {
                        // Check that the article exists, before doing anything stupid ...
                        if ($this->checkArticleURLIsUnique ($objURL =
                        $_GET[ARTICLES_ARTICLES_URL])->toBoolean () == TRUE) {
                            // Make the proper header, at first ...
                            $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                            // Give me back my free hardcore, Quoth the server, '404' ...
                            $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                            new A (Array ('404'))), new S ('Location'));
                        } else {
                            // Update them views ...
                            $this->_Q (_QS ('doUPDATE')
                            ->doToken ('%table', self::$objArticleTable)
                            ->doToken ('%condition', _S ('%objArticleTableFViews = %objArticleTableFViews + 1
                            WHERE %objArticleTableFSEO = "%Id"')->doToken ('%Id', $_GET[ARTICLES_ARTICLES_URL])));

                            // Do me SEO, yah baby! ... (add title and category name, cause we need it)
                            TPL::manageTTL ($this->getArticleInfoByURL ($objURL, self::$objArticleTableFTitle));
                            TPL::manageTTL (MPTT::mpttRemoveUnique ($this->getCategoryInfoById ($this->getArticleInfoByURL($objURL,
                            self::$objArticleTableFCategoryId), self::$objCategoryTableFName)));

                            // Do me SEO, yah baby! ... (add keywords and description, for something extra)
                            TPL::manageTAG (new S ('keywords'), $this->getArticleInfoByURL ($objURL, self::$objArticleTableFTags));
                            TPL::manageTAG (new S ('description'), $this->getArticleInfoByURL ($objURL,
                            self::$objArticleTableFTitle)->appendString (_DCSP)->appendString ($this
                            ->getArticleInfoByURL ($objURL, self::$objArticleTableFExcerpt))->doToken (_QOT, _NONE));

                            // Set the template file ...
                            $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . '-Item.tp');
                            TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                            TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                            TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                            TPL::tpSet ($objURL, new S ('objURL'), $tpF);
                            TPL::tpSet ($this, new S ('ART'), $tpF);
                            TPL::tpExe ($tpF);
                        }
                    } else {
                        if (isset ($_GET[ARTICLES_CATEGORY_URL])) {
                            // Check that the category exists, before doing anything stupid ...
                            if ($this->checkCategoryURLIsUnique ($objCat =
                            $_GET[ARTICLES_CATEGORY_URL])->toBoolean () == TRUE) {
                                // Make the proper header, at first ...
                                $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                                // Give me back my free hardcore, Quoth the server, '404' ...
                                $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                                new A (Array ('404'))), new S ('Location'));
                            } else {
                                // Do me SEO, yah baby! ... (title and pagination to prev. duplicates)
                                TPL::manageTTL (_S (ARTICLES_PAGE_URL)->appendString (_SP)->appendString ($objPag));
                                TPL::manageTTL (MPTT::mpttRemoveUnique ($this
                                ->getCategoryInfoByURL ($objCat, self::$objCategoryTableFName)));

                                // Do me SEO, yah baby! ... (add cat. descr)
                                if ($this->getCategoryInfoByURL ($objCat,
                                self::$objCategoryTableFDescription)->toLength ()->toInt () != 0) {
                                    TPL::manageTAG (new S ('description'), $this->getCategoryInfoByURL ($objCat,
                                    self::$objCategoryTableFDescription)->entityDecode (ENT_QUOTES)->stripTags ()
                                    ->doToken (_QOT, _NONE)->doSubStr (0, META_DESCRIPTION_MAX)
                                    ->appendString (_SP)->appendString (_DTE));
                                }

                                // Set some requirements ...
                                $objCnt = $this->getPublishedArticleCountForCategoryURL ($objCat);
                                $objStk = $this->getStickyArticlesByCategoryURLAndPage ($objCat, $objPag);
                                $objArt = $this->getPublishedArticlesByCategoryURLAndPage ($objCat, $objPag);
								$objInC = new B (TRUE);
                            }
                        } else {
                            // Do me SEO, yah baby! ...
                            TPL::manageTTL (_S (FRONTEND_ARTICLE_URL));
                            TPL::manageTTL (_S (ARTICLES_PAGE_URL)->appendString (_SP)->appendString ($objPag));

                            // Set some requirements ...
                            $objCnt = $this->getPublishedArticleCount ();
                            $objStk = $this->getStickyArticlesByPage ($objPag);
                            $objArt = $this->getPublishedArticlesByPage ($objPag);
							$objInC = new B (FALSE);
							$objCat = new S;
                        }

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                        TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
						TPL::tpSet ($objInC, new S ('objInC'), $tpF);
						TPL::tpSet ($objCat, new S ('objCat'), $tpF);
                        TPL::tpSet ($objArt, new S ('objArt'), $tpF);
                        TPL::tpSet ($objStk, new S ('objStk'), $tpF);
						TPL::tpSet ($this, new S ('ART'), $tpF);
                        TPL::tpExe ($tpF);

                        // Set them paginations ...
                        if ($objCnt->toInt () > (int) self::$objItemsPerPage->toString ())
                        self::$objFrontend->setPagination ($objCnt, new I ((int) self::$objItemsPerPage->toString ()));
                    }
                } else {
                    // Do the biggest error on the PLANET ...
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (ARTICLES_NEED_PROPER_SECTION),
                    new S (ARTICLES_NEED_PROPER_SECTION_FIX));
                }
                // BK;
                break;

            case 'widgetComments':
                // Check if we have the proper URL enabled ...
                if (isset ($_GET[ARTICLES_ARTICLES_URL])) {
                    // Check if the comments are enabled ...
                    if ($this->getArticleInfoByURL ($_GET[ARTICLES_ARTICLES_URL],
                    self::$objArticleTableFCanComment) == 'Y') {
                        // Set some requirements ...
                        $objCommentIsOk = new S;
						$objShowComm = new B (TRUE);
                        $objComments = $this->getApprovedCommentsByArticleURL ($_GET[ARTICLES_ARTICLES_URL],
                        new S ('ORDER BY %objCommentsTableFDate DESC'));

                        // Check for status ...
                        if (isset ($_GET[ARTICLES_STATUS_URL])) {
                            if ($_GET[ARTICLES_STATUS_URL] == ARTICLES_STATUS_OK_URL) {
                                $objCommentIsOk = new S ($objWA['comment_has_been_added']);
                            }
                        }

                        // Set some requirements ...
                        $objShowFrm = new B (TRUE);

                        // Check if we're allowed to show the comment form ...
                        if ($this->getConfigKey (new S ('article_settings_article_auth_to_comment')) == 'Y') {
                            if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                                $objShowFrm = new B (TRUE);
                            } else {
                                $objShowFrm = new B (FALSE);
                            }
                        }

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                        TPL::tpSet ($objCommentIsOk, new S ('objCommentIsOk'), $tpF);
                        TPL::tpSet ($objComments, new S ('objComments'), $tpF);
						TPL::tpSet ($objShowComm, new S ('objShowComm'), $tpF);
                        TPL::tpSet ($objShowFrm, new S ('objShowFrm'), $tpF);
                        TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                        TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                        TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                        TPL::tpExe ($tpF);

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
                            ->setUpdateField (self::$objCommentsTableFId)
                            ->setRedirect (URL::rewriteURL (new A (Array (ARTICLES_STATUS_URL)),
                            new A (Array (ARTICLES_STATUS_OK_URL))))
                            ->setName (new S ('commentForm'))
                            ->setExtraUpdateData (self::$objCommentsTableFDate, new S ((string) time ()))
                            ->setExtraUpdateData (self::$objCommentsTableFArticleId, $this
                            ->getArticleInfoByURL ($_GET[ARTICLES_ARTICLES_URL], self::$objArticleTableFId))
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
                            ->setTinyMCETextarea (new B (TRUE))
                            ->setClass (new S ('tinyMCESimple'))
                            ->setRows (new S ('10'))
                            ->setContainerDiv (new B (TRUE));

                            // Notify ...
                            if ($this->checkFormHasErrors()->toBoolean () == FALSE &&
                            $this->checkPOST (self::$objCommentsTableFComment)->toBoolean () == TRUE) {
                                // Get some needed requirements ..
                                $objURL = $_GET[ARTICLES_ARTICLES_URL];

                                if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                                    // Query the authentication ...
                                    $objUSR = $this->ATH->getCurrentUserInfoById (Authentication::$objAuthUsersTableFUName);
                                } else {
                                    // Or trust the FORM ...
                                    $objUSR = $this->getPOST (self::$objCommentsTableFName);
                                }

                                // Go and SP ... MAIL me ...
                                $objMAIL = new MAIL;
                                $objMAIL->doMAIL ($this->STG->getConfigKey (new S ('settings_website_notification_email')),
                                new S (ARTICLES_COMMENT_HAS_BEEN_POSTED), $this->getHELP (new S ('widgetCommentsCommentPosted'))
                                ->doToken ('%u', $objUSR));
                            }

                            // End form and execute ...
                            $this->setFormEndAndExecute (new B (TRUE));
                        }
					}
                }
                // BK;
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
    		case 'manageArticles':
			    // Check if there's an action to take;
			    if (isset ($_GET[ADMIN_ACTION])) {
			        // Do a switch ...
			        switch ($_GET[ADMIN_ACTION]) {
			            case ADMIN_ACTION_EDIT:
			                $this->renderForm (new S ('articleEdit'));
			                break;

			            case ADMIN_ACTION_ERASE:
			                $this->renderForm (new S ('articleErase'));
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
                            case ARTICLES_SEARCH_TITLE:
                                $objGetCondition->appendString (_SP)
                                ->appendString ('WHERE %objArticleTableFTitle');
                                break;

                            case ARTICLES_SEARCH_CONTENT:
                                $objGetCondition->appendString (_SP)
                                ->appendString ('WHERE %objArticleTableFContent');
                                break;

                            case ARTICLES_SEARCH_TAGS:
                                $objGetCondition->appendString (_SP)
                                ->appendString ('WHERE %objArticleTableFTags');
                                break;

                            case ARTICLES_SEARCH_EXCERPT:
                                $objGetCondition->appendString (_SP)
                                ->appendString ('WHERE %objArticleTableFExcerpt');
                                break;

                            case ARTICLES_SEARCH_CATEGORY:
                                $objGetCondition->appendString ('AS t1 INNER JOIN %objCategoryTable AS t2
                                ON t1.%objArticleTableFCategoryId = t2.%objCategoryTableFId
                                WHERE %objCategoryTableFName');
                                break;
                        }

                        // Add LIKE searching ...
                        $objGetCondition->appendString (_SP)->appendString ('LIKE "%%Search%"')
                        ->doToken ('%Search', $_GET[ADMIN_ACTION_SEARCH]);

                        // Get the count ...
                        $objSearchCount = $this->getArticleCount ($objGetCondition);
                    }

			        // Do a sorting, before anything else;
			        if (isset ($_GET[ADMIN_ACTION_SORT])) {
			            // Do a switch ...
			            switch ($_GET[ADMIN_ACTION_SORT]) {
			                case 'AscByTitle':
			                case 'DescByTitle':
			                    // Make the ordered condition;
			                    $objGetCondition->appendString (_SP)
			                    ->appendString ('ORDER BY %objArticleTableFTitle');

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

		                    case 'AscByPublished':
                            case 'DescByPublished':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objArticleTableFDatePublished');

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
                                break;

                            case 'AscByUpdated':
                            case 'DescByUpdated':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objArticleTableFDateUpdated');

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
                                break;

                            case 'AscByViews':
                            case 'DescByViews':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objArticleTableFViews');

                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByViews':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByViews':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

		                    case 'AscByCategory':
                            case 'DescByCategory':
                                if (isset ($_GET[ADMIN_ACTION_BY])) {
                                    if ($_GET[ADMIN_ACTION_BY] == ARTICLES_SEARCH_CATEGORY) {
                                        // Make the ordered condition;
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ORDER BY t2.%objCategoryTableFName');
                                    } else {
                                        // Make the ordered condition;
                                        $objGetCondition->doToken ('WHERE', _SP . 'AS t1 INNER JOIN %objCategoryTable AS t2
                                        ON t1.%objArticleTableFCategoryId = t2.%objCategoryTableFId WHERE');
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ORDER BY t2.%objCategoryTableFName');
                                    }
                                } else {
                                    // Make the ordered condition;
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('AS t1 INNER JOIN %objCategoryTable AS t2
                                    ON t1.%objArticleTableFCategoryId = t2.%objCategoryTableFId
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
			            }
			        }

		            // Add some LIMITs
		            $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
		            ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

			        // Get based on SQL condtion ...
			        $objArticleTable = $this->getArticles ($objGetCondition);
			        if (isset ($_GET[ADMIN_ACTION_BY])) { $objArticleTableCount = $objSearchCount; }
                    else { $objArticleTableCount = $this->getArticleCount (); }

                    // Fix pagination when count is LESS than 10;
                    if (isset ($_GET[ADMIN_ACTION_BY]) && isset ($_GET[ADMIN_PAGINATION])) {
                        if ($objArticleTableCount->toInt () < 10) {
                            // Remove paging ... & redirect to proper ...
                            TPL::setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGINATION))), new S ('Location'));
                        } else {
                            if (CEIL ($objArticleTableCount->toInt () / 10) < (int) $_GET[ADMIN_PAGINATION]->toString ()) {
                                // Redirect to proper ...
                                TPL::setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGINATION)),
                                new A (Array (CEIL ($objArticleTableCount->toInt () / 10)))), new S ('Location'));
                            }
                        }
                    }

			        // Set the template file ...
			        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageArticles.tp');
			        TPL::tpSet ($objArticleTable, new S ('articleTable'), $tpF);
			        TPL::tpSet ($this, new S ('ART'), $tpF);
			        TPL::tpSet ($this->STG, new S ('STG'), $tpF);
			        TPL::tpExe ($tpF);

			        // Do pagination ...
			        if ($objArticleTableCount->toInt () > 10)
			        self::$objAdministration->setPagination ($objArticleTableCount);

			        // Do the form, make it happen;
			        $this->renderForm (new S ('articleSearch'));
			        $this->renderForm (new S ('articleCreate'));
			    }
			    // BK;
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
			    // BK;
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
                                ->appendString ('AS t1 LEFT JOIN %objArticleTable
                                AS t2 ON t1.%objCommentsTableFArticleId = t2.%objArticleTableFId
                                ORDER BY t2.%objArticleTableFTitle');
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
                            $v[self::$objCommentsTableFName] = $this->ATH->getUserInfoById ($v[self::$objCommentsTableFRUId],
                            Authentication::$objAuthUsersTableFUName);
                        }
                    }

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageComments.tp');
                    TPL::tpSet ($objComments, new S ('articleTable'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpSet ($this, new S ('ART'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination ...
                    if ($objCommentsCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objCommentsCount);
                }
                // BK;
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
            case 'articleSearch':
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
                        new S (ARTICLES_SEARCH_FIELD_IS_EMPTY));

                        // Unset the post ...
                        $this->unsetPOST ();
                    } else {
                        // Get what to search and where ...
                        $objWhatToSearch    = $this->getPOST (new S ('search_by'));
                        $objWhereToSearch   = $this->getPOST (new S ('search_field'));

                        // And go there ...
                        $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SEARCH, ADMIN_ACTION_BY)),
                        new A (Array ($objWhatToSearch, $objWhereToSearch))), new S ('Location'));
                    }
                }

                // Check the option that has been selected;
                $objWasSelected = new A (Array (new B ($objSearchBy == ARTICLES_SEARCH_TITLE ? TRUE : FALSE),
                new B ($objSearchBy == ARTICLES_SEARCH_CONTENT   ? TRUE : FALSE),
                new B ($objSearchBy == ARTICLES_SEARCH_CATEGORY  ? TRUE : FALSE),
                new B ($objSearchBy == ARTICLES_SEARCH_TAGS      ? TRUE : FALSE),
                new B ($objSearchBy == ARTICLES_SEARCH_EXCERPT   ? TRUE : FALSE)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (ARTICLES_SEARCH_BY))
                ->setName ($objFormToRender)
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('search_by'))
                ->setvalue ($objSearchWas)
                ->setLabel (new S (ARTICLES_SEARCH_BY))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('search_field'))
                ->setContainerDiv (new B (TRUE))
                ->setLabel (new S (ARTICLES_SEARCH_IN))
                ->setInputType (new S ('option'))
                ->setName (new S ('article_title'))
                ->setValue (new S (ARTICLES_SEARCH_TITLE))
                ->setLabel (new S (ARTICLES_SEARCH_TITLE))
                ->setSelected ($objWasSelected[0])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_content'))
                ->setValue (new S (ARTICLES_SEARCH_CONTENT))
                ->setLabel (new S (ARTICLES_SEARCH_CONTENT))
                ->setSelected ($objWasSelected[1])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_category'))
                ->setValue (new S (ARTICLES_SEARCH_CATEGORY))
                ->setLabel (new S (ARTICLES_SEARCH_CATEGORY))
                ->setSelected ($objWasSelected[2])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_tags'))
                ->setValue (new S (ARTICLES_SEARCH_TAGS))
                ->setLabel (new S (ARTICLES_SEARCH_TAGS))
                ->setSelected ($objWasSelected[3])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_excerpt'))
                ->setValue (new S (ARTICLES_SEARCH_EXCERPT))
                ->setLabel (new S (ARTICLES_SEARCH_EXCERPT))
                ->setSelected ($objWasSelected[4])
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setValue (new S (ARTICLES_SEARCH_BY))
                ->setName (new S ('search_submit'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'articleCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objArticleTableFTitle)->toBoolean () == TRUE) {
                    // Set what to check
                    $objToCheck = $this->getPOST (self::$objArticleTableFTitle);

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objArticleTableFTitle,
                        new S (ARTICLES_TITLE_CANNOT_BE_EMPTY));
                    } else {
                        // Check title is unique;
                        if ($this->checkArticleTitleIsUnique ($objToCheck)
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objArticleTableFTitle,
                            new S (ARTICLES_TITLE_MUST_BE_UNIQUE));
                        }
                    }
                }

                if ($this->checkPOST (self::$objArticleTableFContent)->toBoolean () == TRUE) {
                    // Set what to check
                    $objToCheck = $this->getPOST (self::$objArticleTableFContent);

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objArticleTableFContent,
                        new S (ARTICLES_CONTENT_CANNOT_BE_EMPTY));
                    }
                }

                // Check & Get;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (ARTICLES_ADD_ARTICLE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objArticleTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objArticleTableFId);

                // Do some extra work;
                if ((int) $this->ATH
                ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId)->toString () != 1) {
                    // In case we're not the BIG MAN, we're the author ...
                    $this->setExtraUpdateData (self::$objArticleTableFAuthorId, $this->ATH
                    ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId));
                }

                // Set the published date;
                $this->setExtraUpdateData (self::$objArticleTableFDatePublished,
                new S ((string) $_SERVER['REQUEST_TIME']));
                $this->setExtraUpdateData (self::$objArticleTableFDateUpdated,
                new S ((string) $_SERVER['REQUEST_TIME']));

                // Add the URL;
                if ($this->checkPOST (self::$objArticleTableFTitle)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objArticleTableFSEO,
                    URL::getURLFromString (new S ($this->getPOST (self::$objArticleTableFTitle) . _U .
                    new S ((string) $_SERVER['REQUEST_TIME']))));
                }

                // And go back to regular hours;
                $this->setName ($objFormToRender);
                if ($this->checkPOST (self::$objArticleTableFTitle)->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (ARTICLES_ADD_ARTICLE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objArticleTableFTags)
                ->setLabel (new S (ARTICLES_TAGS))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TAGS));

                // If we're the BIG MAN, we can set the author of an entry;
                if ((int) $this->ATH
                ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId)->toString () == 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objArticleTableFAuthorId)
                    ->setLabel (new S (ARTICLES_AUTHOR))
                    ->setContainerDiv (new B (TRUE));

                    // Users; (or in this case, authors);
                    foreach ($this->ATH->getUsers () as $k => $v) {
                        $this->setInputType (new S ('option'))
                        ->setName  ($v[Authentication::$objAuthUsersTableFId])
                        ->setValue ($v[Authentication::$objAuthUsersTableFId])
                        ->setLabel ($v[Authentication::$objAuthUsersTableFUName]);
                    }
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setLabel (new S (ARTICLES_STATE))
                ->setName (self::$objArticleTableFState)
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('draft'))
                ->setValue (new S ((string) self::STATE_DRAFT))
                ->setLabel (new S (ARTICLES_DRAFT))
                ->setInputType (new S ('option'))
                ->setName (new S ('published'))
                ->setValue (new S ((string) self::STATE_PUBLISHED))
                ->setLabel (new S (ARTICLES_PUBLISHED))
                ->setInputType (new S ('option'))
                ->setName (new S ('pending_review'))
                ->setValue (new S ((string) self::STATE_PENDING_REVIEW))
                ->setLabel (new S (ARTICLES_PENDING_REVIEW))
                ->setInputType (new S ('option'))
                ->setName (new S ('sticky'))
                ->setValue (new S ((string) self::STATE_STICKY))
                ->setLabel (new S (ARTICLES_STICKY))
                ->setInputType (new S ('select'))
                ->setLabel (new S (ARTICLES_CATEGORY_NAME_LABEL))
                ->setName (self::$objArticleTableFCategoryId)
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
                $this->setInputType (new S ('select'))
				->setName (self::$objArticleTableFCanComment)
				->setLabel (new S (ARTICLES_CAN_COMMENT))
				->setInputType (new S ('option'))
				->setName (new S ('yes'))
				->setValue (new S ('Y'))
				->setLabel (new S (ARTICLES_CAN_COMMENT_YES))
				->setINputType (new S ('option'))
				->setName (new S ('no'))
				->setValue (new S ('N'))
				->setLabel (new S (ARTICLES_CAN_COMMENT_NO))
				->setInputType (new S ('text'))
                ->setName (self::$objArticleTableFTitle)
                ->setLabel (new S (ARTICLES_TITLE))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objArticleTableFExcerpt)
                ->setLabel (new S (ARTICLES_EXCERPT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objArticleTableFContent)
                ->setLabel (new S (ARTICLES_CONTENT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setRows (new S ('5'))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'articleEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST (self::$objArticleTableFTitle)->toBoolean () == TRUE) {
                    // Set what to check;
                    $objToCheck = $this->getPOST (self::$objArticleTableFTitle);

                    // Check to LENGTH;
                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objArticleTableFTitle,
                        new S (ARTICLES_TITLE_CANNOT_BE_EMPTY));
                    } else {
                        // Check if the posted title is different from the one in the database;
                        if ($this->getArticleInfoById ($_GET[ADMIN_ACTION_ID],
                        self::$objArticleTableFTitle) != $objToCheck) {
                            if ($this->checkArticleTitleIsUnique ($objToCheck)->toBoolean () == FALSE) {
                                $this->setErrorOnInput (self::$objArticleTableFTitle,
                                new S (ARTICLES_TITLE_MUST_BE_UNIQUE));
                            }
                        }
                    }
                }

                if ($this->checkPOST (self::$objArticleTableFContent)->toBoolean () == TRUE) {
                    // Set what to check
                    $objToCheck = $this->getPOST (self::$objArticleTableFContent);

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objArticleTableFContent,
                        new S (ARTICLES_CONTENT_CANNOT_BE_EMPTY));
                    }
                }

                // Check & Get;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (ARTICLES_EDIT_ARTICLE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objArticleTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objArticleTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE));

                // Do some extra work;
                $this->setExtraUpdateData (self::$objArticleTableFDateUpdated,
                new S ((string) $_SERVER['REQUEST_TIME']));

                // Add the URL;
                if ($this->checkPOST (self::$objArticleTableFTitle)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objArticleTableFSEO,
                    URL::getURLFromString (new S ($this->getPOST (self::$objArticleTableFTitle) . _U .
                    $this->getArticleInfoById ($_GET[ADMIN_ACTION_ID], self::$objArticleTableFDatePublished))));
                }

                // Continue;
                $this->setInputType (new S ('submit'))
                ->setValue (new S (ARTICLES_EDIT_ARTICLE))
                ->setInputInfoMessage ($this->getHELP (new S ($objFormToRender)))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objArticleTableFTags)
                ->setLabel (new S (ARTICLES_TAGS))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TAGS));

                // If we're the BIG MAN, we can set the author of an entry;
                if ((int) $this->ATH
                ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId)->toString () == 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objArticleTableFAuthorId)
                    ->setLabel (new S (ARTICLES_AUTHOR))
                    ->setContainerDiv (new B (TRUE));

                    // Users (or in this case, authors);
                    foreach ($this->ATH->getUsers () as $k => $v) {
                        $this->setInputType (new S ('option'))
                        ->setName ($v[Authentication::$objAuthUsersTableFId])
                        ->setValue ($v[Authentication::$objAuthUsersTableFId])
                        ->setLabel ($v[Authentication::$objAuthUsersTableFUName]);
                    }
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setLabel (new S (ARTICLES_STATE))
                ->setName (self::$objArticleTableFState)
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('draft'))
                ->setValue (new S ((string) self::STATE_DRAFT))
                ->setLabel (new S (ARTICLES_DRAFT))
                ->setInputType (new S ('option'))
                ->setName (new S ('published'))
                ->setValue (new S ((string) self::STATE_PUBLISHED))
                ->setLabel (new S (ARTICLES_PUBLISHED))
                ->setInputType (new S ('option'))
                ->setName (new S ('pending_review'))
                ->setValue (new S ((string) self::STATE_PENDING_REVIEW))
                ->setLabel (new S (ARTICLES_PENDING_REVIEW))
                ->setInputType (new S ('option'))
                ->setName (new S ('sticky'))
                ->setValue (new S ((string) self::STATE_STICKY))
                ->setLabel (new S (ARTICLES_STICKY))
                ->setInputType (new S ('select'))
                ->setName (self::$objArticleTableFCategoryId)
                ->setLabel (new S (ARTICLES_CATEGORY_NAME_LABEL))
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
                $this->setInputType (new S ('select'))
				->setName (self::$objArticleTableFCanComment)
				->setLabel (new S (ARTICLES_CAN_COMMENT))
				->setInputType (new S ('option'))
				->setName (new S ('yes'))
				->setValue (new S ('Y'))
				->setLabel (new S (ARTICLES_CAN_COMMENT_YES))
				->setINputType (new S ('option'))
				->setName (new S ('no'))
				->setValue (new S ('N'))
				->setLabel (new S (ARTICLES_CAN_COMMENT_NO))
				->setInputType (new S ('text'))
                ->setName (self::$objArticleTableFTitle)
                ->setLabel (new S (ARTICLES_TITLE))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objArticleTableFExcerpt)
                ->setLabel (new S (ARTICLES_EXCERPT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objArticleTableFContent)
                ->setLabel (new S (ARTICLES_CONTENT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setRows (new S ('5'))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'articleErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objArticleTable)
                ->doToken ('%condition', new S ('%objArticleTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                // BK;
                break;

            case 'commentEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (ARTICLES_EDIT_COMMENT))
                ->setAJAXEnabledForm (new B (FALSE))
                ->setRedirect ($objURLToGoBack)
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCommentsTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCommentsTableFId)
                ->setName ($objFormToRender)
                ->setInputType (new S ('submit'))
                ->setValue (new S (ARTICLES_EDIT_COMMENT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objCommentsTableFApproved)
                ->setLabel (new S (ARTICLES_COMMENT_APPROVED))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (ARTICLES_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (ARTICLES_CAN_COMMENT_YES))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCommentsTableFComment)
                ->setLabel (new S (ARTICLES_COMMENT))
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
                    $objToCheck = $this->getPOST (new S ('add_category'));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (new S ('add_category'),
                        new S (ARTICLES_CATEGORY_NAME_IS_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck = MPTT::mpttAddUnique ($objToCheck,
                        new S ((string) $_SERVER['REQUEST_TIME'])))->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (ARTICLES_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }

                        if ($this->checkCategoryURLIsUnique (URL::getURLFromString ($objToCheck))
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (ARTICLES_CATEGORY_URL_MUST_BE_UNIQUE));
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;

                        // Do a switch ...
                        switch ($this->getPOST (new S ('add_category_as_what'))) {
                            case ARTICLES_CATEGORY_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::FIRST_CHILD);
                                break;

                            case ARTICLES_CATEGORY_LAST_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::LAST_CHILD);
                                break;

                            case ARTICLES_CATEGORY_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::PREVIOUS_BROTHER);
                                break;

                            case ARTICLES_CATEGORY_NEXT_BROTHER:
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
                ->setFieldset (new S (ARTICLES_ADD_CATEGORY))
                ->setName ($objFormToRender);
                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (new S (ARTICLES_SHOW_ALL_CATEGORIES))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('add_category_submit'))
                ->setValue (new S (ARTICLES_ADD_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('add_category'))
                ->setLabel (new S (ARTICLES_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_as_what'))
                ->setLabel (new S (ARTICLES_AS_A))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_first_child'))
                ->setLabel (new S (ARTICLES_CATEGORY_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_last_child'))
                ->setLabel (new S (ARTICLES_CATEGORY_LAST_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_previous_brother'))
                ->setLabel (new S (ARTICLES_CATEGORY_BROTHER))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_next_brother'))
                ->setLabel (new S (ARTICLES_CATEGORY_NEXT_BROTHER))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_parent_or_bro'))
                ->setLabel (new S (ARTICLES_OF_CATEGORY));

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
                    $objToCheck = self::$objMPTT->mpttAddUnique ($this->getPOST (self::$objCategoryTableFName),
                    $this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID], self::$objCategoryTableFDate));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it. We don't allow empty group names;
                        $this->setErrorOnInput (self::$objCategoryTableFName,
                        new S (ARTICLES_CATEGORY_NAME_IS_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else if ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName) != $objToCheck) {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (self::$objCategoryTableFName,
                            new S (ARTICLES_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();
                } else {
                    // Nada ...
                    $objFormHappened = new B (FALSE);
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCategoryTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCategoryTableFId)
                ->setFieldset (new S (ARTICLES_EDIT_CATEGORY))
                ->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE));
                if ($this->checkPOST (new S ('edit_category_submit'))->toBoolean () == TRUE &&
                $objFormHappened->toBoolean () == FALSE) {
                    // Set the URL ...
                    $this->setExtraUpdateData (self::$objCategoryTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objCategoryTableFName)))
                    ->setRedirect ($objURLToGoBack);
                }

                // Continue ...
                $this->setInputType (new S ('submit'))
                ->setName (new S ('edit_category_submit'))
                ->setValue (new S (ARTICLES_EDIT_CATEGORY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objCategoryTableFName)
                ->setLabel (new S (ARTICLES_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_CATEGORY))
                ->setMPTTRemoveUnique (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCategoryTableFDescription)
                ->setLabel (new S (ARTICLES_CATEGORY_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase the group node from the table;
                self::$objMPTT->mpttRemoveNode ($this
                ->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                self::$objCategoryTableFName));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));

                // BK;
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
                    self::$objAdministration->setErrorMessage (new S (ARTICLES_CATEGORY_MOVED_TO_CHILD),
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
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_SUBPAGE)), new A (Array (ARTICLES_MANAGE_ARTICLES)));

                // Do some work;
                ($this->checkPOST ()->toBoolean () == TRUE) ?
                ($objOLDCategoryId = $this->getPOST (new S ('old_category_id'))) :
                ($objOLDCategoryId = new S ('0'));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (ARTICLES_MOVE_ARTICLE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objArticleTable)
                ->setUpdateField (self::$objArticleTableFId)

                // Specific code here, need abstractization!
                ->setUpdateWhere ($this->doModuleToken (_S ('%objArticleTableFCategoryId = "%Id"')
                ->doToken ('%Id', $objOLDCategoryId)))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (ARTICLES_MOVE_ARTICLE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('old_category_id'))
                ->setLabel (new S (ARTICLES_OLD_CATEGORY))
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
                ->setName (self::$objArticleTableFCategoryId)
                ->setLabel (new S (ARTICLES_NEW_CATEGORY))
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

            case 'configurationEdit':
                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)),
                    new A (Array ($this->getPOST (new S ('what')))));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (ARTICLES_MANAGE_ARTICLES_CONFIG))
                ->setName ($objFormToRender);

                // Set redirect;
                if ($this->checkPOST ()->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);

                // Continue;
                $this->setInputType (new S ('submit'))
                ->setValue (new S (ADMIN_ACTION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S (ARTICLES_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-articles_per_page'))
                ->setValue (new S ('configurationEdit-articles_per_page'))
                ->setLabel (new S (ARTICLES_CONFIG_ARTICLES_PER_PAGE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-users_should_be_auth_to_comment'))
                ->setValue (new S ('configurationEdit-users_should_be_auth_to_comment'))
                ->setLabel (new S (ARTICLES_CONFIG_USERS_AUTH_TO_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-articles_per_page':
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
                ->setFieldset (new S (ARTICLES_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (ARTICLES_UPDATE_CONFIGURATION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('articles_per_page'))
                ->setLabel (new S (ARTICLES_CONFIG_CHOOSE))
                ->setValue ($this->getConfigKey (new S ('articles_per_page')))
                ->setRegExpType (new S ('ereg'))
                ->setRegExpErrMsg (new S (ARTICLES_CONFIG_PER_PAGE_ERROR))
                ->setPHPRegExpCheck (new S ('[0-9]'))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-users_should_be_auth_to_comment':
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
                ->setFieldset (new S (ARTICLES_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (ARTICLES_UPDATE_CONFIGURATION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('article_settings_article_auth_to_comment'))
                ->setLabel (new S (ARTICLES_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (ARTICLES_CAN_COMMENT_YES))
                ->setSelected ($this
                ->getConfigKey (new S ('article_settings_article_auth_to_comment'))
                == 'Y' ? new B (TRUE) : new B (FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue (new S ('N'))
                ->setLabel (new S (ARTICLES_CAN_COMMENT_NO))
                ->setSelected ($this
                ->getConfigKey (new S ('article_settings_article_auth_to_comment'))
                == 'N' ? new B (TRUE) : new B (FALSE))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
