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

############# Motto: "Knowledge is power!";
class Lyrics extends ICommonExtension implements IFaceCommonConfigExtension {
    /* OBJECT: Identity */
    protected static $objName                   = 'Lyrics :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* TABLE: Lyrics */
    public static $objLyricsTable               = NULL;
    public static $objLyricsTableFId            = NULL;
    public static $objLyricsTableFSEO           = NULL;
    public static $objLyricsTableFTitle         = NULL;
    public static $objLyricsTableFArtist        = NULL;
    public static $objLyricsTableFAlbum         = NULL;
    public static $objLyricsTableFLyrics        = NULL;
    public static $objLyricsTableFDateAdded     = NULL;
    public static $objItemsPerPage              = NULL;

    /* CONSTANTS: ALL */
    const XML_SITEMAP_PRIORITY                      = '0.6';
    const XML_SITEMAP_FREQUENCY                     = 'yearly';

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent;
        parent::__construct ();
        // Do the tie ...
        $this->tieInCommonConfiguration ();

        // Set the proper configuration options, from the config file;
        self::$objLyricsTable                   = $this->getConfigKey (new S ('lyrics_table'));
        self::$objLyricsTableFId                = $this->getConfigKey (new S ('lyrics_table_field_id'));
        self::$objLyricsTableFSEO               = $this->getConfigKey (new S ('lyrics_table_field_seo'));
        self::$objLyricsTableFTitle             = $this->getConfigKey (new S ('lyrics_table_field_title'));
        self::$objLyricsTableFArtist            = $this->getConfigKey (new S ('lyrics_table_field_artist'));
        self::$objLyricsTableFAlbum             = $this->getConfigKey (new S ('lyrics_table_field_album'));
        self::$objLyricsTableFLyrics            = $this->getConfigKey (new S ('lyrics_table_field_lyrics'));
        self::$objLyricsTableFDateAdded         = $this->getConfigKey (new S ('lyrics_table_field_date_added'));

        self::$objItemsPerPage                  = new S ('10');

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
        $objTokens[1] = 'objLyricsTable';
        $objTokens[2] = 'objLyricsTableFId';
        $objTokens[3] = 'objLyricsTableFSEO';
        $objTokens[4] = 'objLyricsTableFTitle';
        $objTokens[5] = 'objLyricsTableFArtist';
        $objTokens[6] = 'objLyricsTableFAlbum';
        $objTokens[7] = 'objLyricsTableFLyrics';
        $objTokens[8] = 'objLyricsTableFDateAdded';

        // Set the replacements;
        $objReplac = new A;
        $objReplac[1] = self::$objLyricsTable;
        $objReplac[2] = self::$objLyricsTableFId;
        $objReplac[3] = self::$objLyricsTableFSEO;
        $objReplac[4] = self::$objLyricsTableFTitle;
        $objReplac[5] = self::$objLyricsTableFArtist;
        $objReplac[6] = self::$objLyricsTableFAlbum;
        $objReplac[7] = self::$objLyricsTableFLyrics;
        $objReplac[8] = self::$objLyricsTableFDateAdded;


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
        // Do a CALL to the parent ...
        parent::tieInWithAdministration ($objAdministrationMech);

        // Do the administration menu;
        $objWP = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
        $this->getConfigKey (new S ('lyrics_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (MANAGE_LYRICS), $objWP,
        $this->getHELP (new S (MANAGE_LYRICS)));

        // Set ACLs;
        $objACL   = new A;
        $objACL[] = new S ('Lyrics.Lyrics.Do.View');

        // ONLY: Faq.FAQ.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('lyrics_file_manage_lyrics')));
            self::$objAdministration->setSubMLink (new S (MANAGE_LYRICS),
            $objMF, $this->getHELP (new S (MANAGE_LYRICS)));
        }

        /*
        // WIDGET: Statistics for FAQs ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%q', $this->getFaqCount ()));

        // WIDGET: Latest 10 faqs ... no status query ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminWidgetLatest10')),
        new B (TRUE));
        */
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
        $objACL[] = new S ('Lyrics.Lyrics.Do.View');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if ($this->ATH->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMakeZone ($objACL[$k], $this->getObjectCLASS ());

            if ($this->ATH->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMapAdministratorToZone ($objACL[$k]);
        }
    }

    public function checkLyricURLIsUnique (S $objLyricURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objLyricsTableFSEO'))->doToken ('%table', self::$objLyricsTable)
        ->doToken ('%condition', new S ('WHERE %objLyricsTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objLyricURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the count of lyrics that meet the passed SQL condition. In conjuction with other methods, it's use to either
     * show the count of items or determine if pagination is needed upon the passed condition.
     *
     * @param S $objSQLCondition The SQL condition, if passed;
     * @return integer The count of faqs that meet the SQL condition;
     */
    public function getLyricCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objLyricsTableFId) AS count'))->doToken ('%table', self::$objLyricsTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    public function getLyricCountForSearch (S $objSearchString, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getLyricCount (_S ('WHERE %objLyricsTableFTitle LIKE "%%Id%"
        OR %objLyricsTableFArtist LIKE "%%Id%"OR %objLyricsTableFAlbum LIKE "%%Id%"')
        ->doToken ('%Id', $objSearchString)->appendString (_SP)->appendString ($objSQLCondition));
    }

    public function getLyricsByPageAndSearch (S $objPageInt, S $objSearch, S $objOrdering = NULL) {
        // Do return ...
        return $this->getLyrics (_S ('WHERE %objLyricsTableFTitle LIKE "%%Id%"
        OR %objLyricsTableFArtist LIKE "%%Id%" OR %objLyricsTableFAlbum LIKE "%%Id%"
        ORDER BY %objLyricsTableFDateAdded %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering)
        ->doToken ('%Id', $objSearch));
    }

    /**
     * Will return the array of lyrics that meet the passed SQL condition. This is usefull when searching, showing the list of
     * lyrics, sorting it and many other uses, inc. pagination and other uses.
     *
     * @param S $objSQLCondition The SQL condition, if passed;
     * @return array The result array;
     */
    public function getLyrics (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objLyricsTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    public function getLyricsByPage (S $objPageInt, S $objOrdering = NULL) {
        return $this->getLyrics (_S ('ORDER BY %objLyricsTableFDateAdded %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return the lyric info (field) by id, which is a quick way to get the information associated to an Id. Usually, this
     * method will find it's use in the backend, as in the front we're used to query for the URL of the lyric, no the Id, due to
     * SEO reasons rather than anything else;
     *
     * @param S $objLyricId The passed lyric id;
     * @param S $objFieldToGet The field to get;
     * @return array The result array;
     */
    public function getLyricInfoById (S $objLyricId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objLyricsTable)
        ->doToken ('%condition', new S ('WHERE %objLyricsTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objLyricId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the lyric info by its URL, thus allowing us to have proper SEO friendly URLs, while keeping the URL (also known
     * as a permalink) in our database. This method is mainly used on the frontned, as we don't need such funcionality in the
     * backend, where search engines can't enter ...
     *
     * @param S $objLyricURL The lyric URL;
     * @param S $objFieldToGet The field to get;
     * @return array The result array;
     */
    public function getLyricInfoByURL (S $objLyricURL, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objLyricsTable)
        ->doToken ('%condition', new S ('WHERE %objLyricsTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objLyricURL))->offsetGet (0)->offsetGet ($objFieldToGet);
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
     * @param $objWidgetToRender The widget to render;
     * @return mixed Depends on the widget;
     */
    public function renderWidget (S $objW, A $objWA = NULL) {
        // Make an empty array if NULL ...
        if ($objWA == NULL) $objWA = new A;

            // XML & RSS: Do a switch ...
        switch ($objW) {
            case 'widgetXML':
                // Yo man ... woohoooooo ...
                foreach ($this->getLyrics (_S ('ORDER
                BY %objLyricsTableFDateAdded DESC LIMIT %LowerLimit, %UpperLimit')
                ->doToken ('%LowerLimit', ((int) $objWA['objId']->toString () - 1) * 25000)
                ->doToken ('%UpperLimit', 25000)) as $k => $v) {
                    // Set some requirements ...
                    $objDTE = date ('Y-m-d', (int) $v[self::$objLyricsTableFDateAdded]->toString ());
                    $objLOC = URL::staticURL (new A (Array (LYRICS_ITEM_URL, FRONTEND_SECTION_URL)),
                    new A (Array ($v[self::$objLyricsTableFSEO], FRONTEND_LYRICS_URL)));

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

        // Do a switch ...
        switch ($objW) {
        	case 'widgetList':
        	    // Check some needed requirements ...
                if ($_GET[FRONTEND_SECTION_URL] == FRONTEND_LYRICS_URL) {
                    // Set some requirements ...
                    $objPag = isset ($_GET[LYRICS_PAGE_URL]) ? $_GET[LYRICS_PAGE_URL] : new S ((string) 1);
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
                    if (isset ($_GET[LYRICS_ITEM_URL])) {
                        // Check that the article exists, before doing anything stupid ...
                        if ($this->checkLyricURLIsUnique ($objURL =
                        $_GET[LYRICS_ITEM_URL])->toBoolean () == TRUE) {
                            // Make the proper header, at first ...
                            $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                            // Give me back my free hardcore, Quoth the server, '404' ...
                            $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                            new A (Array ('404'))), new S ('Location'));
                        } else {
                            // Make me SEO ... yah!
                            TPL::manageTTL ($objTTL = $this->getLyricInfoByURL ($objURL, self::$objLyricsTableFTitle));
                            TPL::manageTTL ($objART = $this->getLyricInfoByURL ($objURL, self::$objLyricsTableFArtist));
                            TPL::manageTTL ($objALB = $this->getLyricInfoByURL ($objURL, self::$objLyricsTableFAlbum));

                            TPL::manageTAG (new S ('description'), new S ('Titlu: ' . $objTTL .
                            ', Artist: ' . $objART . ', Album: ' . $objALB . ', Lyrics: ' .
                            $this->getLyricInfoByURL ($objURL, self::$objLyricsTableFLyrics)
                            ->entityDecode (ENT_QUOTES)->doToken ('<br />', _SP)->stripTags ()->doSubStr (0, 128)));

                            // Set the template file ...
                            $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . '-Item' . TPL_EXTENSION);
                            TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                            TPL::tpSet ($objURL, new S ('objURL'), $tpF);
                            TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                            TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                            TPL::tpSet ($this, new S ('LRQ'), $tpF);
                            TPL::tpExe ($tpF);
                        }
                    } else {
                        if (isset ($_GET[LYRICS_SEARCH_URL])) {
                                // Get audio by page ...
                                $objCnt = $this->getLyricCountForSearch ($_GET[LYRICS_SEARCH_URL]);
                                $objArt = $this->getLyricsByPageAndSearch ($objPag, $_GET[LYRICS_SEARCH_URL]);
                            } else {
                                // Do me SEO, yah baby! ...
                                TPL::manageTTL (_S (FRONTEND_LYRICS_URL));

                                // Make a condition to avoid dup. title tags on different pages ...
                                if ((int) $objPag->toString () >= 1 && isset ($_GET[LYRICS_PAGE_URL]))
                                TPL::manageTTL (_S (LYRICS_PAGE_URL)->appendString (_SP)->appendString ($objPag));

                                // Set some requirements ...
                                $objArt = $this->getLyricsByPage ($objPag);
                                $objCnt = $this->getLyricCount ();
                            }

                            // Set the template file ...
                            $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                            TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                            TPL::tpSet ($objArt, new S ('objAr'), $tpF);
                            TPL::tpSet ($this, new S ('LRQ'), $tpF);
                            TPL::tpExe ($tpF);

                            // Set them paginations ...
                            if ($objCnt->toInt () > (int) self::$objItemsPerPage->toString ())
                            self::$objFrontend->setPagination ($objCnt, new I ((int) self::$objItemsPerPage->toString ()));
                        }
                } else {
                    // Do the biggest error on the PLANET ...
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (LYRICS_NEED_PROPER_SECTION),
                    new S (LYRICS_NEED_PROPER_SECTION_FIX));
                }
                // BK;
        		break;

        	case 'widgetTopN':
        	    if ($objWA['type'] == 1) {
            	    $objArt = $this->_Q (_QS ('doSELECT')
            	    ->doToken ('%what', new S ('%objLyricsTableFArtist, COUNT(%objLyricsTableFTitle) as count'))
            	    ->doToken ('%table', self::$objLyricsTable)
            	    ->doToken ('%condition', new S ('GROUP BY %objLyricsTableFArtist ORDER BY count DESC LIMIT 0, 30')));
        	    } else {
        	        $objArt = $this->_Q (_QS ('doSELECT')
                    ->doToken ('%what', new S ('%objLyricsTableFAlbum, COUNT(%objLyricsTableFTitle) as count'))
                    ->doToken ('%table', self::$objLyricsTable)
                    ->doToken ('%condition', new S ('GROUP BY %objLyricsTableFAlbum ORDER BY count DESC LIMIT 0, 30')));
        	    }

        	    // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($objArt, new S ('objAr'), $tpF);
                TPL::tpSet ($this, new S ('LRQ'), $tpF);
                TPL::tpExe ($tpF);
        	    break;

        	case 'widgetSearch':
        	    $this->renderForm (new S ('widgetSearch'));
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
            case 'manageLyrics':
                // Do some work;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('lyricEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('lyricErase'));
                            break;
                    }
                } else {
                    // Redirect to DescByDateAdded ...
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescByDateAdded'))), new S ('Location'));

                    // Set some requirements ...
                    $objGetCondition = new S;

                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByTitle':
                            case 'DescByTitle':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLyricsTableFTitle');
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
                                // BK;
                                break;

                            case 'AscByArtist':
                            case 'DescByArtist':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLyricsTableFArtist');
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
                                // BK;
                                break;

                            case 'AscByAlbum':
                            case 'DescByAlbum':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLyricsTableFAlbum');
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
                                // BK;
                                break;

                            case 'AscByDateAdded':
                            case 'DescByDateAdded':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objLyricsTableFDateAdded');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByDateAdded':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByDateAdded':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // BK;
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objLyricTable = $this->getLyrics ($objGetCondition);
                    $objLyricTableCount = $this->getLyricCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageLyrics.tp');
                    TPL::tpSet ($objLyricTable, new S ('lyricTable'), $tpF);
                    TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objLyricTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objLyricTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('lyricCreate'));
                }
                // BK;
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
            case 'widgetSearch':
                // Do the form, make it happen ...
                $this->setMethod (new S ('POST'))
                ->setName ($objFormToRender);

                // Do some work ...
                if ($this->checkPOST (new S ('search_lyrics'))->toBoolean () == TRUE) {
                    // Get the title, and check it's name ...
                    if ($this->getPOST (new S ('lyrics_search_keyword'))
                    ->toLength ()->toInt () == 0) {
                        // Well, sadly, we have an issue ...
                        $this->setErrorOnInput (new S ('lyrics_search_keyword'),
                        new S ('Nu ai completat cautarea!'));
                    } else {
                        // Go ...
                        $this->setHeaderKey ($objURLToGoBack = URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
                        LYRICS_SEARCH_URL)), new A (Array (FRONTEND_LYRICS_URL, $this->getPOST (new S ('lyrics_search_keyword'))
                        ->entityDecode (ENT_QUOTES)->stripSlashes ()))), new S ('Location'));
                    }
                }

                // Continue ...
                $this->setInputType (new S ('text'))
                ->setName (new S ('lyrics_search_keyword'));

                // If it's set ... add the VALUE ...
                if (isset ($_GET[LYRICS_SEARCH_URL]))
                $this->setValue ($_GET[LYRICS_SEARCH_URL]);

                // Continue ...
                $this->setLabel (new S ('Cuvant'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('search_lyrics'))
                ->setValue (new S ('Cauta'))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'lyricCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();
                $objErrorHappen = new B (FALSE);

                // Do some work;
                if ($this->checkPOST (self::$objLyricsTableFTitle)
                ->toBoolean () == TRUE) {
                    // Check != 0 ...
                    if ($this->getPOST (self::$objLyricsTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        self::setErrorOnInput (self::$objLyricsTableFTitle,
                        new S (LYRICS_TITLE_CANNOT_BE_EMPTY));
                        $objErrorHappen = new B (TRUE);
                    }
                }

                if ($this->checkPOST (self::$objLyricsTableFArtist)
                ->toBoolean () == TRUE) {
                    // Check != 0 ...
                    if ($this->getPOST (self::$objLyricsTableFArtist)
                    ->toLength ()->toInt () == 0) {
                        self::setErrorOnInput (self::$objLyricsTableFArtist,
                        new S (LYRICS_ARTIST_CANNOT_BE_EMPTY));
                        $objErrorHappen = new B (TRUE);
                    }
                }

                if ($this->checkPOST (self::$objLyricsTableFAlbum)
                ->toBoolean () == TRUE) {
                    // Check != 0 ...
                    if ($this->getPOST (self::$objLyricsTableFAlbum)
                    ->toLength ()->toInt () == 0) {
                        self::setErrorOnInput (self::$objLyricsTableFAlbum,
                        new S (LYRICS_ALBUM_CANNOT_BE_EMPTY));
                        $objErrorHappen = new B (TRUE);
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (LYRICS_ADD_LYRIC))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objLyricsTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objLyricsTableFId)
                ->setExtraUpdateData (self::$objLyricsTableFDateAdded,
                new S ((string) $_SERVER['REQUEST_TIME']));
                if ($this->checkPOST (self::$objLyricsTableFTitle)
                ->toBoolean () == TRUE && $objErrorHappen->toBoolean () == FALSE)
                $this->setExtraUpdateData (self::$objLyricsTableFSEO,
                URL::getURLFromString (new S ($this->getPOST (self::$objLyricsTableFTitle) . _U .
                $this->getPOST (self::$objLyricsTableFArtist) . _U .
                $this->getPOST (self::$objLyricsTableFAlbum)  . _U . $_SERVER['REQUEST_TIME'])));
                $this->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setValue (new S (LYRICS_ADD_LYRIC))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLyricsTableFTitle)
                ->setLabel (new S (LYRICS_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLyricsTableFArtist)
                ->setLabel (new S (LYRICS_ARTIST))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLyricsTableFAlbum)
                ->setLabel (new S (LYRICS_ALBUM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objLyricsTableFLyrics)
                ->setLabel (new S (LYRICS_LYRIC))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'lyricEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));
                $objErrorHappen = new B (FALSE);

                // Do some work;
                if ($this->checkPOST (self::$objLyricsTableFTitle)
                ->toBoolean () == TRUE) {
                    // Check != 0 ...
                    if ($this->getPOST (self::$objLyricsTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        self::setErrorOnInput (self::$objLyricsTableFTitle,
                        new S (LYRICS_TITLE_CANNOT_BE_EMPTY));
                        $objErrorHappen = new B (TRUE);
                    }
                }

                if ($this->checkPOST (self::$objLyricsTableFArtist)
                ->toBoolean () == TRUE) {
                    // Check != 0 ...
                    if ($this->getPOST (self::$objLyricsTableFArtist)
                    ->toLength ()->toInt () == 0) {
                        self::setErrorOnInput (self::$objLyricsTableFArtist,
                        new S (LYRICS_ARTIST_CANNOT_BE_EMPTY));
                        $objErrorHappen = new B (TRUE);
                    }
                }

                if ($this->checkPOST (self::$objLyricsTableFAlbum)
                ->toBoolean () == TRUE) {
                    // Check != 0 ...
                    if ($this->getPOST (self::$objLyricsTableFAlbum)
                    ->toLength ()->toInt () == 0) {
                        self::setErrorOnInput (self::$objLyricsTableFAlbum,
                        new S (LYRICS_ALBUM_CANNOT_BE_EMPTY));
                        $objErrorHappen = new B (TRUE);
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (LYRICS_EDIT_LYRIC))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objLyricsTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objLyricsTableFId);
                if ($this->checkPOST (self::$objLyricsTableFTitle)
                ->toBoolean () == TRUE && $objErrorHappen->toBoolean () == FALSE)
                $this->setExtraUpdateData (self::$objLyricsTableFSEO,
                URL::getURLFromString (new S ($this->getPOST (self::$objLyricsTableFTitle) . _U .
                $this->getPOST (self::$objLyricsTableFArtist) . _U .
                $this->getPOST (self::$objLyricsTableFAlbum)  . _U .
                $this->getLyricInfoById ($_GET[ADMIN_ACTION_ID],
                self::$objLyricsTableFDateAdded))));
                $this->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setValue (new S (LYRICS_EDIT_LYRIC))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLyricsTableFTitle)
                ->setLabel (new S (LYRICS_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLyricsTableFArtist)
                ->setLabel (new S (LYRICS_ARTIST))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objLyricsTableFAlbum)
                ->setLabel (new S (LYRICS_ALBUM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objLyricsTableFLyrics)
                ->setLabel (new S (LYRICS_LYRIC))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'lyricErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objLyricsTable)
                ->doToken ('%condition', new S ('%objLyricsTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect back;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;
        }
    }
}
?>