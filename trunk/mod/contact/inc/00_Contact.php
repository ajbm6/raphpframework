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

############# Motto: "Keep in touch ...";
class Contact extends ICommonExtension implements IFaceCommonConfigExtension {
    /* OBJECT: Identity */
    protected static $objName                   = 'Contact :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* TABLE: Contact messages */
    public static $objContactTable              = NULL;
    public static $objContactTableFId           = NULL;
    public static $objContactTableFMessage      = NULL;
    public static $objContactTableFSubjectId    = NULL;
    public static $objContactTableFEMAIL        = NULL;
    public static $objContactTableFResolved     = NULL;
    public static $objContactTableFComment      = NULL;
    public static $objContactTableFReceived     = NULL;
    public static $objContactTableFLastEdited   = NULL;

    /* TABLE: Subjects */
    public static $objContactSubjectTable       = NULL;
    public static $objContactSubjectFId         = NULL;
    public static $objContactSubjectFTitle      = NULL;

    /* REGEXPses */
    const REGEXP_JS_SUBJECT                     = '[^a-zA-Z0-9 ,_\.\"\?\!\@\#\$\%\^\&\*\~\:\; \(\)\|\-]';

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent;
        parent::__construct ();
        // Do the tie ...
        $this->tieInCommonConfiguration ();

        // Set some configuration defaults, taking them from the config file;
        self::$objContactTable                  = $this->getConfigKey (new S ('contact_table_message'));
        self::$objContactTableFId               = $this->getConfigKey (new S ('contact_table_message_field_id'));
        self::$objContactTableFMessage          = $this->getConfigKey (new S ('contact_table_message_content'));
        self::$objContactTableFSubjectId        = $this->getConfigKey (new S ('contact_table_message_subject_id'));
        self::$objContactTableFEMAIL            = $this->getConfigKey (new S ('contact_table_message_email'));
        self::$objContactTableFResolved         = $this->getConfigKey (new S ('contact_table_message_resolved'));
        self::$objContactTableFComment          = $this->getConfigKey (new S ('contact_table_message_comment'));
        self::$objContactTableFReceived         = $this->getConfigKey (new S ('contact_table_message_received'));
        self::$objContactTableFLastEdited       = $this->getConfigKey (new S ('contact_table_message_last_edited'));

        // Subjects ..
        self::$objContactSubjectTable           = $this->getConfigKey (new S ('contact_table_subject'));
        self::$objContactSubjectFId             = $this->getConfigKey (new S ('contact_table_subject_field_id'));
        self::$objContactSubjectFTitle          = $this->getConfigKey (new S ('contact_table_subject_title'));

        // DB: Auto-CREATE:
        $objQueryDB = new FileContent ($this->getPathToModule ()->toRelativePath () .
        _S . CFG_DIR . _S .  __CLASS__ . SCH_EXTENSION);

        // Make a FOREACH on each ...
        foreach (_S ($objQueryDB->toString ())
        ->fromStringToArray (RA_SCHEMA_HASH_TAG) as $k => $v) {
            // Make'em ...
            $this->_Q (_S ($v));
        }

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
        $objTokens = new A;
        $objTokens[1]   = 'objContactTable';
        $objTokens[2]   = 'objContactTableFId';
        $objTokens[3]   = 'objContactTableFMessage';
        $objTokens[4]   = 'objContactTableFSubjectId';
        $objTokens[5]   = 'objContactTableFEMAIL';
        $objTokens[6]   = 'objContactTableFResolved';
        $objTokens[7]   = 'objContactTableFComment';
        $objTokens[8]   = 'objContactTableFReceived';
        $objTokens[9]   = 'objContactTableFLastEdited';
        $objTokens[10]  = 'objContactSubjectTable';
        $objTokens[11]  = 'objContactSubjectFId';
        $objTokens[12]  = 'objContactSubjectFTitle';

        // Set the replacements;
        $objReplac = new A;
        $objReplac[1]   = self::$objContactTable;
        $objReplac[2]   = self::$objContactTableFId;
        $objReplac[3]   = self::$objContactTableFMessage;
        $objReplac[4]   = self::$objContactTableFSubjectId;
        $objReplac[5]   = self::$objContactTableFEMAIL;
        $objReplac[6]   = self::$objContactTableFResolved;
        $objReplac[7]   = self::$objContactTableFComment;
        $objReplac[8]   = self::$objContactTableFReceived;
        $objReplac[9]   = self::$objContactTableFLastEdited;
        $objReplac[10]  = self::$objContactSubjectTable;
        $objReplac[11]  = self::$objContactSubjectFId;
        $objReplac[12]  = self::$objContactSubjectFTitle;

        // Do a CALL to the parent;
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
        $this->getConfigKey (new S ('contact_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (CONTACT_DASHBOARD), $objWP,
        $this->getHELP (new S (CONTACT_MANAGE_MESSAGES)));

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Contact.Messages.Do.View');
        $objACL[] = new S ('Contact.Subjects.Do.View');
        $objACL[] = new S ('Contact.Do.Configuration');

        // ONLY: Contact.Messages.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMM = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('contact_file_manage_messages')));
            self::$objAdministration->setSubMLink (new S (CONTACT_MANAGE_MESSAGES),
            $objMM, $this->getHELP (new S (CONTACT_MANAGE_MESSAGES)));
        }

        // ONLY: Contact.Subjects.Do.View
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMS = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('contact_file_manage_subjects')));
            self::$objAdministration->setSubMLink (new S (CONTACT_MANAGE_SUBJECTS),
            $objMS, $this->getHELP (new S (CONTACT_MANAGE_SUBJECTS)));
        }

        // ONLY: Contact.Do.Configuration
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('contact_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (CONTACT_MANAGE_CONFIG),
            $objMC, $this->getHELP (new S (CONTACT_MANAGE_CONFIG)));
        }

        // WIDGET: Statistics for contact messages;
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%r', $this->getMessageCount (new S ('WHERE %objContactTableFResolved = "Y"')))
        ->doToken ('%u', $this->getMessageCount (new S ('WHERE %objContactTableFResolved = "N"')))
        ->doToken ('%m', $this->getMessageCount ())
        ->doToken ('%s', $this->getSubjectCount ()));
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
        $objACL[] = new S ('Contact.Messages.Do.View');
        $objACL[] = new S ('Contact.Subjects.Do.View');
        $objACL[] = new S ('Contact.Do.Configuration');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if ($this->ATH->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMakeZone ($objACL[$k], $this->getObjectCLASS ());

            if ($this->ATH->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMapAdministratorToZone ($objACL[$k]);
        }
    }

    /**
     * Will return the contact messages saved in the database;
     *
     * This method will return an array of all the messages stored in the database. We provide it with a parameter, where you
     * can specify a condition to be met by the SQL string.
     *
     * @param S $objSQLCondition The passed SQL condition, if passed ...
     * @return array The array of returned messages;
     */
    public function getMessages (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objContactTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the number of messages that meet the specified SQL condition;
     *
     * This method will take an SQL condition as a parameter and will return the count of the messages that meet that certain
     * criteria. If no argument is specified, you guessed it, it will do a count on ALL the messages currently existing in the
     * messages table. Usually, this is a good way to determine if pagination is needed;
     *
     * @param S $objSQLCondition The passed SQL condition, if passed ...
     * @return array The array of returned messages;
     */
    public function getMessageCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objContactTableFId) AS count'))->doToken ('%table', self::$objContactTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the requested message info, by matching the field to be returned, with the id;
     *
     * This method expects a message id, and a field to query for, and will return that specific field for the passed id. Both
     * parameters are necessary. It's a bad idea to use this function just to get info for messages that have been return as
     * an array, as invoking it 10x, 50x etc. is a bad idea. You should think of making an a JOIN/SELECT for other information ...
     *
     * @param S $objMessageId The expected message id;
     * @param S $objFieldToGet The field to get ...
     * @return mixed Depends on what field needs to be returned ...
     */
    public function getMessageInfoById (S $objMessageId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objContactTable)
        ->doToken ('%condition', new S ('WHERE %objContactTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objMessageId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the contact subjects saved in the database;
     *
     * This method will return an array of all the subjects stored in the database. We provide it with a parameter, where you
     * can specify a condition to be met by the SQL string.
     *
     * @param S $objSQLCondition The passed SQL condition, if passed ...
     * @return array The array of returned subjects;
     */
    public function getSubjects (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objContactSubjectTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the number of subjects that meet the specified SQL condition;
     *
     * This method will take an SQL condition as a parameter and will return the count of the subjects that meet that certain
     * criteria. If no argument is specified, you guessed it, it will do a count on ALL the subjects currently existing in the
     * subjects table. Usually, this is a good way to determine if pagination is needed;
     *
     * @param S $objSQLCondition The passed SQL condition, if passed ...
     * @return array The array of returned subjects;
     */
    public function getSubjectCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objContactSubjectFId) AS count'))->doToken ('%table', self::$objContactSubjectTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the requested subject info, by matching the field to be returned, with the id;
     *
     * This method expects a subject id, and a field to query for, and will return that specific field for the passed id. Both
     * parameters are necessary. It's a bad idea to use this function just to get info for subjects that have been return as
     * an array, as invoking it 10x, 50x etc. is a bad idea. You should think of making an a JOIN/SELECT for other information ...
     *
     * @param S $objMessageId The expected subject id;
     * @param S $objFieldToGet The field to get ...
     * @return mixed Depends on what field needs to be returned ...
     */
    public function getSubjectInfoById (S $objSubjectId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objContactSubjectTable)
        ->doToken ('%condition', new S ('WHERE %objContactSubjectFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objSubjectId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the requested subject info, by matching the field to be returned, with the title;
     *
     * This method expects a subject title, and a field to query for, and will return that specific field for the passed title.
     * Both parameters are necessary. It's a bad idea to use this function just to get info for subjects that have been return as
     * an array, as invoking it 10x, 50x etc. is a bad idea. You should think of making an a JOIN/SELECT for other information ...
     *
     * @param S $objMessageId The expected subject id;
     * @param S $objFieldToGet The field to get ...
     * @return mixed Depends on what field needs to be returned ...
     */
    public function getSubjectInfoByTitle (S $objSubjectTitle, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objContactSubjectTable)
        ->doToken ('%condition', new S ('WHERE %objContactSubjectFTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objSubjectTitle))->offsetGet (0)->offsetGet ($objFieldToGet);
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
        // Make a default ...
        if ($objWA == NULL) $objWA = new A;

        // Do a switch ...
        switch ($objW) {
            case 'widgetContactForm':
            	// Set some requirements ...
            	isset ($_GET[CONTACT_STATUS_URL]) ? $objBIT = new B (FALSE) : $objBIT = new B (TRUE);
            	isset ($_GET[CONTACT_STATUS_URL]) ? $objTXT = $this->getConfigKey (new S ('contact_page_message_content_ok')) :
            	$objTXT = $this->getConfigKey (new S ('contact_page_message_content'));

                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                TPL::tpSet ($objTXT->entityDecode (ENT_QUOTES), new S ('objContactText'), $tpF);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($objBIT, new S ('objShowForm'), $tpF);
                TPL::tpSet ($this, new S ('CNT'), $tpF);
                TPL::tpExe ($tpF);

                // Do me SEO, yah baby! ...
                TPL::manageTTL (new S ($objWA['page_title']));
                TPL::manageTAG (new S ('description'), $this->getConfigKey (new S ('contact_page_message_content'))
                ->entityDecode (ENT_QUOTES)->stripTags ()->doSubStr (0, META_DESCRIPTION_MAX)->appendString (_DTE));
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
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality ...
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderBackendPage (S $objPageToRender) {
        # Get a specific CSS file for this controller ...
        TPL::manageCSS (new FilePath ($this->getPathToSkinCSS ()
        ->toRelativePath () . $objPageToRender . CSS_EXTENSION), $objPageToRender);

        # Do pagination ...
        if (isset ($_GET[ADMIN_PAGINATION])) {
            $objLowerLimit = (int) $_GET[ADMIN_PAGINATION]->toString () * 10 - 10;
            $objUpperLimit = 10;
        } else {
            $objLowerLimit = 0;
            $objUpperLimit = 10;
        }

        // Do a switch on the rendered page ...
        switch ($objPageToRender) {
            case 'manageMessages':
                // Set some predefines;
                $objGetCondition = new S;
                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Switch between actions;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_VIEW:
                            // Set the template file ...
                            $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'viewMessage.tp');
                            TPL::tpSet ($_GET[ADMIN_ACTION_ID], new S ('objGETId'), $tpF);
                            TPL::tpSet ($this, new S ('CNT'), $tpF);
                            TPL::tpExe ($tpF);

                            // Do the form, make it happen ...
                            $this->renderForm (new S ('messageOperations'));
                            $this->renderForm (new S ('messageSend'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('messageErase'));
                            break;
                    }
                } else {
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescByReceived'))), new S ('Location'));

                    // Set some requirements;
                    $objGetCondition = new S;

                    // Check if there's an action to take;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByReceived':
                            case 'DescByReceived':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objContactTableFReceived');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByReceived':
                                        $objGetCondEFEFEFition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByReceived':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByLastEdited':
                            case 'DescByLastEdited':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objContactTableFLastEdited');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByLastEdited':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByLastEdited':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscBySubject':
                            case 'DescBySubject':
                                // Set some requirements;
                                $objGetCondition->appendString ('AS t1 INNER JOIN %objContactSubjectTable AS t2
                                ON t1.%objContactTableFSubjectId = t2.%objContactSubjectFId
                                ORDER BY %objContactSubjectFTitle');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscBySubject':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescBySubject':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;
                        }
                    }

                    // Add some LIMITS;
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objArticleTable = $this->getMessages ($objGetCondition);
                    $objArticleTableCount = $this->getMessageCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageMessages.tp');
                    TPL::tpSet ($objArticleTable, new S ('articleTable'), $tpF);
                    TPL::tpSet ($this, new S ('CNT'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination;
                    if ($objArticleTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objArticleTableCount);
                }
                // Break out;
                break;

            case 'manageSubjects':
                // Set some predefines;
                $objGetCondition = new S;

                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Switch between actions;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('subjectEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('subjectErase'));
                            break;
                    }
                } else {
                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByTitle':
                            case 'DescByTitle':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objContactSubjectFTitle');
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
                        }
                    }

                    // Add some LIMITs;
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objArticleTable = $this->getSubjects ($objGetCondition);
                    $objArticleTableCount = $this->getSubjectCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageSubjects.tp');
                    TPL::tpSet ($objArticleTable, new S ('articleTable'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination ...
                    if ($objArticleTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objArticleTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('subjectCreate'));
                }
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
            case 'contactForm':
                // Set the URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array ('Status')), new A (Array ('Ok')));

                // Set some requirements;
                $objPHPEMLRegExpCheck = new S (Authentication::REGEXP_PHP_CHECK_EMAIL);

                // Get some configuration parameters ...
                $objNameFrm = $objFA['form_name'];
                $objSubject = $objFA['field_subject'];
                $objEMAIL   = $objFA['field_email'];
                $objMessage = $objFA['field_message'];
                $objSendFrm = $objFA['form_submit_contact'];
                $objErrorE  = $objFA['error_must_enter_valid_email'];
                $objErrorM  = $objFA['error_must_enter_message'];
                $objErrorI  = $objFA['error_entered_email_not_valid'];

                // Do some work;
                if ($this->checkPOST (self::$objContactTableFEMAIL)
                ->toBoolean () == TRUE) {
                    // Check the EMAIL was set;
                    if ($this->getPOST (self::$objContactTableFEMAIL)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objContactTableFEMAIL,
                        $objErrorE);
                    }
                }

                if ($this->checkPOST (self::$objContactTableFMessage)
                ->toBoolean () == TRUE) {
                    // Check the MESSAGE is not empty;
                    if ($this->getPOST (self::$objContactTableFMessage)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objContactTableFMessage,
                        $objErrorM);
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S ($objNameFrm))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objContactTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objContactTableFId)
                ->setExtraUpdateData (self::$objContactTableFReceived,
                new S ((string) $_SERVER['REQUEST_TIME']))
                ->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('contact_submit'))
                ->setValue ($objSendFrm)
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objContactTableFSubjectId)
                ->setLabel ($objSubject)
                ->setContainerDiv (new B (TRUE));

                // Get the subjects ...
                foreach ($this->getSubjects () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objContactSubjectFId])
                    ->setValue ($v[self::$objContactSubjectFId])
                    ->setLabel ($v[self::$objContactSubjectFTitle]);
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objContactTableFEMAIL)
                ->setLabel ($objEMAIL)
                ->setRegExpType (new S ('preg'))
                ->setRegExpErrMsg ($objErrorI)
                ->setPHPRegExpCheck ($objPHPEMLRegExpCheck)
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objContactTableFMessage)
                ->setLabel ($objMessage)
                ->setRows (new S ('10'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setClass (new S ('tinyMCESimple'))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));

                // Do some after work;
                if ($this->checkPOST (new S ('contact_submit'))->toBoolean () == TRUE) {
                    if ($this->checkFormHasErrors ()->toBoolean () == FALSE) {
                        // Set some requirements ...
                        $objMAIL = new MAIL;

                        // Set From: MAIL header;
                        $objMAIL->setFrom ($this->getPOST (self::$objContactTableFEMAIL));
                        $objMAIL->doMAIL ($this->getConfigKey (new S ('contact_message_email')),
                        $this->getSubjectInfoById ($this->getPOST (self::$objContactTableFSubjectId),
                        self::$objContactSubjectFTitle), $this->getPOST (self::$objContactTableFMessage));
                    }

                    // Do a redirect, to the ok page ... if everything is OK;
                    if ($this->checkFormHasErrors ()->toBoolean () == FALSE)
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // Break out ...
                break;

            case 'messageSend':
                // Set the URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST (new S ('submit_resend_message'))->toBoolean () == TRUE) {
                    // Set some requirements ...
                    $objTo          = $this->getConfigKey (new S ('contact_message_email'));
                    $objSubjectId   = $this->getMessageInfoById ($_GET[ADMIN_ACTION_ID], self::$objContactTableFSubjectId);
                    $objSubject     = $this->getSubjectInfoById ($objSubjectId, self::$objContactSubjectFTitle);
                    $objFrom        = $this->getMessageInfoById ($_GET[ADMIN_ACTION_ID], self::$objContactTableFEMAIL);
                    $objMessage     = $this->getMessageInfoById ($_GET[ADMIN_ACTION_ID], self::$objContactTableFMessage);

                    // Set some requirements ...
                    $objMAIL = new MAIL;

                    // Set From: MAIL header ...
                    $objMAIL->setFrom ($objFrom);
                    $objMAIL->doMAIL ($objTo, $objSubject, $objMessage);

                    // Do a redirect, and get the user back where he belongs;
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_RESEND_MESSAGE))
                ->setName ($objFormToRender)
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit_resend_message'))
                ->setValue (new S (CONTACT_RESEND_MESSAGE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'messageOperations':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_EDIT_COMMENT_AND_STATUS))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objContactTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objContactTableFId)
                ->setExtraUpdateData (self::$objContactTableFLastEdited,
                new S ((string) $_SERVER['REQUEST_TIME']))
                ->setName ($objFormToRender)
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_EDIT_COMMENT_AND_STATUS))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objContactTableFResolved)
                ->setLabel (new S (CONTACT_MESSAGE_RESOLVED))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('yes'))
                ->setValue (new S ('Y'))
                ->setLabel (new S (CONTACT_MESSAGE_RESOLVED_YES))
                ->setInputType (new S ('option'))
                ->setName (new S ('no'))
                ->setValue (new S ('N'))
                ->setLabel (new S (CONTACT_MESSAGE_RESOLVED_NO))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objContactTableFComment)
                ->setLabel (new S (CONTACT_MESSAGE_COMMENT))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));

                if ($this->checkPOST (self::$objContactTableFComment)->toBoolean() == TRUE) {
                    // Do a redirect, and get the user back where he belongs;
                    if ($this->checkFormHasErrors ()->toBoolean () == FALSE)
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                break;

            case 'messageErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objContactTable)
                ->doToken ('%condition', new S ('%objContactTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'subjectCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objContactSubjectFTitle)
                ->toBoolean () == TRUE) {
                    // Check that the subject title is not empty!;
                    if ($this->getPOST (self::$objContactSubjectFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objContactSubjectFTitle,
                        new S (CONTACT_SUBJECT_CANNOT_BE_EMPTY));
                    }
                }

                // Get AJAX
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_ADD_SUBJECT))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objContactSubjectTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objContactSubjectFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_ADD_SUBJECT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objContactSubjectFTitle)
                ->setLabel (new S (CONTACT_SUBJECT))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_SUBJECT))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'subjectEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST (self::$objContactSubjectFTitle)
                ->toBoolean () == TRUE) {
                    // Check that the subject title is not empty!;
                    if ($this->getPOST (self::$objContactSubjectFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objContactSubjectFTitle,
                        new S (CONTACT_SUBJECT_CANNOT_BE_EMPTY));
                    }
                }

                // Get AJAX
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_EDIT_SUBJECT))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objContactSubjectTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objContactSubjectFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_EDIT_SUBJECT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objContactSubjectFTitle)
                ->setLabel (new S (CONTACT_SUBJECT))
                ->setJSRegExpReplace (new S (self::REGEXP_JS_SUBJECT))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'subjectErase':
                // The URL to go back too;
                $objURLToGoBack  = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));
                $objSQLCondition = new S ('WHERE %objContactTableFSubjectId = "%Id"');

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objContactSubjectTable)
                ->doToken ('%condition', new S ('%objContactSubjectFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'configurationEdit':
                // Set the URL to go back too;
                $objURLToGoBack = new S;

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // The URL to go back too;
                    $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)),
                    new A (Array ($this->getPOST (new S ('what')))));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_MANAGE_CONFIG))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_CONFIG_DO))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S (CONTACT_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-contact_address'))
                ->setValue (new S ('configurationEdit-contact_address'))
                ->setLabel (new S (CONTACT_CONFIG_EMAIL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-page_content'))
                ->setValue (new S ('configurationEdit-page_content'))
                ->setLabel (new S (CONTACT_CONFIG_PAGE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-ok_page_content'))
                ->setValue (new S ('configurationEdit-ok_page_content'))
                ->setLabel (new S (CONTACT_CONFIG_SUCCES_PAGE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-contact_address':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_MANAGE_CONFIG))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_MANAGE_CONFIGURATION_UPDATE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('contact_message_email'))
                ->setLabel (new S (CONTACT_DEFAULT))
                ->setValue ($this->getConfigKey (new S ('contact_message_email')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-page_content':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_MANAGE_CONFIGURATION_UPDATE))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_MANAGE_CONFIGURATION_UPDATE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('contact_page_message_content'))
                ->setLabel (new S (CONTACT_PAGE_CONTENT))
                ->setValue ($this->getConfigKey (new S ('contact_page_message_content')))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-ok_page_content':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (CONTACT_MANAGE_CONFIGURATION_UPDATE))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (CONTACT_MANAGE_CONFIGURATION_UPDATE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('contact_page_message_content_ok'))
                ->setLabel (new S (CONTACT_PAGE_CONTENT_OK))
                ->setValue ($this->getConfigKey (new S ('contact_page_message_content_ok')))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>