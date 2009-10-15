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
class Faq extends ICommonExtension implements IFaceCommonConfigExtension {
    /* OBJECT: Identity */
    protected static $objName                   = 'Faq :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* TABLE: Faq */
    public static $objFaqTable                  = NULL;
    public static $objFaqTableFId               = NULL;
    public static $objFaqTableFQuestion         = NULL;
    public static $objFaqTableFSEO              = NULL;
    public static $objFaqTableFAnswer           = NULL;

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent;
        parent::__construct ();
        // Do the tie ...
        $this->tieInCommonConfiguration ();

        // Set the proper configuration options, from the config file;
        self::$objFaqTable                      = $this->getConfigKey (new S ('faq_table'));
        self::$objFaqTableFId                   = $this->getConfigKey (new S ('faq_table_field_id'));
        self::$objFaqTableFQuestion             = $this->getConfigKey (new S ('faq_table_field_question'));
        self::$objFaqTableFSEO                  = $this->getConfigKey (new S ('faq_table_field_seo'));
        self::$objFaqTableFAnswer               = $this->getConfigKey (new S ('faq_table_field_answer'));

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
        $objTokens[1] = 'objFaqTable';
        $objTokens[2] = 'objFaqTableFId';
        $objTokens[3] = 'objFaqTableFAnswer';
        $objTokens[4] = 'objFaqTableFQuestion';
        $objTokens[5] = 'objFaqTableFSEO';

        // Set the replacements;
        $objReplac = new A;
        $objReplac[1] = self::$objFaqTable;
        $objReplac[2] = self::$objFaqTableFId;
        $objReplac[3] = self::$objFaqTableFAnswer;
        $objReplac[4] = self::$objFaqTableFQuestion;
        $objReplac[5] = self::$objFaqTableFSEO;


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
        $this->getConfigKey (new S ('faq_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (MANAGE_FAQ), $objWP,
        $this->getHELP (new S (MANAGE_FAQ)));

        // Set ACLs;
        $objACL   = new A;
        $objACL[] = new S ('Faq.FAQ.Do.View');

        // ONLY: Faq.FAQ.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('faq_file_manage_faq')));
            self::$objAdministration->setSubMLink (new S (MANAGE_FAQ),
            $objMF, $this->getHELP (new S (MANAGE_FAQ)));
        }

        // WIDGET: Statistics for FAQs ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%q', $this->getFaqCount ()));

        // WIDGET: Latest 10 faqs ... no status query ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminWidgetLatest10')),
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
        $objACL[] = new S ('Faq.FAQ.Do.View');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if ($this->ATH->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMakeZone ($objACL[$k], $this->getObjectCLASS ());

            if ($this->ATH->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMapAdministratorToZone ($objACL[$k]);
        }
    }

    /**
     * Will check to see if the faq question is unique;
     *
     * This method will check that the faq question is unique, because we want to first make an unique SQL index on the title
     * of articles, but due to the fact that we automatically used the faq question as the rewritten URL we need to make sure
     * that no two faq questions have the same title. Also, two articles with the exact same name can be confusing for users
     * at first, and most importantly, for search engines at second;
     *
     * @param S $objFaqQuestion The faq question to check for;
     * @return boolean Will return true if the faq question is unique in the database;
     */
    public function checkFaqQuestionIsUnique (S $objFaqQuestion) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objFaqTableFQuestion'))->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', new S ('WHERE %objFaqTableFQuestion = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objFaqQuestion))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check to see if the faq question is unique;
     *
     * This method will check that the faq question is unique, because we want to first make an unique SQL index on the title
     * of articles, but due to the fact that we automatically used the faq question as the rewritten URL we need to make sure
     * that no two faq questions have the same title. Also, two articles with the exact same name can be confusing for users
     * at first, and most importantly, for search engines at second;
     *
     * @param S $objFaqQuestion The faq question to check for;
     * @return boolean Will return true if the faq question is unique in the database;
     */
    public function checkFaqQuestionURLIsUnique (S $objFaqQuestionURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objFaqTableFSEO'))->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', new S ('WHERE %objFaqTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objFaqQuestionURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the count of faqs that meet the SQL condition;
     *
     * This method will query the database and return the count of faqs that meet the passed SQL criteria. This can be used in
     * many contexts like pagination or reporting, or any other circumstance you can think of ...
     *
     * @param S $objSQLCondition The SQL condition, if passed;
     * @return integer The count of faqs that meet the SQL condition;
     */
    public function getFaqCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objFaqTableFId) AS count'))->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return an array of faqs that meet the SQL condition;
     *
     * This method will query the database and return the array of faqs that meet the passed SQL criteria. You can use this for
     * sorting and displaying a list or a table of defined FAQs;
     *
     * @param S $objSQLCondition The SQL condition, if passed;
     * @return array The result array;
     */
    public function getFaqs (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the field on a passed faq, by giving the id of that faq;
     *
     * This method will get two search criterias the faq identifier and a field in the faq table it can query for, and will try
     * to return the context of that field. It can be used for example when displaying just one FAQ and when specific information
     * is needed for that specific faq;
     *
     * @param S $objFaqId The passed faq id;
     * @param S $objFieldToGet The field to get;
     * @return array The result array;
     */
    public function getFaqInfoById (S $objFaqId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', new S ('WHERE %objFaqTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objFaqId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the field on a passed faq, by giving the question of that faq;
     *
     * This method will get two search criterias the faq question and a field in the faq table it can query for, and will try
     * to return the context of that field. It can be used for example when displaying just one FAQ and when specific information
     * is needed for that specific faq;
     *
     * @param S $objFaqQuestion The passed faq question;
     * @param S $objFieldToGet The field to get;
     * @return array The result array;
     */
    public function getFaqInfoByQuestion (S $objFaqQuestion, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', new S ('WHERE %objFaqTableFQuestion = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objFaqQuestion))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the field on a passed faq, by giving the question url of that faq;
     *
     * This method will get two search criterias the faq question and a field in the faq table it can query for, and will try
     * to return the context of that field. It can be used for example when displaying just one FAQ and when specific information
     * is needed for that specific faq;
     *
     * @param S $objFaqQuestion The passed faq question url;
     * @param S $objFieldToGet The field to get;
     * @return array The result array;
     */
    public function getFaqInfoByQuestionURL (S $objFaqQuestionURL, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objFaqTable)
        ->doToken ('%condition', new S ('WHERE %objFaqTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objFaqQuestionURL))->offsetGet (0)->offsetGet ($objFieldToGet);
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

        // Do a switch ...
        switch ($objW) {
        	case 'widgetList':
        	    // Set some SEO ... yah baby ...
        	    TPL::manageTTL ($objWA['faq_questions_title']);

        		// Set the template file ...
        		$tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
        		TPL::tpSet ($this->getFaqs (), new S ('objFaqArray'), $tpF);
        		TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
        		TPL::tpExe ($tpF);
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
            case 'manageFaq':
                // Do some work;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('faqEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('faqErase'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;

                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByQuestion':
                            case 'DescByQuestion':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objFaqTableFQuestion');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByQuestion':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByQuestion':
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
                    $objFaqTable = $this->getFaqs ($objGetCondition);
                    $objFaqTableCount = $this->getFaqCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageFaq.tp');
                    TPL::tpSet ($objFaqTable, new S ('faqTable'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objFaqTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objFaqTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('faqCreate'));
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
            case 'faqCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objFaqTableFQuestion)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objFaqTableFQuestion)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objFaqTableFQuestion,
                        new S (FAQ_QUESTION_CANNOT_BE_EMPTY));
                    }

                    if ($this->checkFaqQuestionIsUnique ($this
                    ->getPOST (self::$objFaqTableFQuestion))->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objFaqTableFQuestion,
                        new S (FAQ_QUESTION_MUST_BE_UNIQUE));
                    }

                    if ($this->checkFaqQuestionIsUnique (URL::getURLFromString ($this
                    ->getPOST (self::$objFaqTableFQuestion)))->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objFaqTableFQuestion,
                        new S (FAQ_QUESTION_URL_MUST_BE_UNIQUE));
                    }
                }

                if ($this->checkPOST (self::$objFaqTableFAnswer)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objFaqTableFAnswer)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objFaqTableFAnswer,
                        new S (FAQ_ANSWER_CANNOT_BE_EMPTY));
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (FAQ_ADD_FAQ))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objFaqTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objFaqTableFId);
                if ($this->checkPOST (self::$objFaqTableFQuestion)->toBoolean () == TRUE)
                $this->setExtraUpdateData (self::$objFaqTableFSEO, URL::getURLFromString ($this
                ->getPOST (self::$objFaqTableFQuestion)));
                $this->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (FAQ_ADD_FAQ))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objFaqTableFQuestion)
                ->setLabel (new S (FAQ_QUESTION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objFaqTableFAnswer)
                ->setTinyMCETextarea (new B (TRUE))
                ->setLabel (new S (FAQ_ANSWER))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'faqEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST (self::$objFaqTableFAnswer)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objFaqTableFAnswer)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objFaqTableFAnswer,
                        new S (FAQ_ANSWER_CANNOT_BE_EMPTY));
                    }
                }

                if ($this->checkPOST (self::$objFaqTableFQuestion)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objFaqTableFQuestion)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objFaqTableFQuestion,
                        new S (FAQ_QUESTION_CANNOT_BE_EMPTY));
                    }

                    if ($this->getPOST (self::$objFaqTableFQuestion) != $this
                    ->getFaqInfoById ($_GET[ADMIN_ACTION_ID], self::$objFaqTableFQuestion)) {
                        if ($this->checkFaqQuestionIsUnique ($this
                        ->getPOST (self::$objFaqTableFQuestion))->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objFaqTableFQuestion,
                            new S (FAQ_QUESTION_MUST_BE_UNIQUE));
                        }

                        if ($this->checkFaqQuestionIsUnique (URL::getURLFromString ($this
                        ->getPOST (self::$objFaqTableFQuestion)))->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objFaqTableFQuestion,
                            new S (FAQ_QUESTION_URL_MUST_BE_UNIQUE));
                        }
                    }

                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (FAQ_EDIT_FAQ))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objFaqTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objFaqTableFId);
                if ($this->checkPOST (self::$objFaqTableFQuestion)->toBoolean () == TRUE)
                $this->setExtraUpdateData (self::$objFaqTableFSEO, URL::getURLFromString ($this
                ->getPOST (self::$objFaqTableFQuestion)));
                $this->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setValue (new S (FAQ_EDIT_FAQ))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objFaqTableFQuestion)
                ->setLabel (new S (FAQ_QUESTION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objFaqTableFAnswer)
                ->setTinyMCETextarea (new B (TRUE))
                ->setLabel (new S (FAQ_ANSWER))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'faqErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objFaqTable)
                ->doToken ('%condition', new S ('%objFaqTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect back;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;
        }
    }
}
?>