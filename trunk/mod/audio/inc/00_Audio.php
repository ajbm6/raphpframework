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

############# Motto: "Did you know Bethoven actually means stream?! ...";
class Audio extends ICommonExtension implements IFaceAudio {
    /* OBJECT: Identity */
    protected static $objName                       = 'Audio :: RA PHP Framework';
    protected $objIdentificationString              = __CLASS__;

    /* MPTT for categories */
    protected static $objMPTT                       = NULL;

    /* TABLE: Audio Files */
    public static $objAudioTable                    = NULL;
    public static $objAudioTableFId                 = NULL;
    public static $objAudioTableFFile               = NULL;
    public static $objAudioTableFArtwork            = NULL;
    public static $objAudioTableFTitle              = NULL;
    public static $objAudioTableFSEO                = NULL;
    public static $objAudioTableFArtist             = NULL;
    public static $objAudioTableFAlbum              = NULL;
    public static $objAudioTableFLyrics             = NULL;
    public static $objAudioTableFDescription        = NULL;
    public static $objAudioTableFUploadedDate       = NULL;
    public static $objAudioTableFUploaderId         = NULL;
    public static $objAudioTableFCategoryId         = NULL;
    public static $objAudioTableFApproved           = NULL;
    public static $objAudioTableFCanComment         = NULL;
	public static $objAudioTableFViews				= NULL;

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
    public static $objCommentsTableFAudioFileId     = NULL;

    /* TABLE: Categories */
    public static $objCategoryTable                 = NULL;
    public static $objCategoryTableFId              = NULL;
    public static $objCategoryTableFName            = NULL;
    public static $objCategoryTableFSEO             = NULL;
    public static $objCategoryTableFDescription     = NULL;
    public static $objCategoryTableFDate            = NULL;

    /* TABLE: Configuration */
    public static $objItemsPerPage                  = NULL;

    /* CONSTANTS: ALL */
    const XML_SITEMAP_PRIORITY                      = '0.5';
    const XML_SITEMAP_FREQUENCY                     = 'monthly';

    /* CONSTANTS: Specs ... */
    const AUDIO_PLAYER_JS_PATH                      = 'audioPlayer.js';
    const AUDIO_PLAYER_JS_STRING                    = 'audioPlayerSWFObject';

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent, parse the configuration meanwhile;
        parent::__construct ();
        // Tie in common configuration data;
        $this->tieInCommonConfiguration ();

        // Get the confiuration data ...
        self::$objAudioTable                    = $this->getConfigKey (new S ('audio_table'));
        self::$objAudioTableFId                 = $this->getConfigKey (new S ('audio_table_field_id'));
        self::$objAudioTableFFile               = $this->getConfigKey (new S ('audio_table_field_file'));
        self::$objAudioTableFArtwork            = $this->getConfigKey (new S ('audio_table_field_artwork'));
        self::$objAudioTableFTitle              = $this->getConfigKey (new S ('audio_table_field_title'));
        self::$objAudioTableFSEO                = $this->getConfigKey (new S ('audio_table_field_seo'));
        self::$objAudioTableFArtist             = $this->getConfigKey (new S ('audio_table_field_artist'));
        self::$objAudioTableFAlbum              = $this->getConfigKey (new S ('audio_table_field_album'));
        self::$objAudioTableFLyrics             = $this->getConfigKey (new S ('audio_table_field_lyrics'));
        self::$objAudioTableFDescription        = $this->getConfigKey (new S ('audio_table_field_description'));
        self::$objAudioTableFUploadedDate       = $this->getConfigKey (new S ('audio_table_field_date_uploaded'));
        self::$objAudioTableFUploaderId         = $this->getConfigKey (new S ('audio_table_field_uploader_id'));
        self::$objAudioTableFCategoryId         = $this->getConfigKey (new S ('audio_table_field_category_id'));
        self::$objAudioTableFApproved           = $this->getConfigKey (new S ('audio_table_field_approved'));
        self::$objAudioTableFCanComment         = $this->getConfigKey (new S ('audio_table_field_can_comment'));
		self::$objAudioTableFViews				= $this->getConfigKey (new S ('audio_table_field_views'));

        // Comments ...
        self::$objCommentsTable                 = $this->getConfigKey (new S ('audio_comments_table'));
        self::$objCommentsTableFId              = $this->getConfigKey (new S ('audio_comments_table_id'));
        self::$objCommentsTableFName            = $this->getConfigKey (new S ('audio_comments_table_name'));
        self::$objCommentsTableFEML             = $this->getConfigKey (new S ('audio_comments_table_email'));
        self::$objCommentsTableFURL             = $this->getConfigKey (new S ('audio_comments_table_website'));
        self::$objCommentsTableFRUId            = $this->getConfigKey (new S ('audio_comments_table_registered_user_id'));
        self::$objCommentsTableFComment         = $this->getConfigKey (new S ('audio_comments_table_comment'));
        self::$objCommentsTableFApproved        = $this->getConfigKey (new S ('audio_comments_table_approved'));
        self::$objCommentsTableFDate            = $this->getConfigKey (new S ('audio_comments_table_date'));
        self::$objCommentsTableFAudioFileId     = $this->getConfigKey (new S ('audio_comments_table_audio_file_id'));

        // Categories ...
        self::$objCategoryTable                 = $this->getConfigKey (new S ('audio_category_table'));
        self::$objCategoryTableFId              = $this->getConfigKey (new S ('audio_category_table_id'));
        self::$objCategoryTableFName            = $this->getConfigKey (new S ('audio_category_table_name'));
        self::$objCategoryTableFSEO             = $this->getConfigKey (new S ('audio_category_table_seo'));
        self::$objCategoryTableFDescription     = $this->getConfigKey (new S ('audio_category_table_description'));
        self::$objCategoryTableFDate            = $this->getConfigKey (new S ('audio_category_table_date'));

        // Configuration ...
        self::$objItemsPerPage                  = $this->getConfigKey (new S ('audio_settings_audio_items_per_page'));

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

        // Get some JSS data ...
        $this->objPathToSkinJSS = $this->getPathToSkinJSS ()->toRelativePath ();
        $this->objPathToSkinCSS = $this->getPathToSkinCSS ()->toRelativePath ();

        // CALL them specific TPL methods, add JSS & CSS to page <head'er ...
        TPL::manageJSS (new FilePath ($this->objPathToSkinJSS .
        self::AUDIO_PLAYER_JS_PATH), new S (self::AUDIO_PLAYER_JS_STRING));
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
        $objTokens = new A;
        $objTokens[1]   = 'objAudioTable';
        $objTokens[2]   = 'objAudioTableFId';
        $objTokens[3]   = 'objAudioTableFFile';
        $objTokens[4]   = 'objAudioTableFTitle';
        $objTokens[5]   = 'objAudioTableFSEO';
        $objTokens[6]   = 'objAudioTableFCategoryId';
        $objTokens[7]   = 'objCategoryTable';
        $objTokens[8]   = 'objCategoryTableFId';
        $objTokens[9]   = 'objCategoryTableFName';
        $objTokens[10]  = 'objCategoryTableFSEO';
        $objTokens[11]  = 'objCategoryTableFDescription';
        $objTokens[12]  = 'objAudioTableFArtist';
        $objTokens[13]  = 'objAudioTableFAlbum';
        $objTokens[14]  = 'objAudioTableFLyrics';
        $objTokens[15]  = 'objAudioTableFDescription';
        $objTokens[16]  = 'objAudioTableFUploaderId';
        $objTokens[17]  = 'objAudioTableFUploadedDate';
        $objTokens[18]  = 'objAudioTableFApproved';
        $objTokens[19]  = 'objAudioTableFArtwork';
        $objTokens[20]  = 'objCommentsTable';
        $objTokens[21]  = 'objCommentsTableFId';
        $objTokens[22]  = 'objCommentsTableFName';
        $objTokens[23]  = 'objCommentsTableFEML';
        $objTokens[24]  = 'objCommentsTableFURL';
        $objTokens[25]  = 'objCommentsTableFRUId';
        $objTokens[26]  = 'objCommentsTableFComment';
        $objTokens[27]  = 'objCommentsTableFApproved';
        $objTokens[28]  = 'objCommentsTableFDate';
        $objTokens[29]  = 'objCommentsTableFAudioFileId';
		$objTokens[30]	= 'objAudioTableFViews';
        $objTokens[31]  = 'objCategoyTableFDate';
        $objTokens[32]  = 'objAudioTableFCanComment';
        $objTokens[33]  = 'objAuthenticationUserTable';
        $objTokens[34]  = 'objAuthenticationUserTableFId';

        // Set the replacements;
        $objReplac = new A;
        $objReplac[1]   = self::$objAudioTable;
        $objReplac[2]   = self::$objAudioTableFId;
        $objReplac[3]   = self::$objAudioTableFFile;
        $objReplac[4]   = self::$objAudioTableFTitle;
        $objReplac[5]   = self::$objAudioTableFSEO;
        $objReplac[6]   = self::$objAudioTableFCategoryId;
        $objReplac[7]   = self::$objCategoryTable;
        $objReplac[8]   = self::$objCategoryTableFId;
        $objReplac[9]   = self::$objCategoryTableFName;
        $objReplac[10]  = self::$objCategoryTableFSEO;
        $objReplac[11]  = self::$objCategoryTableFDescription;
        $objReplac[12]  = self::$objAudioTableFArtist;
        $objReplac[13]  = self::$objAudioTableFAlbum;
        $objReplac[14]  = self::$objAudioTableFLyrics;
        $objReplac[15]  = self::$objAudioTableFDescription;
        $objReplac[16]  = self::$objAudioTableFUploaderId;
        $objReplac[17]  = self::$objAudioTableFUploadedDate;
        $objReplac[18]  = self::$objAudioTableFApproved;
        $objReplac[19]  = self::$objAudioTableFArtwork;
        $objReplac[20]  = self::$objCommentsTable;
        $objReplac[21]  = self::$objCommentsTableFId;
        $objReplac[22]  = self::$objCommentsTableFName;
        $objReplac[23]  = self::$objCommentsTableFEML;
        $objReplac[24]  = self::$objCommentsTableFURL;
        $objReplac[25]  = self::$objCommentsTableFRUId;
        $objReplac[26]  = self::$objCommentsTableFComment;
        $objReplac[27]  = self::$objCommentsTableFApproved;
        $objReplac[28]  = self::$objCommentsTableFDate;
        $objReplac[29]  = self::$objCommentsTableFAudioFileId;
		$objReplac[30]	= self::$objAudioTableFViews;
        $objReplac[31]  = self::$objCategoryTableFDate;
        $objReplac[32]  = self::$objAudioTableFCanComment;
        $objReplac[33]  = Authentication::$objAuthUsersTable;
        $objReplac[34]  = Authentication::$objAuthUsersTableFId;

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
        $this->getConfigKey (new S ('audio_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (AUDIO_MANAGE_AUDIO), $objWP,
        $this->getHELP (new S (AUDIO_MANAGE_AUDIO)));

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Audio.Audio.Do.View');
        $objACL[] = new S ('Audio.Categories.Do.View');
        $objACL[] = new S ('Audio.Comments.Do.View');
        $objACL[] = new S ('Audio.Do.Operations');
        $objACL[] = new S ('Audio.Do.Configuration');

        // ONLY: Audio.Audio.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMA = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('audio_file_manage_audio')));
            self::$objAdministration->setSubMLink (new S (AUDIO_MANAGE_AUDIO),
            $objMA, $this->getHELP (new S (AUDIO_MANAGE_AUDIO)));
        }

        // ONLY: Audio.Categories.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('audio_file_manage_categories')));
            self::$objAdministration->setSubMLink (new S (AUDIO_MANAGE_CATEGORIES),
            $objMC, $this->getHELP (new S (AUDIO_MANAGE_CATEGORIES)));
        }

        // ONLY: Audio.Comments.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('audio_file_manage_comments')));
            self::$objAdministration->setSubMLink (new S (AUDIO_MANAGE_COMMENTS),
            $objMC, $this->getHELP (new S (AUDIO_MANAGE_COMMENTS)));
        }

        // ONLY: Audio.Do.Operations
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[3])->toBoolean () == TRUE) {
            $objMM = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('audio_file_manage_operations')));
            self::$objAdministration->setSubMLink (new S (AUDIO_MANAGE_AUDIO_OPERATIONS),
            $objMM, $this->getHELP (new S (AUDIO_MANAGE_AUDIO_OPERATIONS)));
        }

        // ONLY: Audio.Do.Configuration
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[4])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('audio_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (AUDIO_MANAGE_AUDIO_CONFIG),
            $objMF, $this->getHELP (new S (AUDIO_MANAGE_AUDIO_CONFIG)));
        }

        // WIDGET: Statistics for audio files ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%t', $this->getAudioFileCount ())
        ->doToken ('%a', $this->getApprovedAudioFileCount ()),
        new B (TRUE));

        // WIDGET: Latest 10 audio files ... no status query ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminWidgetLatest10')),
        new B (TRUE));

        // WIDGET: Statistics for comments on audio files ...
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
        $objACL[] = new S ('Audio.Audio.Do.View');
        $objACL[] = new S ('Audio.Categories.Do.View');
        $objACL[] = new S ('Audio.Comments.Do.View');
        $objACL[] = new S ('Audio.Do.Operations');
        $objACL[] = new S ('Audio.Do.Configuration');

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
        TPL::manageLNK (new S ('RSS - LATEST 30 - Audio'), new S (Frontend::RSS_ALTERNATE),
        new S (Frontend::RSS_TYPE), URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
        FRONTEND_FEED_URL)), new A (Array (FRONTEND_RSS_XML, __CLASS__))));
    }

    /**
     * Will check to see if the file URL is unique;
     *
     * This method will check that the file URL is unique, because we want first to amke an unique SQL index on the URL of
     * files, but due to the fact that we automatically used the file URL as the rewritten URL we need to make sure
     * that no two files have the same URL. Also, two files with the exact same name can be configusin for users at
     * first and mostly, to search engines also;
     *
     * @param S $objAudioFileURL The file URL ...
     * @return boolean Will return true if the audio fiel url is unique;
     */
    public function checkAudioFileURLIsUnique (S $objAudioFileURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objAudioTableFSEO'))->doToken ('%table', self::$objAudioTable)
        ->doToken ('%condition', new S ('WHERE %objAudioTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAudioFileURL))->doCount ()->toInt () == 0) {
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
     * @return boolean Will return true if the category name is unique in the database;
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
     * Will return the count of files in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of files that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of files that matched the condition, if given;
     */
    public function getAudioFileCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objAudioTableFSEO) AS count'))->doToken ('%table', self::$objAudioTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of approved files in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of files that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of files that matched the condition, if given;
     */
    public function getApprovedAudioFileCount (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getAudioFileCount (_S ('WHERE %objAudioTableFApproved = "Y"')
        ->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of approved files in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of files that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of files that matched the condition, if given;
     */
    public function getApprovedAudioFileCountForCategoryURL (S $objCategoryURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getApprovedAudioFileCount (_S ('AND %objAudioTableFCategoryId = "%Id"')
        ->doToken ('%Id', $this->getCategoryInfoByURL ($objCategoryURL, self::$objCategoryTableFId))
        ->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of approved files in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getArticles, and that should return
     * the number of files that matched a given condition but with the added performance loss. This is not the case here ...
     *
     * @param S $objSQLCondition The SQL condition to get the count for;
     * @return integer Will return an integer, as the number of files that matched the condition, if given;
     */
    public function getApprovedAudioFileCountForSearch (S $objSearchString, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getApprovedAudioFileCount (_S ('AND (%objAudioTableFTitle LIKE "%%Id%"
        OR %objAudioTableFArtist LIKE "%%Id%"OR %objAudioTableFAlbum LIKE "%%Id%")')
        ->doToken ('%Id', $objSearchString)->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Will return the count of categories in the current table. It does take a condition, because in general, if there's a
     * condition to be stated, then you can do a ->doCount () on the array returned by ->getCategories, and that should return
     * the number of files that matched a given condition but with the added performance loss. This is not the case here ...
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
     * Will return the files, based on the specified condition;
     *
     * This method will return all the files defined in the database, by taking the passed SQL condition as argument. If no
     * condition is specified, then it will return ALL defined files in the table;
     *
     * @param S $objSQLCondition The SQL condition passed for files to get;
     * @return array The result array;
     */
    public function getAudioFiles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAudioTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the approved files, based on the specified condition;
     *
     * This method will return all the files defined in the database, by taking the passed SQL condition as argument. If no
     * condition is specified, then it will return ALL defined files in the table;
     *
     * @param S $objSQLCondition The SQL condition passed for files to get;
     * @return array The result array;
     */
    public function getApprovedAudioFiles (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getAudioFiles (_S ('WHERE %objAudioTableFApproved = "Y"')
        ->appendString (_SP)->appendString ($objSQLCondition));
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
     * Will return information about a given file id, while giving the correspoding field as a parameter to the function. An
     * easy way to get information about stored categories, without having too much of a problem with getting information about'em;
     *
     * @param S $objAudioId The file identifier;
     * @param S $objFieldToGet The field to retrieve for the file;
     * @return mixed Really depends on what was requested;
     */
    public function getAudioFileInfoById (S $objAudioId, S $objFieldToGet) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAudioTable)
        ->doToken ('%condition', new S ('WHERE %objAudioTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAudioId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given file url, while giving the correspoding field as a parameter to the function. An
     * easy way to get information about stored categories, without having too much of a problem with getting information about'em;
     *
     * @param S $objAudioId The file identifier;
     * @param S $objFieldToGet The field to retrieve for the file;
     * @return mixed Really depends on what was requested;
     */
    public function getAudioFileInfoByURL (S $objAudioSEO, S $objFieldToGet) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAudioTable)
        ->doToken ('%condition', new S ('WHERE %objAudioTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAudioSEO))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given category id, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories, without having too much of a problem with getting information about'em;
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
     * Will return files by the given category id;
     *
     * This method will return all files in a category id, giving us the possibility to return an array of all files defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all files are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined files in that category;
     */
    public function getAudioFilesByCategoryId (S $objCategoryId,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAudioTable)
        ->doToken ('%condition', _S ('WHERE %objAudioTableFCategoryId = "%Id"')
        ->doToken ('%Id', $objCategoryId))->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Will return files by the given category id;
     *
     * This method will return all files in a category id, giving us the possibility to return an array of all files defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all files are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined files in that category;
     */
    public function getAudioFilesByCategoryURL (S $objCategoryURL,
    S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getAudioFilesByCategoryId ($this
        ->getCategoryInfoByURL ($objCategoryURL,
        self::$objCategoryTableFId), $objSQLCondition);
    }

    /**
     * Will return files by the given category id;
     *
     * This method will return all files in a category id, giving us the possibility to return an array of all files defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all files are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined files in that category;
     */
    public function getApprovedAudioFilesByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getAudioFiles (_S ('WHERE %objAudioTableFApproved = "Y"
        ORDER BY %objAudioTableFUploadedDate %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return files by the given category id;
     *
     * This method will return all files in a category id, giving us the possibility to return an array of all files defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all files are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined files in that category;
     */
    public function getApprovedAudioFilesByPageAndSearch (S $objPageInt, S $objSearch, S $objOrdering = NULL) {
        // Do return ...
        return $this->getAudioFiles (_S ('WHERE %objAudioTableFApproved = "Y" AND (%objAudioTableFTitle LIKE "%%Id%"
        OR %objAudioTableFArtist LIKE "%%Id%" OR %objAudioTableFAlbum LIKE "%%Id%")
        ORDER BY %objAudioTableFUploadedDate %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Id', $objSearch));
    }

    /**
     * Will return files by the given category id;
     *
     * This method will return all files in a category id, giving us the possibility to return an array of all files defined
     * in a category, which in turn gives us the possibility to build blog-like pages in our sites (ex. Wordpress, Moveable Type)
     * where all files are listed for that given category id;
     *
     * @param S $objCategoryId The given category id;
     * @return array An array containing all defined files in that category;
     */
    public function getApprovedAudioFilesByCategoryURLAndPage (S $objCategoryURL, S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getAudioFilesByCategoryURL ($objCategoryURL, _S ('AND %objAudioTableFApproved = "Y"
        ORDER BY %objAudioTableFUploadedDate %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
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
    public function getCommentsByAudioFileURL (S $objAudioURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objCommentsTable)
        ->doToken ('%condition', new S ('WHERE %objCommentsTableFAudioFileId = "%Id"'))
        ->doToken ('%Id', $this->getAudioFileInfoByURL ($objAudioURL, self::$objAudioTableFId))
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
    public function getApprovedCommentsByAudioFileURL (S $objAudioURL, S $objSQLCondition = NULL) {
        // Set some requirements ...
        $objSQLCondition = $objSQLCondition == NULL ? new S : $objSQLCondition;

        // Do return ...
        return $this->getCommentsByAudioFileURL ($objAudioURL, $objSQLCondition
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
                foreach ($this->getApprovedAudioFiles (new S ('ORDER
                BY %objAudioTableFUploadedDate DESC')) as $k => $v) {
                    // Set some requirements ...
                    $objDTE = date ('Y-m-d', (int) $v[self::$objAudioTableFUploadedDate]->toString ());
                    $objLOC = URL::staticURL (new A (Array (AUDIO_ITEM_URL, FRONTEND_SECTION_URL)),
                    new A (Array ($v[self::$objAudioTableFSEO], FRONTEND_AUDIO_URL)));

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
                    foreach ($this->getApprovedAudioFiles (new S ('ORDER BY %objAudioTableFUploadedDate
                    DESC LIMIT 0, 30')) as $k => $v) {
                        // Set some requirements ...
                        $objDTE = date (DATE_RFC822, (int) $v[self::$objAudioTableFUploadedDate]->toString ());
						$objLOC = URL::staticURL (new A (Array (AUDIO_ITEM_URL, FRONTEND_SECTION_URL)),
                        new A (Array ($v[self::$objAudioTableFSEO], FRONTEND_AUDIO_URL)));
                        $objTTL = $v[self::$objAudioTableFTitle]->appendString (_DCSP)
                        ->appendString ($v[self::$objAudioTableFArtist])->appendString (_DCSP)
                        ->appendString ($v[self::$objAudioTableFAlbum]);
                        $objDSC = $v[self::$objAudioTableFDescription]
                        ->entityEncode (ENT_QUOTES)->entityDecode (ENT_QUOTES)->stripTags ();

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
                // Set some requirements ...
                if (isset ($objWA['cache_file'])) {
                    // Take the input;
                    $objCacheFile = $objWA['cache_file'];
                } else {
                    // Do cache me ...
                    $objCacheFile = new B (TRUE);
                }

                if (isset ($objWA['cache_time'])) {
                    // Get the cache time from me;
                    $objCacheTime = $objWA['cache_time'];
                } else {
                    // Do a cache for: 6 hours;
                    $objCacheTime = new I (60 * 60 * 6);
                }

                // Set the template file ...
                if ($cId = TPL::tpIni ($tpF = new FilePath ($this->getPathToSkin ()
				->toRelativePath () . $objW . TPL_EXTENSION), $objCacheTime, $objCacheFile)) {
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
                	TPL::tpSet ($objCategoryList, new S ('objCategoryList'), $tpF);
                	TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                	TPL::tpSet ($this, new S ('AUD'), $tpF);
                	TPL::tpExe ($tpF);
					TPL::tpEnd ($cId);
				}
				// BK;
                break;

            case 'widgetList':
                // Check some needed requirements ...
                if ($_GET[FRONTEND_SECTION_URL] == FRONTEND_AUDIO_URL) {
                    // Set some requirements ...
                    $objPag = isset ($_GET[AUDIO_PAGE_URL]) ? $_GET[AUDIO_PAGE_URL] : new S ((string) 1);
                    $objPag = new I ((int) $objPag->toString ());

					// Fixes for a bugged user ...
                    if ($objPag->toInt () == 0) {
                        // Make the proper header, at first ...
                        $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                        // Give me back my free hardcore, Quoth the server, '404' ...
                        $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                        new A (Array ('404'))), new S ('Location'));
                    } else {
                        // Make it a string again ...
                        $objPag = new S ((string) $objPag->toInt ());
                    }

					// Get your switches on ...
                    if (isset ($_GET[AUDIO_ITEM_URL])) {
                        // Check that the article exists, before doing anything stupid ...
                        if ($this->checkAudioFileURLIsUnique ($objURL =
                        $_GET[AUDIO_ITEM_URL])->toBoolean () == TRUE) {
                            // Make the proper header, at first ...
                            $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                            // Give me back my free hardcore, Quoth the server, '404' ...
                            $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                            new A (Array ('404'))), new S ('Location'));
                        } else {
                            // Make me SEO ... yah!
                            TPL::manageTTL ($objTTL = $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFTitle));
                            TPL::manageTTL ($objART = $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFArtist));
                            TPL::manageTTL ($objALB = $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFAlbum));

							// SEO my category, little one ...
                            TPL::manageTTL ($objCAT = MPTT::mpttRemoveUnique ($this->getCategoryInfoById ($this
                            ->getAudioFileInfoByURL ($objURL, self::$objAudioTableFCategoryId), self::$objCategoryTableFName)));

							// Get the published date ... we need it;
                            $objPBL = new S (date (self::$objFrontend->STG->getConfigKey (new S ('settings_default_date_format')),
                            (int) $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFUploadedDate)->toString ()));

                            // Get the keywords ...
                            $objKEY = $this->getAudioFileInfoByURL ($objURL,
							self::$objAudioTableFDescription)->entityDecode (ENT_QUOTES)
							->stripTags ();

                            // Get the description ...
                            $objDSC = $this->getAudioFileInfoByURL ($objURL,
							self::$objAudioTableFDescription)->entityDecode (ENT_QUOTES)
							->stripTags ();

                            // Cut it down to size ...
                            if ($objDSC->toLength ()->toInt () > META_DESCRIPTION_MAX)
                            $objDSC->doSubStr (0, META_DESCRIPTION_MAX)
                            ->appendString (_SP)->appendString (_DTE);
                            if ($objKEY->toLength ()->toInt () > META_DESCRIPTION_MAX)
                            $objKEY->doSubStr (0, META_DESCRIPTION_MAX)
                            ->appendString (_SP)->appendString (_DTE);

                            // Only description if it's bigger ...
                            if ($objDSC->toLength ()->toInt () != 0)
                            $objDSC = $objDSC->prependString ($objWA['audio_description'])->prependString (', ');

                            // Get the description ... but prepend it with the Title, Artist, Album ...
                            $objDSC->prependString ($objPBL)->prependString ($objWA['audio_uploaded_at'])
                            ->prependString (', ')->prependString ($objCAT)->prependString ($objWA['audio_genre'])
                            ->prependString (', ')->prependString ($objALB)->prependString ($objWA['audio_album'])
                            ->prependString (', ')->prependString ($objART)->prependString ($objWA['audio_artist'])
                            ->prependString (', ')->prependString ($objTTL)->prependString ($objWA['audio_title']);

							// Add them LYRICS ...
							if ($this->getAudioFileInfoByURL ($objURL,
							self::$objAudioTableFLyrics)->toLength ()->toInt () != 0) {
								// Yes ...
								$objHasLrc = new S ('Da');
							} else {
								// Nop ...
								$objHasLrc = new S ('Nu');
							}

							// Add them DESCRIPTIONS ...
							if ($this->getAudioFileInfoByURL ($objURL,
							self::$objAudioTableFDescription)->toLength ()->toInt () != 0) {
								// Yes ...
								$objHasDsc = new S ('Da');
							} else {
								// Nop ...
								$objHasDsc = new S ('Nu');
							}

							// Modify the DESCRIPTION ...
							$objDSC->appendString (', ')->appendString ($objWA['audio_lyrics'])->appendString ($objHasLrc);
							$objDSC->appendString (', ')->appendString ($objWA['audio_description'])->appendString ($objHasDsc);

                            // Add the TAG, as we have description ...
                            TPL::manageTAG (new S ('description'), $objDSC->entityEncode (ENT_QUOTES));

                            // Add the TAG, as we have keywords ...
                            if ($objKEY->toLength ()->toInt () != 0)
                            TPL::manageTAG (new S ('keywords'), $objKEY->eregReplace ('[^a-zA-Z0-9 -]', _NONE)
                            ->eregReplace (_SP, ', ')->eregReplace (', ,', ',')->entityEncode (ENT_QUOTES));

                            // Set some requirements ...
                            $objPathToItem = self::$objMPTT->mpttGetSinglePath ($this->getCategoryInfoById ($this
                            ->getAudioFileInfoByURL ($objURL, self::$objAudioTableFCategoryId), self::$objCategoryTableFName));

							// Update them views ...
							$this->_Q (_QS ('doUPDATE')->doToken ('%table', self::$objAudioTable)
							->doToken ('%condition', new S ('%objAudioTableFViews = %objAudioTableFViews + 1
							WHERE %objAudioTableFSEO = "%Id"'))->doToken ('%Id', $objURL));

                            // Set the template file ...
                            $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . '-Item' . TPL_EXTENSION);
                            TPL::tpSet ($objPathToItem, new S ('objPathToItem'), $tpF);
                            TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                            TPL::tpSet ($objURL, new S ('objURL'), $tpF);
                            TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                            TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                            TPL::tpSet ($this, new S ('AUD'), $tpF);
                            TPL::tpExe ($tpF);
                        }
                    } else {
                        if (isset ($_GET[AUDIO_CATEGORY_URL])) {
                            // Check that the category exists, before doing anything stupid ...
                            if ($this->checkCategoryURLIsUnique ($objCat =
                            $_GET[AUDIO_CATEGORY_URL])->toBoolean () == TRUE) {
                                // Make the proper header, at first ...
                                $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                                // Give me back my free hardcore, Quoth the server, '404' ...
                                $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                                new A (Array ('404'))), new S ('Location'));
                            } else {
                                // Do me SEO, yah baby! ...
                                TPL::manageTTL (MPTT::mpttRemoveUnique ($this
                                ->getCategoryInfoByURL ($objCat, self::$objCategoryTableFName)));

								// Make a condition to avoid dup. title tags on different pages ...
								if ((int) $objPag->toString () >= 1 && isset ($_GET[AUDIO_PAGE_URL]))
                                TPL::manageTTL (_S (AUDIO_PAGE_URL)->appendString (_SP)->appendString ($objPag));

                                // Set some requirements ...
                                $objCnt = $this->getApprovedAudioFileCountForCategoryURL ($objCat);
                                $objArt = $this->getApprovedAudioFilesByCategoryURLAndPage ($objCat, $objPag);
                            }
                        } else {
                            if (isset ($_GET[AUDIO_SEARCH_URL])) {
                                // Get audio by page ...
                                $objCnt = $this->getApprovedAudioFileCountForSearch ($_GET[AUDIO_SEARCH_URL]);
                                $objArt = $this->getApprovedAudioFilesByPageAndSearch ($objPag, $_GET[AUDIO_SEARCH_URL]);
                            } else {
                                // Do me SEO, yah baby! ...
                                TPL::manageTTL (_S (FRONTEND_AUDIO_URL));

								// Make a condition to avoid dup. title tags on different pages ...
								if ((int) $objPag->toString () >= 1 && isset ($_GET[AUDIO_PAGE_URL]))
                                TPL::manageTTL (_S (AUDIO_PAGE_URL)->appendString (_SP)->appendString ($objPag));

                                // Set some requirements ...
                                $objArt = $this->getApprovedAudioFilesByPage ($objPag);
                                $objCnt = $this->getApprovedAudioFileCount ();
                            }
                        }

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                        TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                        TPL::tpSet ($objArt, new S ('objAr'), $tpF);
                        TPL::tpExe ($tpF);

                        // Set them paginations ...
                        if ($objCnt->toInt () > (int) self::$objItemsPerPage->toString ())
                        self::$objFrontend->setPagination ($objCnt, new I ((int) self::$objItemsPerPage->toString ()));
                    }
                } else {
                    // Do the biggest error on the PLANET ...
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (AUDIO_NEED_PROPER_SECTION),
                    new S (AUDIO_NEED_PROPER_SECTION_FIX));
                }
                // BK;
                break;

            case 'widgetComments':
                // Check if we have the proper URL enabled ...
                if (isset ($_GET[AUDIO_ITEM_URL])) {
                    // Check if the comments are enabled ...
                    if ($this->getAudioFileInfoByURL ($objURL = $_GET[AUDIO_ITEM_URL],
                    self::$objAudioTableFCanComment) == 'Y') {
                        // Set some requirements ...
                        $objCommentIsOk = new S;
                        $objComments = $this->getApprovedCommentsByAudioFileURL ($_GET[AUDIO_ITEM_URL],
                        new S ('ORDER BY %objCommentsTableFDate DESC'));

                        // Check for status ...
                        if (isset ($_GET[AUDIO_STATUS_URL])) {
                            if ($_GET[AUDIO_STATUS_URL] == AUDIO_STATUS_OK_URL) {
                                $objCommentIsOk = new S ($objWA['comment_has_been_added']);
                            }
                        }

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                        TPL::tpSet ($objCommentIsOk, new S ('objCommentIsOk'), $tpF);
                        TPL::tpSet ($objComments, new S ('objComments'), $tpF);
                        TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                        TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                        TPL::tpExe ($tpF);

                        // Set some requirements ...
                        $objShowFrm = new B (TRUE);

                        // Check if we're allowed to show the comment form ...
                        if ($this->getConfigKey (new S ('audio_settings_audio_auth_to_comment')) == 'Y') {
                            if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
								// To show ... or not ...
                                $objShowFrm = new B (TRUE);
                            } else {
								// to show ... or not ...
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
                            $this->setRedirect (URL::rewriteURL (new A (Array (AUDIO_STATUS_URL)),
                            new A (Array (AUDIO_STATUS_OK_URL))));
                            $this->setName (new S ('commentForm'))
                            ->setExtraUpdateData (self::$objCommentsTableFDate, new S ((string) time ()))
                            ->setExtraUpdateData (self::$objCommentsTableFAudioFileId, $this
                            ->getAudioFileInfoByURL ($_GET[AUDIO_ITEM_URL], self::$objAudioTableFId))
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
                                new S (AUDIO_COMMENT_HAS_BEEN_POSTED), $this->getHELP (new S ('widgetCommentsCommentPosted'))
                                ->doToken ('%t', $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFTitle))
                                ->doToken ('%a', $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFArtist))
                                ->doToken ('%b', $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFAlbum))
                                ->doToken ('%u', $objUSR));

                                // Go deeper and notify them users ...
                                $objCommentsForItem = $this->getCommentsByAudioFileURL ($objURL);
                                foreach ($objCommentsForItem as $k => $v) {
                                    $objMAIL = new MAIL;
                                    $objMAIL->doMAIL ($this->ATH->getUserInfoById ($v[self::$objCommentsTableFRUId],
                                    Authentication::$objAuthUsersTableFEML), new S (AUDIO_COMMENT_HAS_BEEN_POSTED),
                                    $this->getHELP (new S ('widgetCommentsCommentPostedFrontend'))
                                    ->doToken ('%t', $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFTitle))
                                    ->doToken ('%a', $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFArtist))
                                    ->doToken ('%b', $this->getAudioFileInfoByURL ($objURL, self::$objAudioTableFAlbum))
                                    ->doToken ('%u', $objUSR)->doToken ('%k', URL::rewriteURL ()));
                                }
                            }

                            // End form and execute ...
                            $this->setFormEndAndExecute (new B (TRUE));
                        }
                    }
                }
                // BK;
                break;

            case 'widgetUploadForm':
                // Ya, checking the STATUS ...
                if (isset ($_GET[AUDIO_STATUS_URL])) {
                    // Get me going my dear one ...
                    $objConfKey = new S ('audio_settings_audio_form_page_status_ok');
                    $objShowFrm = new B (FALSE);
                } else {
                    // Get me going my dear one ...
                    $objConfKey = new S ('audio_settings_audio_form_page');
                    $objShowFrm = new B (TRUE);
                }

                // If we're NOT authenticated ... bang, you're DEAD ...
                if ($this->ATH->checkIfUserIsLoggedIn ()->toBoolean () == FALSE) {
                    // Get me going my dear one ...
                    $objConfKey = new S ('audio_settings_audio_form_not_authenticated');
                    $objShowFrm = new B (FALSE);

                }

                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                TPL::tpSet ($this->getConfigKey ($objConfKey), new S ('objContent'), $tpF);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($objShowFrm, new S ('objShowForm'), $tpF);
                TPL::tpSet ($this, new S ('AUD'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'widgetLastN':
                // Do CACHE ...
                if ($cId = TPL::tpIni ($tpF = new FilePath ($this->getPathToSkin ()
                ->toRelativePath () . $objW . TPL_EXTENSION), new I (180))) {
                    // Set the template file ...
                    TPL::tpSet ($this->getApprovedAudioFiles (_S ('ORDER BY %objAudioTableFUploadedDate DESC LIMIT 0, %UpperLimit')
					->doToken ('%UpperLimit', $objWA['audio_n_count'])), new S ('objLastN'), $tpF);
                    TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                    TPL::tpSet ($this, new S ('AUD'), $tpF);
                    TPL::tpExe ($tpF);
                    TPL::tpEnd ($cId);
                }
                // BK;
                break;

            case 'widgetRandomN':
				// Do CACHE ...
                if ($cId = TPL::tpIni ($tpF = new FilePath ($this->getPathToSkin ()
                ->toRelativePath () . $objW . TPL_EXTENSION), new I (180))) {
					// Set the template file ...
                	TPL::tpSet ($this->getApprovedAudioFiles (_S ('ORDER BY RAND() LIMIT 0, %UpperLimit')
					->doToken ('%UpperLimit', $objWA['audio_n_count'])), new S ('objRandomN'), $tpF);
                	TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                	TPL::tpSet ($this, new S ('AUD'), $tpF);
                	TPL::tpExe ($tpF);
					TPL::tpEnd ($cId);
				}
				// BK;
                break;

			case 'widgetRelatedN':
				// Do CACHE ...
				if ($cId = TPL::tpIni ($tpF = new FilePath ($this->getPathToSkin ()
				->toRelativePath () . $objW . TPL_EXTENSION), new I (180))) {
					// Set the template file ...
					TPL::tpSet ($this->getApprovedAudioFiles (_S ('AND %objAudioTableFArtist LIKE "%Id" LIMIT 0, %UpperLimit')
					->doToken ('%Id', $this->getAudioFileInfoByURL ($objWA['audio_song_item'], self::$objAudioTableFArtist))
					->doToken ('%UpperLimit', $objWA['audio_n_count'])), new S ('objRandomN'), $tpF);
					TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
					TPL::tpSet ($this, new S ('AUD'), $tpF);
					TPL::tpExe ($tpF);
					TPL::tpEnd ($cId);
				}
				// BK;
				break;

			case 'widgetTopN':
				// Do CACHE ...
				if ($cId = TPL::tpIni ($tpF = new FilePath ($this->getPathToSkin ()
				->toRelativePath () . $objW . TPL_EXTENSION), new I (180))) {
					// Set the template file ...
					TPL::tpSet ($this->getApprovedAudioFiles (_S ('ORDER BY %objAudioTableFViews DESC LIMIT 0, %UpperLimit')
					->doToken ('%UpperLimit', $objWA['audio_n_count'])), new S ('objRandomN'), $tpF);
					TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
					TPL::tpSet ($this, new S ('AUD'), $tpF);
					TPL::tpExe ($tpF);
					TPL::tpEnd ($cId);
				}
				// BK;
				break;

            case 'widgetRandomItem':
                // Do some work ...
                if ($this->checkPOST (new S ('search_random_item'))
                ->toBoolean () == TRUE) {
                    // Get me there ... quick ...
                    $this->setHeaderKey (URL::staticURL (new A (Array (AUDIO_ITEM_URL, FRONTEND_SECTION_URL)),
                    new A (Array ($this->getApprovedAudioFiles (_S ('ORDER BY RAND() LIMIT 1'))->offsetGet (0)
                    ->offsetGet (self::$objAudioTableFSEO), FRONTEND_AUDIO_URL))), new S ('Location'));
                }

                // Do the form, make it happen ...
                $this->setMethod (new S ('POST'))
                ->setName (new S ('audioRandomItem'))
                ->setInputType (new S ('submit'))
                ->setName (new S ('search_random_item'))
                ->setValue (new S ($objWA['audio_random']))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
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
    	    case 'manageAudio':
                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch ...
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('audioEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('audioErase'));
                            break;
                    }
                } else {
                    // Show them ordered by DESC;
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescByUploadedDate'))), new S ('Location'));

                    // Set some requirements ...
                    $objGetCondition = new S;

                    if (isset ($_GET[ADMIN_ACTION_BY])) {
                            // Do a switch ...
                            switch ($_GET[ADMIN_ACTION_BY]) {
                                case AUDIO_SEARCH_TITLE:
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('WHERE %objAudioTableFTitle');
                                    break;

                                case AUDIO_SEARCH_ARTIST:
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('WHERE %objAudioTableFArtist');
                                    break;

                                case AUDIO_SEARCH_ALBUM:
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('WHERE %objAudioTableFAlbum');
                                    break;
                            }

                            // Add LIKE searching ...
                            $objGetCondition->appendString (_SP)->appendString ('LIKE "%%Search%"')
                            ->doToken ('%Search', $_GET[ADMIN_ACTION_SEARCH]);

                            // Get the count ...
                            $objSearchCount = $this->getAudioFileCount ($objGetCondition);
                        }

                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByTitle':
                            case 'DescByTitle':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAudioTableFTitle');
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

                            case 'AscByArtist':
                            case 'DescByArtist':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAudioTableFArtist');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByArtist':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByArtist':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByAlbum':
                            case 'DescByAlbum':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAudioTableFAlbum');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByAlbum':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByAlbum':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByUploadedDate':
                            case 'DescByUploadedDate':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAudioTableFUploadedDate');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByUploadedDate':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByUploadedDate':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByCategory':
                            case 'DescByCategory':
                                if (isset ($_GET[ADMIN_ACTION_BY])) {
                                    // Make the ordered condition;
                                    $objGetCondition->doToken ('WHERE', _SP . 'AS t1 INNER JOIN %objCategoryTable AS t2
                                    ON t1.%objAudioTableFCategoryId = t2.%objCategoryTableFId WHERE');
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('ORDER BY t2.%objCategoryTableFName');
                                } else {
                                    // Make the ordered condition;
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('AS t1 INNER JOIN %objCategoryTable AS t2
                                    ON t1.%objAudioTableFCategoryId = t2.%objCategoryTableFId
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

                            case 'AscByApproved':
                            case 'DescByApproved':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAudioTableFApproved');
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
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Get based on SQL condtion ...
                    $objArticleTable = $this->getAudioFiles ($objGetCondition);
                    if (isset ($_GET[ADMIN_ACTION_BY])) { $objArticleTableCount = $objSearchCount; }
                    else { $objArticleTableCount = $this->getAudioFileCount (); }

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
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageAudio.tp');
                    TPL::tpSet ($objArticleTable, new S ('articleTable'), $tpF);
                    TPL::tpSet ($this, new S ('ART'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination ...
                    if ($objArticleTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objArticleTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('audioSearch'));
                    $this->renderForm (new S ('audioCreate'));
                }
                // BK;
                break;

    	    case 'manageCategories':
    	        // Add some requirements;
                TPL::manageJSS (new FilePath ($this->getPathToSkinJSS ()
                ->toRelativePath () . 'manageCategories.js'), new S ('manageCategories'));

                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
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
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescByDate'))), new S ('Location'));

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
                                ->appendString ('AS t1 LEFT JOIN %objAudioTable
                                AS t2 ON t1.%objCommentsTableFAudioFileId = t2.%objAudioTableFId
                                ORDER BY t2.%objAudioTableFTitle');
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
                    TPL::tpSet ($this, new S ('TXT'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
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
    public function renderForm (S $objFormToRender, A $objFA = NULL) {
        // Make them defaults ...
        if ($objFA == NULL) $objFA = new A;

        // Do a switch ...
        switch ($objFormToRender) {
            case 'uploadForm':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (AUDIO_STATUS_URL)), new A (Array (AUDIO_STATUS_OK_URL)));

                // Do some work ...
                if ($this->checkPOST (self::$objAudioTableFTitle)->toBoolean () == TRUE) {
                    // Check non empty ...
                    if ($this->getPOST (self::$objAudioTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objAudioTableFTitle,
                        new S ($objFA['error_title_empty']));
                    }
                }

                if ($this->checkPOST (self::$objAudioTableFArtist)->toBoolean () == TRUE) {
                    // Check non empty ...
                    if ($this->getPOST (self::$objAudioTableFArtist)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objAudioTableFArtist,
                        new S ($objFA['error_artist_empty']));
                    }
                }

                if ($this->checkPOST (self::$objAudioTableFAlbum)->toBoolean () == TRUE) {
                    // Check non empty ...
                    if ($this->getPOST (self::$objAudioTableFAlbum)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objAudioTableFAlbum,
                        new S ($objFA['error_album_empty']));
                    }
                }

                // Check & Get;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S ($objFA['upload_audio_file']))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAudioTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAudioTableFId)
                ->setExtraUpdateData (self::$objAudioTableFUploadedDate, new S ((string) $_SERVER['REQUEST_TIME']))
                ->setExtraUpdateData (self::$objAudioTableFUploaderId, $this->ATH
                ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId))
                ->setExtraUpdateData (self::$objAudioTableFApproved, new S ('N'))
                ->setUploadDirectory (new S ('audio/mp3/' . date ('Y/m/d', $_SERVER['REQUEST_TIME'])))
                ->setUploadImageResize (new A (Array (128 => 128, 640 => 480, 800 => 600)));

                // Add the URL ...
                if ($this->checkPOST (self::$objAudioTableFTitle)->toBoolean () == TRUE &&
                $this->checkPOST (self::$objAudioTableFArtist)->toBoolean () == TRUE ) {
                    // CLONE them little monkeys ...
                    $objURL = CLONE $this->getPOST (self::$objAudioTableFTitle);
                    $objART = CLONE $this->getPOST (self::$objAudioTableFArtist);

                    // CONCAT'enate them ...
                    $this->setExtraUpdateData (self::$objAudioTableFSEO,
                    URL::getURLFromString ($objURL->appendString (_U)->appendString ($objART)
                    ->appendString (_U)->appendString ((string) $_SERVER['REQUEST_TIME'])));
                }

                // Continue ...
                $this->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S ($objFA['upload_audio_file']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFTitle)
                ->setLabel (new S ($objFA['audio_file_title']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFCategoryId)
                ->setLabel (new S ($objFA['audio_file_category']))
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
                $this->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFArtist)
                ->setLabel (new S ($objFA['audio_file_artist']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFAlbum)
                ->setLabel (new S ($objFA['audio_file_album']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objAudioTableFFile)
                ->setLabel (new S ($objFA['audio_file_file']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objAudioTableFArtwork)
                ->setLabel (new S ($objFA['audio_file_artwork']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAudioTableFLyrics)
                ->setLabel (new S ($objFA['audio_file_lyrics']))
                ->setTinyMCETextarea (new B (TRUE))
                ->setClass (new S ('tinyMCESimple'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAudioTableFDescription)
                ->setLabel (new S ($objFA['audio_file_description']))
                ->setTinyMCETextarea (new B (TRUE))
                ->setClass (new S ('tinyMCESimple'))
                ->setContainerDiv (new B (TRUE));

                // Notify ...
                if ($this->checkFormHasErrors()->toBoolean () == FALSE) {
                    // Go ...
                    $objMAIL = new MAIL;
                    $objMAIL->doMAIL ($this->STG->getConfigKey (new S ('settings_website_notification_email')),
                    new S (AUDIO_FRONT_FILE_HAS_BEEN_UPLOADED), $this->getHELP (new S ('uploadFormEMLMessage'))
                    ->doToken ('%t', $this->getPOST (self::$objAudioTableFTitle))
                    ->doToken ('%a', $this->getPOST (self::$objAudioTableFArtist))
                    ->doToken ('%b', $this->getPOST (self::$objAudioTableFAlbum))
                    ->doToken ('%u', $this->ATH
                    ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFUName)));
                }

                // End form and execute ...
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioSearchOnFrontend':
                // Do the form, make it happen ...
                $this->setMethod (new S ('POST'))
                ->setName ($objFormToRender);

                // Do some work ...
                if ($this->checkPOST (new S ('search'))->toBoolean () == TRUE) {
                    // Get the title, and check it's name ...
                    if ($this->getPOST (new S ('audio_file_title'))
                    ->toLength ()->toInt () == 0) {
                        // Well, sadly, we have an issue ...
                        $this->setErrorOnInput (new S ('audio_file_title'),
                        new S ($objFA['error_empty_search']));
                    } else {
						// Notify ...
						$objMAIL = new MAIL;
						$objMAIL->doMAIL ($this->STG->getConfigKey (new S ('settings_website_notification_email')),
						new S (AUDIO_SEARCH_HAS_BEEN_PERFORMED), $this->getHELP (new S ('audioSearchOnFrontend'))
						->doToken ('%s', $this->getPOST (new S ('audio_file_title'))));

                        // Go ...
                        $this->setHeaderKey ($objURLToGoBack = URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
                        AUDIO_SEARCH_URL)), new A (Array (FRONTEND_AUDIO_URL, $this->getPOST (new S ('audio_file_title'))
						->entityDecode (ENT_QUOTES)->stripSlashes ()))), new S ('Location'));
                    }
                }

                // Continue ...
                $this->setInputType (new S ('submit'))
				->setName (new S ('search'))
				->setValue (new S ($objFA['search_submit']))
				->setContainerDiv (new B (TRUE))
				->setInputType (new S ('text'))
                ->setName (new S ('audio_file_title'));

                // If it's set ... add the VALUE ...
				if (isset ($_GET[AUDIO_SEARCH_URL]))
                $this->setValue ($_GET[AUDIO_SEARCH_URL]);

                // Continue ...
                $this->setLabel (new S ($objFA['search_title']))
                ->setContainerDiv (new B (TRUE))
				->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioSearch':
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
                        new S (AUDIO_SEARCH_FIELD_IS_EMPTY));

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
                $objWasSelected = new A (Array (new B ($objSearchBy == AUDIO_SEARCH_TITLE ? TRUE : FALSE),
                new B ($objSearchBy == AUDIO_SEARCH_ARTIST  ? TRUE : FALSE),
                new B ($objSearchBy == AUDIO_SEARCH_ALBUM   ? TRUE : FALSE)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUDIO_SEARCH_BY))
                ->setName ($objFormToRender)
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('search_by'))
                ->setvalue ($objSearchWas)
                ->setLabel (new S (AUDIO_SEARCH_BY))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('search_field'))
                ->setContainerDiv (new B (TRUE))
                ->setLabel (new S (AUDIO_SEARCH_IN))
                ->setInputType (new S ('option'))
                ->setName (new S ('article_title'))
                ->setValue (new S (AUDIO_SEARCH_TITLE))
                ->setLabel (new S (AUDIO_SEARCH_TITLE))
                ->setSelected ($objWasSelected[0])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_content'))
                ->setValue (new S (AUDIO_SEARCH_ARTIST))
                ->setLabel (new S (AUDIO_SEARCH_ARTIST))
                ->setSelected ($objWasSelected[1])
                ->setInputType (new S ('option'))
                ->setName (new S ('article_category'))
                ->setValue (new S (AUDIO_SEARCH_ALBUM))
                ->setLabel (new S (AUDIO_SEARCH_ALBUM))
                ->setSelected ($objWasSelected[2])
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setValue (new S (AUDIO_SEARCH_BY))
                ->setName (new S ('search_submit'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();
                $objUPDirectory = date ('Y/m/d', $_SERVER['REQUEST_TIME']);

                // Do some work ...
                if ($this->checkPOST (self::$objAudioTableFTitle)->toBoolean () == TRUE) {
                    // Check non empty ...
                    if ($this->getPOST (self::$objAudioTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objAudioTableFTitle,
                        new S (AUDIO_TITLE_REQUIRED));
                    }
                }

                if ($this->checkPOST (self::$objAudioTableFArtist)->toBoolean () == TRUE) {
                    // Check non empty ...
                    if ($this->getPOST (self::$objAudioTableFArtist)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objAudioTableFArtist,
                        new S (AUDIO_ARTIST_REQUIRED));
                    }
                }

                if ($this->checkPOST (self::$objAudioTableFAlbum)->toBoolean () == TRUE) {
                    // Check non empty ...
                    if ($this->getPOST (self::$objAudioTableFAlbum)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objAudioTableFAlbum,
                        new S (AUDIO_ALBUM_REQUIRED));
                    }
                }

                // Check & Get;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUDIO_ADD_AUDIO))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAudioTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAudioTableFId)
                ->setExtraUpdateData (self::$objAudioTableFUploaderId, $this->ATH
                ->getCurrentUserInfoById (Authentication::$objAuthUsersTableFId))
                ->setUploadDirectory (new S ('audio/mp3/' . $objUPDirectory))
                ->setUploadImageResize (new A (Array (128 => 128, 640 => 480, 800 => 600)));

                // Add the URL ...
                if ($this->checkPOST (self::$objAudioTableFTitle)->toBoolean () == TRUE &&
                $this->checkPOST (self::$objAudioTableFArtist)->toBoolean () == TRUE ) {
                    // CLONE them little monkeys ...
                    $objURL = CLONE $this->getPOST (self::$objAudioTableFTitle);
                    $objART = CLONE $this->getPOST (self::$objAudioTableFArtist);

                    // CONCAT'enate them ...
                    $this->setExtraUpdateData (self::$objAudioTableFSEO,
                    URL::getURLFromString ($objURL->appendString (_U)->appendString ($objART)
                    ->appendString (_U)->appendString ((string) $_SERVER['REQUEST_TIME'])));
                }

                // Continue ...
                $this->setName ($objFormToRender);
                if ($this->checkPOST (self::$objAudioTableFTitle)->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_ADD_AUDIO))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFApproved)
                ->setLabel (new S (AUDIO_FILE_APPROVED))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName  (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (AUDIO_FILE_APPROVED_YES))
                ->setInputType (new S ('option'))
                ->setName  (new S ('no'))
                ->setValue (new S ('N'))
                ->setLabel (new S (AUDIO_FILE_APPROVED_NO))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFCanComment)
                ->setLabel (new S (AUDIO_CAN_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (AUDIO_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (AUDIO_CAN_COMMENT_YES))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFTitle)
                ->setLabel (new S (AUDIO_FILE_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFCategoryId)
                ->setLabel (new S (AUDIO_FILE_CATEGORY))
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
                $this->setInputType (new S ('file'))
                ->setName (self::$objAudioTableFFile)
                ->setLabel (new S (AUDIO_FILE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objAudioTableFArtwork)
                ->setLabel (new S (AUDIO_FILE_ARTWORK))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFArtist)
                ->setLabel (new S (AUDIO_FILE_ARTIST))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFAlbum)
                ->setLabel (new S (AUDIO_FILE_ALBUM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAudioTableFLyrics)
                ->setLabel (new S (AUDIO_FILE_LYRICS))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAudioTableFDescription)
                ->setLabel (new S (AUDIO_FILE_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioEdit':
                // God send us our info ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'audioEditInfo.tp');
                TPL::tpSet ($this, new S ('AUD'), $tpF);
                TPL::tpExe ($tpF);

                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));
                $objUPDirectory = date ('Y/m/d', (int) $this->getAudioFileInfoById ($_GET[ADMIN_ACTION_ID],
                self::$objAudioTableFUploadedDate)->toString ());

                // Check & Get;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUDIO_EDIT_AUDIO))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAudioTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objAudioTableFId)
                ->setUploadDirectory (new S ('audio/mp3/' . $objUPDirectory))
                ->setUploadImageResize (new A (Array (128 => 128, 640 => 480, 800 => 600)));

                // Add the URL ...
                if ($this->checkPOST (self::$objAudioTableFTitle)->toBoolean () == TRUE &&
                $this->checkPOST (self::$objAudioTableFArtist)->toBoolean () == TRUE ) {
                    // CLONE them little monkeys ...
                    $objURL = CLONE $this->getPOST (self::$objAudioTableFTitle);
                    $objART = CLONE $this->getPOST (self::$objAudioTableFArtist);

                    // CONCAT'enate them ...
                    $this->setExtraUpdateData (self::$objAudioTableFSEO,
                    URL::getURLFromString ($objURL->appendString (_U)->appendString ($objART)
                    ->appendString (_U)->appendString ($this->getAudioFileInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objAudioTableFUploadedDate))));
                }

                // Continue ...
                $this->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_EDIT_AUDIO))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFApproved)
                ->setLabel (new S (AUDIO_FILE_APPROVED))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName  (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (AUDIO_FILE_APPROVED_YES))
                ->setInputType (new S ('option'))
                ->setName  (new S ('no'))
                ->setValue (new S ('N'))
                ->setLabel (new S (AUDIO_FILE_APPROVED_NO))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFCanComment)
                ->setLabel (new S (AUDIO_CAN_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (AUDIO_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (AUDIO_CAN_COMMENT_YES))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFTitle)
                ->setLabel (new S (AUDIO_FILE_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objAudioTableFCategoryId)
                ->setLabel (new S (AUDIO_FILE_CATEGORY))
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
                $this->setInputType (new S ('file'))
                ->setName (self::$objAudioTableFFile)
                ->setLabel (new S (AUDIO_FILE))
                ->setFileController (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objAudioTableFArtwork)
                ->setLabel (new S (AUDIO_FILE_ARTWORK))
                ->setFileController (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFArtist)
                ->setLabel (new S (AUDIO_FILE_ARTIST))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAudioTableFAlbum)
                ->setLabel (new S (AUDIO_FILE_ALBUM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAudioTableFLyrics)
                ->setLabel (new S (AUDIO_FILE_LYRICS))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAudioTableFDescription)
                ->setLabel (new S (AUDIO_FILE_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));
                $objUPDirectory = date ('Y/m/d/', (int) $this->getAudioFileInfoById ($_GET[ADMIN_ACTION_ID],
                self::$objAudioTableFUploadedDate)->toString ());

                if ($this->getCommentCount (_S ('WHERE %objCommentsTableFAudioFileId = "%Id"')
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))->toInt () != 0) {
                    self::$objAdministration->setErrorMessage (new S
                    (AUDIO_HAS_COMMENTS), $objURLToGoBack);
                } else {
                    // Remove them files ...
                    $objPath = new FilePath ('upd/audio/mp3/' . $objUPDirectory);

                    // First: check to see if file is there ...
                    $objArtF = $this->getAudioFileInfoById ($_GET[ADMIN_ACTION_ID], self::$objAudioTableFFile);
                    // Go ...
                    if ($objArtF->toLength ()->toInt () != 0) {
                        // Kill'em ALL ...
                        UNLINK ($objPath . $objArtF);
                    }

                    // Second: check to see if artwork is there ...
                    $objArtF = $this->getAudioFileInfoById ($_GET[ADMIN_ACTION_ID], self::$objAudioTableFArtwork);
                    // Go ...
                    if ($objArtF->toLength ()->toInt () != 0) {
                        // Kill'em ALL ...
                        UNLINK ($objPath . ''         . $objArtF);
                        UNLINK ($objPath . '128_128_' . $objArtF);
                        UNLINK ($objPath . '640_480_' . $objArtF);
                        UNLINK ($objPath . '800_600_' . $objArtF);
                    }

                    // Do erase it ...
                    $this->_Q (_QS ('doDELETE')
                    ->doToken ('%table', self::$objAudioTable)
                    ->doToken ('%condition', new S ('%objAudioTableFId = "%Id"'))
                    ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                    // Do a redirect, and get the user back where he belongs;
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // BK;
                break;

            case 'commentEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUDIO_EDIT_COMMENT))
                ->setAJAXEnabledForm (new B (FALSE))
                ->setRedirect ($objURLToGoBack)
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCommentsTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCommentsTableFId)
                ->setName ($objFormToRender)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_EDIT_COMMENT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objCommentsTableFApproved)
                ->setLabel (new S (AUDIO_COMMENT_APPROVED))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue  (new S ('N'))
                ->setLabel (new S (AUDIO_CAN_COMMENT_NO))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (AUDIO_CAN_COMMENT_YES))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCommentsTableFComment)
                ->setLabel (new S (AUDIO_COMMENT))
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

                // Do some work;
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
                        new S (AUDIO_CATEGORY_NAME_IS_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (AUDIO_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }

                        if ($this->checkCategoryURLIsUnique (URL::getURLFromString ($objToCheck))
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (AUDIO_CATEGORY_URL_MUST_BE_UNIQUE));
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;

                        switch ($this->getPOST (new S ('add_category_as_what'))) {
                            case AUDIO_CATEGORY_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::FIRST_CHILD);
                                break;

                            case AUDIO_CATEGORY_LAST_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::LAST_CHILD);
                                break;

                            case AUDIO_CATEGORY_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::PREVIOUS_BROTHER);
                                break;

                            case AUDIO_CATEGORY_NEXT_BROTHER:
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
                ->setFieldset (new S (AUDIO_ADD_CATEGORY))
                ->setName ($objFormToRender);
                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (new S (AUDIO_SHOW_ALL_CATEGORIES))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('add_category_submit'))
                ->setValue (new S (AUDIO_ADD_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 ,.!?;&-]'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category'))
                ->setLabel (new S (AUDIO_CATEGORY_NAME_LABEL))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_as_what'))
                ->setLabel (new S (AUDIO_AS_A))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_first_child'))
                ->setLabel (new S (AUDIO_CATEGORY_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_last_child'))
                ->setLabel (new S (AUDIO_CATEGORY_LAST_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_previous_brother'))
                ->setLabel (new S (AUDIO_CATEGORY_BROTHER))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_next_brother'))
                ->setLabel (new S (AUDIO_CATEGORY_NEXT_BROTHER))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_parent_or_bro'))
                ->setLabel (new S (AUDIO_OF_CATEGORY));

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
                        new S (AUDIO_CATEGORY_NAME_IS_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else if ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName) != $objToCheck) {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (self::$objCategoryTableFName,
                            new S (AUDIO_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCategoryTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCategoryTableFId)
                ->setFieldset (new S (AUDIO_EDIT_CATEGORY));
				if ($this->checkPOST (self::$objCategoryTableFName)->toBoolean () == TRUE &&
				$objFormHappened->toBoolean () == FALSE) {
					// Add the URL processing ...
					$this->setExtraUpdateData (self::$objCategoryTableFSEO, URL::getURLFromString ($this
					->getPOST (self::$objCategoryTableFName)));
					$this->setRedirect ($objURLToGoBack);
				}

                // Continue ...
				$this->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('edit_category_submit'))
                ->setValue (new S (AUDIO_EDIT_CATEGORY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objCategoryTableFName)
                ->setLabel (new S (AUDIO_CATEGORY_NAME_LABEL))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 ,.!?;&-]'))
                ->setMPTTRemoveUnique (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCategoryTableFDescription)
                ->setLabel (new S (AUDIO_CATEGORY_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase the group node from the table;
                self::$objMPTT->mpttRemoveNode ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                self::$objCategoryTableFName));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
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
                    self::$objAdministration->setErrorMessage (new S (AUDIO_CATEGORY_MOVED_TO_CHILD),
                    $objURLToGoBack);
                } else {
                    // Move nodes;
                    self::$objMPTT->mpttMoveNode ($objThatIsMoved,
                    $objWhereToMove, $_GET[ADMIN_ACTION_TYPE]);
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // BK;
                break;

            case 'categoryMoveOperation':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_SUBPAGE)), new A (Array (AUDIO_MANAGE_AUDIO)));

                // Do some work;
                ($this->checkPOST ()->toBoolean () == TRUE) ?
                ($objOLDCategoryId = $this->getPOST (new S ('old_category_id'))) :
                ($objOLDCategoryId = new S ('0'));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUDIO_MOVE_ARTICLE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAudioTable)
                ->setUpdateField (self::$objAudioTableFId)

                // Specific code here, need abstractization!
                ->setUpdateWhere ($this->doModuleToken (_S ('%objAudioTableFCategoryId = "%Id"')
                ->doToken ('%Id', $objOLDCategoryId)))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_MOVE_ARTICLE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('old_category_id'))
                ->setLabel (new S (AUDIO_OLD_CATEGORY))
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
                ->setName (self::$objAudioTableFCategoryId)
                ->setLabel (new S (AUDIO_NEW_CATEGORY))
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
                ->setFieldset (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender);

                // Set redirect;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $this->setRedirect ($objURLToGoBack);
                }

                // Continue;
                $this->setInputType (new S ('submit'))
                ->setValue (new S (ADMIN_ACTION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S (AUDIO_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-message_on_top_of_upload_form_page'))
                ->setValue (new S ('configurationEdit-message_on_top_of_upload_form_page'))
                ->setLabel (new S (AUDIO_CONFIG_MESSAGE_ON_UPLOAD))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-message_upon_upload_ok'))
                ->setValue (new S ('configurationEdit-message_upon_upload_ok'))
                ->setLabel (new S (AUDIO_CONFIG_MESSAGE_ON_UPLOAD_OK))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-message_if_not_auth'))
                ->setValue (new S ('configurationEdit-message_if_not_auth'))
                ->setLabel (new S (AUDIO_CONFIG_MESSAGE_USER_NOT_AUTH))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-items_per_page'))
                ->setValue (new S ('configurationEdit-items_per_page'))
                ->setLabel (new S (AUDIO_CONFIG_ITEMS_PER_PAGE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-users_should_be_auth_to_comment'))
                ->setValue (new S ('configurationEdit-users_should_be_auth_to_comment'))
                ->setLabel (new S (AUDIO_CONFIG_USER_LOGGED_TO_COMM))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-message_on_top_of_upload_form_page':
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
                ->setFieldset (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('audio_settings_audio_form_page'))
                ->setLabel (new S (AUDIO_CONFIG_DEFAULT))
                ->setValue ($this->getConfigKey (new S ('audio_settings_audio_form_page')))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-message_upon_upload_ok':
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
                ->setFieldset (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('audio_settings_audio_form_page_status_ok'))
                ->setLabel (new S (AUDIO_CONFIG_DEFAULT))
                ->setValue ($this->getConfigKey (new S ('audio_settings_audio_form_page_status_ok')))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-message_if_not_auth':
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
                ->setFieldset (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('audio_settings_audio_form_not_authenticated'))
                ->setLabel (new S (AUDIO_CONFIG_DEFAULT))
                ->setValue ($this->getConfigKey (new S ('audio_settings_audio_form_not_authenticated')))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-items_per_page':
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
                ->setFieldset (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('audio_settings_audio_items_per_page'))
                ->setLabel (new S (AUDIO_CONFIG_DEFAULT))
                ->setValue ($this->getConfigKey (new S ('audio_settings_audio_items_per_page')))
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
                ->setFieldset (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUDIO_UPDATE_CONFIGURATION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('audio_settings_audio_auth_to_comment'))
                ->setLabel (new S (AUDIO_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S ('Yes'))
                ->setSelected ($this
                ->getConfigKey (new S ('audio_settings_audio_auth_to_comment'))
                == 'Y' ? new B (TRUE) : new B (FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue (new S ('N'))
                ->setLabel (new S ('No'))
                ->setSelected ($this
                ->getConfigKey (new S ('audio_settings_audio_auth_to_comment'))
                == 'N' ? new B (TRUE) : new B (FALSE))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
