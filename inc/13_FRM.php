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

############# Motto: "Imagination is more important than knowledge. Knowledge is limited. (Albert E.)";
/**
 * Concrete CLASS providing functionality of auto-forms to our framework. Very basic CRUD operations are handled this way, where
 * update and insertion operations are handled through our auto-forms CLASS and R/D operations are handled through separate methods;
 *
 * @package RA-Auto-Forms
 * @category RA-Concrete-Core
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License
 * @access public
 */
class FRM extends GPH implements IFaceFRM {
    /**
     * @staticvar $objFormPassedExecution Switch to detect if the current form passed execution or not ...
     * @staticvar $objFormInFieldSet Switch to see if the current form has a fieldset around it ...
     * @staticvar $objPostPassedToSession Switch that will tell if the current _POST has been passed to _SESSION['POST']
     * @staticvar $objUpdateTableName Container of the table string on which we need to operate for auto-update/insert
     * @staticvar $objUpdateUpdateField Field for the WHERE = clause, needed on an update ...
     * @staticvar $objUpdateUpdateId Update id, (or string) on which to work, for auto-update/insert
     * @staticvar $objImageTimestampPrefix Container for the current _SERVER['REQUEST_TIME'], to map-out file uploads ...
     * @staticvar $objFormErrorField Container for some <inputs, that have detected errors on them ...
     * @staticvar $objUpdateTableFields Container for fields in objUpdateTableName, that need to be updated manually ...
     * @staticvar $objOtherSQLData Container for additional calculated data ...
     * @staticvar $objFormDataContainer Container for attributes set only on the <form tag
     * @staticvar $objDataContainer Container (numeric) for all inputs defined in a form ...
     * @staticvar $objDataCountInput Container for the current input, increased by calling a new input method ...
     * @staticvar $objDataToForm Switch to detect on what to set the current default attributes (id, class, name, title, etc ...)
     * @staticvar $objOptGroupOpen Memorize the last opened <optgroup tag ...
     * @staticvar @objOpenedSelectName Memorize the name="" of the last open <select tag ...
    */
    protected static $objName                   = 'FRM :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    private static $objFormStarted              = NULL;
    private static $objFormPassedExecution      = NULL;
    private static $objFormInFieldSet           = NULL;
    private static $objPostPassedToSession      = NULL;
    private static $objUpdateTableName          = NULL;
    private static $objUpdateUpdateField        = NULL;
    private static $objUpdateUpdateId           = NULL;
    private static $objUpdateWhere              = NULL;
    private static $objUpdateSELECTData         = NULL;
    private static $objImageTimestampPrefix     = NULL;
    private static $objFormErrorField           = NULL;
    private static $objFormAjaxErrorField       = NULL;
    private static $objUpdateTableFields        = NULL;
    private static $objOtherSQLData             = NULL;
    private static $objFormDataContainer        = NULL;
    private static $objDataContainer            = NULL;
    private static $objDataCountInput           = NULL;
    private static $objDataToForm               = NULL;
    private static $objOptGroupOpen             = NULL;
    private static $objOpenedSelectName         = NULL;
    private static $objRegisteredFunctionArray  = NULL;

	// CONSTRUCT;
    public function __construct () {
        parent::__construct ();
        if (!TheFactoryMethodOfSingleton::checkHasInstance (__CLASS__)) {
            self::setExeTime (new S (__CLASS__));
            // Make required containers;
            self::$objFormErrorField = new A;
            self::$objUpdateTableFields = new A;
            self::$objOtherSQLData = new A;

            // Make the data container;
            self::$objDataContainer = new A;
            self::$objFormDataContainer = new A;
            self::$objDataToForm = new I (0);
            self::$objDataCountInput = new I (0);
            self::$objPostPassedToSession = new I (0);
            self::$objFormInFieldSet = new I (0);
            self::$objFormStarted = new I (0);
            self::$objFormPassedExecution = new I (1);
            self::$objOptGroupOpen = new I (0);
            self::$objUpdateTableName = new S (_NONE);
            self::$objUpdateUpdateField = new S (_NONE);
            self::$objUpdateUpdateId = new S (_NONE);
            self::$objUpdateWhere = new S (_NONE);
            self::$objUpdateSELECTData = new A;
            self::$objRegisteredFunctionArray = new A;
            // Set POST to SESSION['POST'];
            if (!empty ($_POST)) {
                // Convert _POST to an A, get ALL HTML processed;
                $_SESSION['POST'] = new A;
                foreach ($_POST as $k => $v) {
                    // Do the samba ...
                    $v = new S ($v);
                    $_SESSION['POST'][$k] = $v->entityEncode (ENT_QUOTES)->doToken ("\r\n", new S);
                    $_SESSION['POST'][$k] = $_SESSION['POST'][$k]->trimLeft ();
                    $_SESSION['POST'][$k] = $_SESSION['POST'][$k]->trimRight ();
                }

                // Yes, we did;
                self::$objPostPassedToSession = new I (1);
            } else {
                if ((self::checkPOST ()->toBoolean () == TRUE && (count ($_SESSION['POST']) > 0))) {
                    self::$objPostPassedToSession = new I (1);
                } else {
                    self::$objPostPassedToSession = new I (0);
                }
            }
        } else {
            // Return the instantiated object;
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set an error on a given input name, with the error string defined in the second parameter ...
     *
     * This method, setErrorOnInput, will set an error on a given <input name. For the moment, it accepts only a string
     * variable, which means that in can be 'hijacked' so it will desplay a .tp file, only if the developer gets the content
     * of that executed tp, prior to setting the error on the input. In short, whatever you set in the second parameter, is
     * shown as an error message to the user.
     *
     * @param string $inputWithError The name of the input with error on it;
     * @param string $errorMessage The error message to be shown upon error;
     * @return object The current object instance;
    */
    public static final function setErrorOnInput (S $inputWithError, S $errorMessage) {
        // Set some predefines;
        self::$objFormErrorField[$inputWithError] = $errorMessage->entityEncode (ENT_QUOTES);
        self::$objFormPassedExecution = new I (0);

        // Return to chain ...
        return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
    }

    /**
     * Will retrieve all ajax errors registered for this form;
     *
     * This method will return the exact code we need to to AJAX form validation. We need at the point of calling to discard all
     * output that's been done by the script since now, and we need to JSON encode the error array we received. It's a nice way
     * to enable ajaxified forms, with as little code as possible;
     */
    public function getAjaxErrors () {
        // ONLY if it's an JS CALL;
        if (isset ($_GET['Ajax'])) {
            // Get errors on inputs;
            self::getAjaxErrorsOnInput ();
        }
    }

    /**
     * Will set form autoSQLData for a given form, if the form has auto-update/insert enabled ...
     *
     * This method, setFormAutoSQLData, will set additional info for a form, on a basis of key/var pairs. That means that
     * if the PHP script needs to wait for user input to be able to calculate some information, then there's no need to do
     * manual SQL, when you could set the fields to the corresponding values, by just passing the proper key/var pairs here ...
     *
     * @param string $fieldKeyName The name of the field to be updated;
     * @param string $fieldValue The value of the field to be inserted;
     * @return object the current object instance;
    */
    public static final function setExtraUpdateData (S $fieldKeyName, S $fieldValue) {
        // Set and ENCODE the extra data;
        self::$objOtherSQLData[$fieldKeyName] = $fieldValue->entityEncode (ENT_QUOTES);
        // Return to chain ...
        return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
    }

    /**
     * Will set the <form action=''
     *
     * This method, setAction, will set the <form action='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The action="" attribute on a form;
     * @return object The current object instance;
    */
    public static final function setAction (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('action'), $objFormAttributeVar);
    }

    /**
     * Will set the <form enctype=''
     *
     * This method, setEnctype, will set the <form enctype='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The enctype="" attribute on a form;
     * @return object The current object instance;
    */
    public static final function setEnctype (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('enctype'), $objFormAttributeVar);
    }

    /**
     * Will set the <form method=''
     *
     * This method, setMethod, will set the <form method='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The method="" attribute on a form;
     * @return object The current object instance;
    */
    public static final function setMethod (S $objFormAttributeVar) {
        switch ($objFormAttributeVar) {
            case 'POST':
            case 'GET':
                // Do return ...
                self::setAttribute (new S ('action'), URL::rewriteURL ());
                return self::setAttribute (new S ('method'), $objFormAttributeVar->toLower ());
                break;
            default:
                // Error me proudly;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (FORM_METHOD_IS_INVALID),
                new S (FORM_METHOD_IS_INVALID_FIX));
        }
    }

    /**
     * Will set the <form target=''
     *
     * This method, setTarget, will set the <form target='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The target="" attribute on a form;
     * @return object The current object instance;
    */
    public static final function setTarget (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('target'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input id=''
     *
     * This method, setId, will set the <form or <input id='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The id="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setId (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('id'), $objFormAttributeVar);
    }

    /**
     * Will set the <input jQuery mask ...
     *
     * This method, setMask, will set the <form or <input jQuery mask attribute, on this specific input you call. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The jQuery mask attribute on HTML elements;
     * @return object The current object instance;
    */

    public static final function setMask (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('mask'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input class=''
     *
     * This method, setClass, will set the <f or <input class='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The class="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setClass (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('class'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input name=''
     *
     * This method, setName, will set the <f or <input name='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The name="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setName (S $objFormAttributeVar) {
        // Also, set the id, when setting the name, cause they're both DOM unique;
        self::setAttribute (new S ('id'), $objFormAttributeVar);
        // Do return ...
        return self::setAttribute (new S ('name'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input title=''
     *
     * This method, setTitle, will set the <f or <input title='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The title="" attribute on HTML elements (that support it);
     * @return object The current object instance;
    */
    public static final function setTitle (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('title'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input style=''
     *
     * This method, setStyle, will set the <f or <input style='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The style="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setStyle (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('style'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input dir=''
     *
     * This method, setTextDirection, will set <input dir='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The dir="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setTextDirection (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('dir'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input acceskey=''
     *
     * This method, setAccessKey, will set <input accesskey='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar the accesskey="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setAccessKey (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('accesskey'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input tabindex=''
     *
     * This method, setTabIndex, will set <input tabindex='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The tabindex="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setTabIndex (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('tabindex'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input lang=''
     *
     * This method, setLanguage, will set <input lang='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The lang="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setLanguage (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('lang'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onsubmit=''
     *
     * This method, setOnSubmit, will set <input onsubmit='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onsubmit="" attribute on form;
     * @return object The current object instance;
    */
    public static final function setOnSubmit (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onsubmit'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onreset=''
     *
     * This method, setOnReset, will set <input onreset='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onreset="" attribute on form;
     * @return object The current object instance;
    */
    public static final function setOnReset (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onreset'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onclick=''
     *
     * This method, setOnClick, will set <input onclick='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onclick="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnClick (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onclick'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input ondblclick=''
     *
     * This method, setOnDblClick, will set <input ondblclick='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The ondblclick="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnDblClick (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('ondblclick'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onmousedown=''
     *
     * This method, setOnMouseDown, will set <input onmousedown='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onmousedown="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnMouseDown (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onmousedown'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onmouseup=''
     *
     * This method, setOnMouseUp, will set <input onmouseup='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onmouseup=""" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnMouseUp (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onmouseup'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onmouseover=''
     *
     * This method, setOnMouseOver, will set <input onmouseover='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onmouseover=""" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnMouseOver (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onmouseover'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onmousemove=''
     *
     * This method, setOnMouseMove, will set <input onmousemove='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onmousemove="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnMouseMove (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onmousemove'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onmouseout=''
     *
     * This method, setOnMouseOut, will set <input onmouseout='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onmouseout="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnMouseOut (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onmouseout'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onkeypress=''
     *
     * This method, setOnKeyPress, will set <input onkeypress='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onkeypress="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnKeyPress (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onkeypress'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onkeydown=''
     *
     * This method, setOnKeyDown, will set <input onkeydown='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onkeydown="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnKeyDown (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onkeydown'), $objFormAttributeVar);
    }

    /**
     * Will set the <form or <input onkeyup=''
     *
     * This method, setOnKeyUp, will set <input onkeyup='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onkeyup="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnKeyUp (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onkeyup'), $objFormAttributeVar);
    }

    /**
     * Will set the <input type=''
     *
     * This method, setInputType, will set <input type='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The type of input to make;
     * @return object The current object instance;
    */
    public static final function setInputType (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('type'), $objFormAttributeVar);
    }

    /**
     * Will set the <input value=''
     *
     * This method, setValue, will set <input value='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttribute The value="" attribute on a input;
     * @return object The current object instance;
    */
    public static final function setValue (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('value'), $objFormAttributeVar);
    }

    /**
     * Will set the <input size=''
     *
     * This method, setSize, will set <input size='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormATtributeVar The size="" attribute on HTML elements;
     * @return object The current objesct instance;
    */
    public static final function setSize (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('size'), $objFormAttributeVar);
    }

    /**
     * Will set the <input type='file' accept=''
     *
     * This method, setAcceptFileType, will set <input accept='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The accept="" attribute on forms;
     * @return object The current object instance;
    */
    public static final function setAcceptFileType (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('accept'), $objFormAttributeVar);
    }

    /**
     * Will set the <input maxlength=''
     *
     * This method, setMaxLength, will set <input maxlength='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The maxlength="" attribute on inputs;
     * @return object The current object instance;
    */
    public static final function setMaxLength (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('maxlength'), $objFormAttributeVar);
    }

    /**
     * Will set the <input regexptype=''
     *
     * This method, setRegExpType, will set <input regextype='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The kind of RegExp to apply to the inputs;
     * @return object The current object instance;
    */
    public static final function setRegExpType (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('reg_type'), $objFormAttributeVar);
    }

    /**
     * Will set the <input jsregexp=''
     *
     * This method, setJSRegExpReplace, will set <input jsreg='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The RegExp to be used in JS input RegExp Checker;
     * @return object The current object instance;
    */
    public static final function setJSRegExpReplace (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('reg_javascript_replace'), $objFormAttributeVar);
    }

    /**
     * Will set the <input regexpmsg=''
     *
     * This method, setRegExpErrMsg, will set <input regexpmsg='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The error message to be shown if it fails PHP RegExp;
     * @return object The current object instance;
    */
    public static final function setRegExpErrMsg (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('reg_err_msg'), $objFormAttributeVar);
    }

    /**
     * Will set the <input phpregexp=''
     *
     * This method, setPHPRegExpCheck, will set <input phpreg='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The PHP RegExp to check input for;
     * @return object The current object instance;
    */
    public static final function setPHPRegExpCheck (S $objFormAttributeVar) {
        if (isset (self::$objDataContainer[self::$objDataCountInput]['reg_err_msg'])) {
            return self::setAttribute (new S ('reg_check'), $objFormAttributeVar);
        } else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (PHP_REGEXP_CHECK_ERRMSG),
            new S (PHP_REGEXP_CHECK_ERRMSG_FIX));
        }
    }

    /**
     * Will set the <input infomessage=''
     *
     * This method will set the input info message, which it's actually a <div, containing an user-specified message, which can
     * be used to describe the below field, so that the visitor will be guided on how to complete the form, avoiding to get a
     * validation error;
     *
     * @param S $objFormAttributeVar The message to set;
     * @return object The current object instance;
     */
    public static final function setInputInfoMessage (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('input_info_msg'),
        $objFormAttributeVar->entityEncode (ENT_QUOTES));
    }

    /**
     * Will set the input tooltip;
     *
     * This method, will set the input tooltip attribute, that can be used by, for example, our jQuery based tooltip JS, so that
     * every input can have a hovering info box, that isn't part of the design and that will help the user fill-in the form
     * without much trouble;
     *
     * @param S $objFormAttributeVar
     * @return unknown
     */
    public static final function setToolTip (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('tooltip'),
        $objFormAttributeVar->entityEncode (ENT_QUOTES));
    }

    /**
     * Will set the <input type='image' src=''
     *
     * This method, setImageSoure, will set <input src='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar Set the image src="" attribute on inputs;
     * @return object The current object instance;
    */
    public static final function setImageSource (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('src'), $objFormAttributeVar);
    }

    /**
     * Will set the <input type='image' alt=''
     *
     * This method, setImageAlt, will set <input alt='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar Set the image alt="" attribute on inputs;
     * @return object The current object instance;
    */
    public static final function setImageAlternative (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('alt'), $objFormAttributeVar);
    }

    /**
     * Will set the <textarea rows=''
     *
     * This method, setTextareaRows, will set <textarea rows='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The rows="" attribute on textarea;
     * @return object The current object instance;
    */
    public static final function setRows (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('rows'), $objFormAttributeVar);
    }

    /**
     * Will set the <textarea cols=''
     *
     * This method, setTextareaColumns, will set <textarea cols='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The cols="" attribute on textareas;
     * @return object The current object instance;
    */
    public static final function setColumns (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('cols'), $objFormAttributeVar);
    }

    /**
     * Will activate the TinyMCE editor on the curren textarea;
     *
     * This method will activate the TinyMCE editor on the current textarea, or textareas that contain a specific CLASS we put
     * on them. It's the best way to have this WYSIWYG editor available only for a portion of textareas that are on a page,
     * besides having to make all textareas WYSISWYG enabled or enabled just for a coupled of HTML ids;
     *
     * @param boolean $objFormAttributeVar To enable or disable TinyMCE on the current textarea;
     * @return object The current object instance;
     */
    public static final function setTinyMCETextarea (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('tiny_mce_textarea'), $objFormAttributeVar);
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set the <input onblur=''
     *
     * This method, setOnBlur, will set <input onblur='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onblur="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnBlur (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onblur'), $objFormAttributeVar);
    }

    /**
     * Will set the <input onfocus=''
     *
     * This method, setOnFocus, will set <input onfocus='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string $objFormAttributeVar The onfocus="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnFocus (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onfocus'), $objFormAttributeVar);
    }

    /**
     * Will set the <input onchange=''
     *
     * This method, setOnChange, will set <input onchange='' attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param string @objFormAttributeVar The onchange="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setOnChange (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('onchange'), $objFormAttributeVar);
    }

    /**
     * Will set the table name needed for the auto update/insert operations ...
     *
     * This method, setTableName, will set the auto update/insert table name for SQL operations. We have a feature in our
     * form generator to allow for automatic update/insert SQL operations, on every given input.
     *
     * @param string $objFormAttributeVar The name of the table for auto-update or auto-insert operations;
     * @return object The current object instance;
    */
    public static final function setTableName (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('table_name'), $objFormAttributeVar);
    }

    /**
     * Will set the multi-table auto update/insert operations
     *
     * This method, setTableJoinOn, will set the auto update/insert table name for SQL operations. We have a feature in our
     * form generator to allow for automatic update/insert SQL operations, on every given input.
     *
     * @param string $objFormAttributeVar An SQL string for the joining of two tables;
     * @return object The current object instance;
    */
    public static final function setTableJoinOn (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('table_join_on'), $objFormAttributeVar);
    }

    /**
     * Will make a map between <inputs and tables in the current database ...
     *
     * This method, setTableMapping, will map-out <inputs to specific fields in the database. The requirement to have <inputs
     * named the same as the field in the table is still a must, but for us to know where to insert specific <inputs this
     * method is obligatory ...
     *
     * @param array $objFormAttributeVar An array mapping individual inputs to other tables;
     * @return object The current object instance;
    */
    public static final function setTableMapping (A $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('table_save_into'), $objFormAttributeVar);
    }

    /**
     * Will set the table update_id to be used upon 'SELECT and 'UPDATE
     *
     * This method, setUpdateId, will set the update_id to be used upon 'SELECT and 'UPDATE statements. We do a prior checking
     * for the update_id and if it doesn't exist, that we change the setSQLAction into 'insert', thus, we can have the nice
     * possibility that, with a single for, we can do either inserts or updates.
     *
     * @param string $objFormAttributeVar An identifier of the field to update. Usually, it's a numeric Id, but not limited;
     * @return object The current object instance;
    */
    public static final function setUpdateId (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('update_id'), $objFormAttributeVar);
    }

    /**
     * Will set a specific 'WHERE clause for the update operation, efectivelly replacing update_id and field ...
     *
     * This method, setUpdateWhere, will set a specific 'WHERE clause replacing the 'WHERE field = 'id' clause we've set
     * earlier. This helps us do massive updates with only one form, and is helpfull for some edge-cases where massive
     * administration is needed.
     *
     * @param string $objFormAttributeVar An SQL string containing the WHERE clause for update;
     * @return object The current objet instance;
    */
    public static final function setUpdateWhere (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('update_where'), $objFormAttributeVar);
    }

    /**
     * Will set the update_id_field needed for the 'WHERE clasue in the 'UPDATE ...
     *
     * This method, setUpdateField, will set the field required when doing an 'UPDATE or 'SELECT from specific tables. In
     * most cases we do this operation by unique PRIMARY KEYS, on which we need a field name and a comparsion id, which both
     * can be strings. Thus, we can have updates on KEYS that are made of strings, rather than numerical ids.
     *
     * @param string $objFormAttributeVar The update field t be updated;
     * @return object The current object instance;
    */
    public static final function setUpdateField (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('update_id_field'), $objFormAttributeVar);
    }

    /**
     * Will set the action to be taken upon autoSQL, be it 'update' or 'insert';
     *
     * This method, setSQLAction, will set the autoSQL action, either 'update' or 'insert' that needs to be taken care of.
     * Setting this attribute, along with 'table_name', will enable automatic SQL 'UPDATE and 'INSERT operations, and that's
     * what we need to be able to reach a 'CRUD' development scheme ...
     *
     * @param string $objFormAttributeVar A command to either update or insert values in the form;
     * @return object The current object instance;
    */
    public static final function setSQLAction (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('update_or_insert'), $objFormAttributeVar);
    }

    /**
     * Will set a redirect href="" (URL) where to send the visitor if the form passes validation ...
     *
     * This method, setRedirect, will set a header 'Location:' where to send the visitor if the form passes validation. Else,
     * this attribute never gets used.
     *
     * @param string $objFormAttributeVar Where to redirect if the form is OK;
     * @return object The current object instance;
    */
    public static final function setRedirect (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('redirect_if_ok'), $objFormAttributeVar);
    }

    /**
     * Will set the possible upload types, as MIMEs;
     *
     * This method, setUploadType, will set the accepted upload types on the requested form. If any of the files uploaded
     * doesn't fit in the MIME key for the specific upload input, than the form will not validate, and the data will be as soon
     * as possibled, discarded. It's a nice way to validate upload types in one single line ...
     *
     * @param string $objFormAttributeVar A string containing the accepted upload mime types;
     * @return object The current object instance;
    */
    public static final function setUploadType (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('accepted_mime_types'), $objFormAttributeVar);
    }

    /**
     * Will set the upload error message
     *
     * This method, setUploadErrMsg, will set the upload error message. Sometimes, the default error message doesn't quite
     * fit well in the majority of cases, case upon which a developer needs to act, and change the coressponding message for
     * the kind of error we have. Nontheless, we can extend functionality in this area by adding methods that add specific
     * errors for some specific error types like no file upload, or partial file upload, or i/o error ...
     *
     * @param string $objFormAttributeVar The upload error message to display in case of error;
     * @return object The current object instance;
    */
    public static final function setUploadErrMsg (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('upload_error_message'), $objFormAttributeVar);
    }

    /**
     * Will set the upload directory ...
     *
     * This method, setUploadDirectory, will set the directory where the files will be uplaoded. If the directory doesn't exist,
     * or the path does not exist, the generator will take care of that and make the path before trying to move files to a
     * non-existing path. If it fails, it will return an error, which will indicate to the user that the website could not
     * create the path (most unlikely, since we already tested the "is_writeable" on the upload folder ...)
     *
     * @param string $objFormAttributeVar The upload directory to upload files to;
     * @return object The current object instance;
    */
    public static final function setUploadDirectory (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('upload_dir'), $objFormAttributeVar);
    }

    /**
     * Will set an array to resize images to ...
     *
     * This method will accept an array of width => height key/var pairs, that will set the requested size of image resizing
     * widths/heights, so that uploaded images can be quickly processed. An automatic system takes care of properly naming files
     * and doing updates/inserts in the database;
     *
     * @param array $objFormAttributeVar The array of widths/heights to be passed;
     * @return object The current object instance;
    */
    public static final function setUploadImageResize (A $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('upload_resize_img'), $objFormAttributeVar);
    }

    /**
     * Will set an extra="" parameter on HTML elements;
     *
     * This method will set an extra HTML attribute for the generated element. For example, the extra="" parameter can be used
     * in Javascript do to a lot of fancy things, while binding JS with PHP, through non-standard attributes. The bad part is
     * that it's not W3C valid, but again ... some people need it;
     *
     * @param string $objFormAttributeVar The extra="" attribute on HTML elements;
     * @return object The current object instance;
    */
    public static final function setExtra (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('extra'), $objFormAttributeVar);
    }

    /**
     * Will surround the form in a fieldset;
     *
     * This method will issue a fieldset for the current form. A fieldset is used to cleverly organize several forms on a page,
     * in an ordingly manner. It's a great way of making clear that form, does what, if for example subsequent forms are used.
     *
     * @param string $objFormAttributeVar The text of the fieldset (to be shown);
     * @return object The current object instance;
    */
    public static final function setFieldset (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('fieldset'), $objFormAttributeVar);
    }

    /**
     * Will set the label for="" on inputs;
     *
     * This method will set the <label for="" on inputs, thus giving the full power of making forms easy with out generator. The
     * reality is that without labels, it's kinda hard to guess what should be entered in forms, so proper use of this method
     * is mandatory in making an accesible form;
     *
     * @param string $objFormAttributeVar The text to be shown on the <label;
     * @return object The current object instance;
    */
    public static final function setLabel (S $objFormAttributeVar) {
        // Do return ...
        return self::setAttribute (new S ('label'), $objFormAttributeVar);
    }

    /**
     * Will enable or disable the container div arround HTML elements;
     *
     * This method will either enable or disable the container div arroudn HTML inputs. This container div gives the developer
     * another row of CSS control over the inputs, really giving him the power to separate between the presentation and the
     * functionality generated by our form generator;
     *
     * @param boolean $objFormAttributeVar To enable (true) or disable (false) the container div;
     * @return object The current object instance;
    */
    public static final function setContainerDiv (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('container_div'), new S ('yes'));
        } else {
            // Do return ...
            return self::setAttribute (new S ('container_div'), new S ('no'));
        }
    }

    /**
     * Will enable or disable ajaxified validation on the form;
     *
     * This method will either enable or disable ajax validation on the given form. This is usefull for some fancy end-user
     * actions and to also keep network trafic as low as possible, by doing validation prior to refreshing the page;
     *
     * @param boolean $objFormAttributeVar To enable (true) or disable (false) the container div;
     * @return object The current object instance;
    */
    public static final function setAJAXEnabledForm (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('ajax_form'), $objFormAttributeVar);
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will make a multiple select input;
     *
     * This method will transform a select to a multiple one, thus giving the user a chance to due selections on a large scale.
     * For now, the mechanism for multiple selects isn't yet implemented, but will be soon available, when the need constrains
     * us to do the necessary work on it;
     *
     * @param boolean $objFormAttributeVar Make it a multiple select or now;
     * @return objec The current object instance;
    */
    public static final function setMultiple (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('multiple'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will select the current option in an select input type;
     *
     * This method will enable the selected attribute on option inputs in a select input type. Thus, for example when we retrieve
     * content from the database, the option that has the value that's in the database will be auto-selected. It's a nice feature
     * we like and love to use;
     *
     * @param boolean $objFormAttributeVar Enable (true) or not (false) the select attribute on an option;
     * @return object The current object instance;
    */
    public static final function setSelected (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('selected'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set the <input disabled
     *
     * This method, setDisabled, will set <input disabled attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param boolean $objFormAttributeVar If true, will disable the input, else, won't do a thing;
     * @return object The current object instance;
    */
    public static final function setDisabled (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('disabled'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set the <input checked
     *
     * This method, setChecked, will set <input checked attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param boolean $objFormAttributeVar Will set the checked or no checked attribute on options;
     * @return object The current object instance;
    */
    public static final function setChecked (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('checked'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set the <input readonly
     *
     * This method, setReadOnly, will set <input readonly attribute, to the string you pass it as a parameter. It makes a
     * request to setAttribute, which is the final method that will take care of passing parameters in a proper order. Finally,
     * the setAttribute will call the generateHTML, to generate the <form and <input (<divs, and other ...) tags for
     * the soul purpose of generating the form ...
     *
     * @param boolean $objFormAttributeVar Will set the readonly="" attribute on inputs;
     * @return object The current object instance;
    */
    public static final function setReadOnly (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('readonly'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set the file controller for auto-updated/auto-inserted fiel inputs;
     *
     * This method will enable the file controller for fields that are implied in an auto-update/auto-insert form. This file
     * controller has the meaning to disable the file input if something is already in the database. The user can enable the file
     * controller by checking option near the controller, but if he doesn't than the content already in the database isn't changed;
     * This allows us to have file inputs that don't require extra-processing, and the user is spared of having to upload the
     * file each time;
     *
     * @param boolean $objFormAttributeVar Enable or disable the file controller;
     * @return object The current object instance;
     */
    public static final function setFileController (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('file_controller'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will set the mptt_remove_unique clear flag ...
     *
     * This method will set the MPTT::mpttRemoveUnique flag for a special bunch of category creating inputs, where we need to add
     * a special string at the end of the category name to allow for an infinite hierarchy in one table. It's a drawback that wasn't
     * resolved by the MPTT algorithm when it comes to unique names, so we overcame this by a a suffix ...
     *
     * @param boolean $objFormAttributeVar Enable or disable the mptt_remove_unique
     * @return object The current object instance;
     */
    public static final function setMPTTRemoveUnique (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do return ...
            return self::setAttribute (new S ('mptt_remove_unique'), new S ('yes'));
        } else {
            // Do return ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will check if the current form has errors or not;
     *
     * This method will check that the _POST information has been submitted, and that the form doesn't contain any errors,
     * on any of the fields in the form. Will return the proper boolean value for each of the case;
     *
     * @return boolean Will return true if the form has errors;
     */
    public function checkFormHasErrors () {
        return (self::checkPOST ()->toBoolean () == TRUE && self::$objFormErrorField
        ->doCount ()->toInt () == 0) ? (new B (FALSE)) : (new B (TRUE));
    }

    /**
     * Will set the form ending ...
     *
     * This method, setFormEndAndExecute, will set the form ending, and execute catched information about each input, in the
     * objDataContainer. It will assign the form attributes to the first input of the form, beucase the form tag needs to be
     * delcared prior to declaring any input, if we want that input to be part of the form. Also, it prepends and appends the
     * fieldset tags for the form ...
     *
     * @param boolean $objFormAttributeVar A boolean value, that if true, will render the form above;
     * @return object The current object instance;
    */
    public static final function setFormEndAndExecute (B $objFormAttributeVar) {
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Do content cleaning rutine;
            if (self::checkPOST ()->toBoolean () == TRUE) {
                foreach ($_SESSION['POST'] as $k => $v) {
                    # $_SESSION['POST'][$k] = mysql_real_escape_string ($v);
                }
            }
            // Update and START;
            self::updateObjectPropertiesonSQLUpdateorInsert ();
            self::setFormHeaderOnInputStart ();
            // Do the foreach ...
            $tp = new FilePath (FORM_TP_DIR . _S . 'frm_input_gen.tp');
            foreach (self::$objDataContainer as $k => $v) {
                // Tie SQL operations to the first input that has a name ...
                if (!isset (self::$objFormDataContainer['SQL_operations_tied_to_input'])) {
                    if (isset (self::$objFormDataContainer['update_or_insert'])) {
                        if (isset (self::$objDataContainer[$k]['name'])) {
                            self::$objFormDataContainer['SQL_operations_tied_to_input'] = self::$objDataContainer[$k]['name'];
                        }
                    }
                }

                // Set a hook before form _POST or _QUERY data, got from the database ...
                if (isset (self::$objRegisteredFunctionArray['hook:beforeFormPOSTOrQuery'])) {
                    foreach (self::$objRegisteredFunctionArray['hook:beforeFormPOSTOrQuery'] as $objFunction) {
                        call_user_func ($objFunction->toString (), self::$objDataContainer[$k]);
                    }
                }

                // Generate the FILE controller;
                self::makeInputFileController (new I ($k));

                // Check if the _SESSION['POST'] was set;
                if (self::checkPOST ()->toBoolean () == TRUE) {
                    self::optionSELECTOnPOST (new I ($k));
                    self::checkboxRadioSQLOperations (new I ($k));
                    self::sessionToInputValueSetter (new I ($k));
                    // Set a hook for a innerFormPOST;
                    if (isset (self::$objRegisteredFunctionArray['hook:innerFormPOST'])) {
                        foreach (self::$objRegisteredFunctionArray['hook:innerFormPOST'] as $objFunction) {
                            call_user_func ($objFunction->toString (), self::$objDataContainer[$k], $_SESSION['POST']);
                        }
                    }
                } else if (isset (self::$objUpdateSELECTData[0])) {
                    if (isset (self::$objDataContainer[$k]['name'])) {
                        if (in_array (self::$objDataContainer[$k]['name'], self::$objUpdateTableFields->toArray ())) {
                            self::queryToInputValueSetter (new I ($k));
                        }
                    }

                    // REMBER ME: This used of be an 'else if', so if bugs appear, be sure to check this out!
                    if (self::$objDataContainer[$k]['type'] == new S ('option')) {
                        if (isset (self::$objDataContainer[$k]['value'])) {
                            // REMEMBER ME: BIND options to SELECTs;
                            if (self::$objDataContainer[$k]['value'] ==
                            self::$objUpdateSELECTData[0][self::$objDataContainer[$k]['bound_to']]) {
                                self::$objDataContainer[$k]['selected'] = new S ('yes');
                            }
                        }
                    }

                    // Set a hook innerFormQUERY;
                    if (isset (self::$objRegisteredFunctionArray['hook:innerFormQUERY'])) {
                        foreach (self::$objRegisteredFunctionArray['hook:innerFormQUERY'] as $objFunction) {
                            call_user_func ($objFunction->toString (), self::$objDataContainer[$k], self::$objUpdateSELECTData[0]);
                        }
                    }
                }

                // Clear the MPTT unique flag ...
                if (isset (self::$objDataContainer[$k]['mptt_remove_unique'])) {
                    // Remove it ...
                    self::$objDataContainer[$k]['value'] = MPTT::mpttRemoveUnique (self::$objDataContainer[$k]['value']);
                }

                // Set a hook after a FormPOSTOrQuery;
                if (isset (self::$objRegisteredFunctionArray['hook:afterFormPOSTOrQuery'])) {
                    foreach (self::$objRegisteredFunctionArray['hook:afterFormPOSTOrQuery'] as $objFunction) {
                        call_user_func ($objFunction->toString (), self::$objDataContainer[$k]);
                    }
                }
                // Set the number index;
                self::$objDataContainer[$k]['index_of_input'] = $k;
                // Generate an HTML element;
                self::generateHTML (self::$objDataContainer[$k]['type'], self::$objDataContainer[$k], $tp);
            }

            // End the form;
            self::setFormFooterOnInputEnd ();
            self::$objDataContainer = new A;
            self::$objDataCountInput = new I (0);
            self::$objDataToForm = new I (0);
            self::$objFormDataContainer = new A;
            self::$objOtherSQLData = new A;
            self::$objUpdateSELECTData = new A;
            self::$objRegisteredFunctionArray = new A;
            // Return to chain ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        } else {
            // Return to chain ...
            return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
        }
    }

    /**
     * Will register a form hook with the current form;
     *
     * This method will set a hook with the current form, that will be executed in one of the predefined forms in our generator. It
     * allows a developer to dinamically add code to the form generator, without changing the base class thus keeping the code
     * updateable with newer versions of the platform;
     *
     * @param string $actionFunctionOfUser The name/string of the callable method/function;
     * @param string $whereToRegister The zone where to execute the hook;
     * @return object The current object instance;
     */
    public static final function registerFormHook (S $actionFunctionOfUser, S $whereToRegister) {
        self::$objRegisteredFunctionArray[$whereToRegister][] = $actionFunctionOfUser;
        // Do return ...
        return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
    }

    /**
     * Will set a key/var combination of HTML tags with attributes (key) and values (vars) ...
     *
     * This method, setAttribute, will set a key/var combination of attributes/values, that are needed for form generation. It
     * will take a default action of attributes that do not have special requirements, while parsing some of those that
     * indeed need special attention. For example, for enctype, it will accept one of three possible parameters, else it will
     * output an error screen ... Other attributes may suffer the same behaviour ...
     *
     * @param string $objFormAttributeKey The key to be set;
     * @param mixed $objFormAttributeVar The var to be set for the current key;
     * @return object The current object instance;
    */
    private static final function setAttribute (S $objFormAttributeKey, M $objFormAttributeVar) {
        switch ($objFormAttributeKey) {
            case 'method':
            case 'action':
            case 'fieldset':
            case 'table_name':
            case 'table_join_on':
            case 'table_save_into':
            case 'update_where':
            case 'update_or_insert':
            case 'redirect_if_ok':
            case 'accepted_mime_types':
            case 'upload_error_message':
            case 'upload_dir':
            case 'upload_resize_img':
                self::$objFormDataContainer[$objFormAttributeKey] = $objFormAttributeVar;
            break;
            case 'update_id':
                if ($objFormAttributeVar == '#nextTableAutoIncrement') {
                    // Set some requirements;
                    $objSQLCondition = new S ('SHOW TABLE STATUS LIKE "%t"');
                    self::$objUpdateUpdateId = self::getQuery ($objSQLCondition
                    ->doToken ('%t', self::$objFormDataContainer['table_name']))
                    ->offsetGet (0)->offsetGet ('Auto_increment');
                } else {
                    self::$objUpdateUpdateId = $objFormAttributeVar;
                }
            break;
            case 'update_id_field':
                self::$objUpdateUpdateField = $objFormAttributeVar;
            break;
            case 'type':
                self::$objDataToForm->setInt (1);
                self::$objDataCountInput->setInt (count (self::$objDataContainer));
                if (isset (self::$objDataContainer[self::$objDataCountInput->toInt () - 1])) {
                    if (isset (self::$objDataContainer[self::$objDataCountInput->toInt () - 1]['type'])) {
                        if (self::$objDataContainer[self::$objDataCountInput->toInt () - 1]['type'] == 'option'    ||
                            self::$objDataContainer[self::$objDataCountInput->toInt () - 1]['type'] == 'optgroup') {
                            if ($objFormAttributeVar == 'optgroup') {
                                self::$objDataContainer[self::$objDataCountInput] =
                                new A (array ('type' => new S ('optgroup_ending'), 'name' => new S ('optgroup_ending')));
                                self::$objDataCountInput->setInt (count (self::$objDataContainer));
                                self::$objOptGroupOpen->setInt (1);
                            }
                            if ($objFormAttributeVar != 'option') {
                                if ($objFormAttributeVar != 'optgroup') {
                                    if ($objFormAttributeVar != 'optgroup_ending') {
                                        if (self::$objOptGroupOpen->toInt () == 1) {
                                            self::$objDataContainer[self::$objDataCountInput] =
                                            new A (array ('type' => new S ('optgroup_ending'),
                                            'name' => new S ('optgroup_ending')));
                                            self::$objDataCountInput->setInt (count (self::$objDataContainer));
                                            self::$objOptGroupOpen->setInt (1);
                                        }
                                        self::$objDataContainer[self::$objDataCountInput] =
                                        new A (array ('type' => new S ('select_ending'), 'name' => new S ('select_ending')));
                                        self::$objDataCountInput->setInt (count (self::$objDataContainer));
                                    }
                                }
                            }
                        }
                    }
                }

                // REMEMBER ME: If SELECT is empty; Added so that SELECTS with nothing in them, shoud not produce HTML errors;
                // If we experience problems, this is the place to CHECK;
                if (isset (self::$objDataContainer[self::$objDataCountInput->toInt () - 1])) {
                    if (isset (self::$objDataContainer[self::$objDataCountInput->toInt () - 1]['type'])) {
                        if (self::$objDataContainer[self::$objDataCountInput->toInt () - 1]['type'] == 'select') {
                            if ($objFormAttributeVar != 'option') {
                                if ($objFormAttributeVar != 'optgroup') {
                                    if ($objFormAttributeVar != 'optgroup_ending') {
                                        self::$objDataContainer[self::$objDataCountInput] =
                                        new A (array ('type' => new S ('select_ending'), 'name' => new S ('select_ending')));
                                        self::$objDataCountInput->setInt (count (self::$objDataContainer));
                                    }
                                }
                            }
                        }
                    }
                }

                // Continue;
                isset (self::$objDataContainer[self::$objDataCountInput])   ?
                self::$objDataContainer[self::$objDataCountInput] = _NONE   :
                self::$objDataContainer[self::$objDataCountInput] = new A;
                self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
            break;
            case 'multiple':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('select')) {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_SET_MULTIPLE_ON_NON_SELECT),
                        new S (CANNOT_SET_MULTIPLE_ON_NON_SELECT_FIX));
                    }
                } else {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (CANNOT_SET_MULTIPLE_ON_NON_SELECT),
                    new S (CANNOT_SET_MULTIPLE_ON_NON_SELECT_FIX));
                }
            break;
            case 'accept':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('file')) {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_SET_ACCEPT_ON_NON_FILE),
                        new S (CANNOT_SET_ACCEPT_ON_NON_FILE_FIX));
                    }
                } else {
                    # The <form has an accept parameter you know ...
                    self::$objFormDataContainer[$objFormAttributeKey] = $objFormAttributeVar;
                }
            break;
            case 'alt':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('image')) {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_SET_ALT_ON_NON_IMAGE),
                        new S (CANNOT_SET_ALT_ON_NON_IMAGE_FIX));
                    }
                } else {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (CANNOT_SET_ALT_ON_NON_IMAGE),
                    new S (CANNOT_SET_ALT_ON_NON_IMAGE_FIX));
                }
            break;
            case 'src':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('image')) {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_SET_SRC_ON_NON_IMAGE),
                        new S (CANNOT_SET_SRC_ON_NON_IMAGE_FIX));
                    }
                } else {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (CANNOT_SET_SRC_ON_NON_IMAGE),
                    new S (CANNOT_SET_SRC_ON_NON_IMAGE_FIX));
                }
            break;
            case 'checked':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('radio') ||
                        self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('checkbox')) {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_SET_CHK_ON_NON_CHKRADIO),
                        new S (CANNOT_SET_CHK_ON_NON_CHKRADIO_FIX));
                    }
                } else {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (CANNOT_SET_CHK_ON_NON_CHKRADIO),
                    new S (CANNOT_SET_CHK_ON_NON_CHKRADIO_FIX));
                }
            break;
            case 'name':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == 'select') {
                        self::$objOpenedSelectName = $objFormAttributeVar;
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else if (self::$objDataContainer[self::$objDataCountInput]['type'] == 'select_ending') {
                        self::$objOpenedSelectName = NULL;
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('option')) {
                        self::$objDataContainer[self::$objDataCountInput]['bound_to'] = self::$objOpenedSelectName;
                    } else {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    }
                } else {
                    self::$objFormDataContainer[$objFormAttributeKey] = $objFormAttributeVar;
                }
            break;
            case 'file_controller':
                if (self::$objDataToForm->toInt () == 1) {
                    if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('file')) {
                        self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;
                    } else {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (CANNOT_USE_FILE_CONTROLLER),
                        new S (CANNOT_USE_FILE_CONTROLLER_FIX));
                    }
                }
            break;
            case 'label':
                // Set the LABEL;
                self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar;

                // For OPTIONs, also set the VALUE;
                if (self::$objDataContainer[self::$objDataCountInput]['type'] == new S ('option')) {
                    // If NOT already set;
                    if (!isset (self::$objDataContainer[self::$objDataCountInput]['value'])) {
                        self::$objDataContainer[self::$objDataCountInput]['value'] = $objFormAttributeVar;
                    }
                }
            break;
            case 'tiny_mce_textarea':
                // Set the required TinyMCE JS Scripts;
                self::manageJSS (new FilePath (FORM_TP_DIR . _S . JAVASCRIPT_DIR .
                _S . 'tiny_mce/tiny_mce.js'), new S ('tmce_src'));
                self::manageJSS (new FilePath (FORM_TP_DIR . _S . JAVASCRIPT_DIR .
                _S . 'tiny_mce/tiny_mce_exec.js'), new S ('tmce_exe'));
                // Set the proper CLASS;
                if (isset (self::$objDataContainer[self::$objDataCountInput][new S ('class')])) {
                    self::$objDataContainer[self::$objDataCountInput][new S ('class')] =
                    self::$objDataContainer[self::$objDataCountInput][new S ('class')] . _SP . 'RA_mceRichText';
                } else {
                    self::$objDataContainer[self::$objDataCountInput][new S ('class')] = 'RA_mceRichText';
                }
            break;
            case 'ajax_form':
                if (isset (self::$objFormDataContainer[new S ('class')])) {
                    self::$objFormDataContainer[new S ('class')] =
                    self::$objFormDataContainer[new S ('class')] . _SP . 'RA_ajax_form';
                } else {
                    self::$objFormDataContainer[new S ('class')] = 'RA_ajax_form';
                }
            break;
            default:
                self::$objDataToForm->toInt () == 1                                                         ?
                self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar  :
                self::$objFormDataContainer[$objFormAttributeKey] = $objFormAttributeVar;
            break;
        }
        # Return to chain ...
        return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @return object The current object instance;
    */
    private static function setSQLOperationsOnForm () {
        if (isset (self::$objFormDataContainer['table_name'])) {
            if (isset (self::$objFormDataContainer['table_name']) && isset (self::$objUpdateWhere)) {
                $q = new S ('SELECT * FROM %table WHERE %condition LIMIT 1');
                $q->doToken ('%table', self::$objFormDataContainer['table_name']);
                $q->doToken ('%condition',self::$objUpdateWhere);
                if (self::getQuery ($q)->doCount ()->toInt () == 0) {
                    self::$objFormDataContainer['update_or_insert'] = new S ('insert');
                }
            }
            // Do update;
            if ((isset (self::$objFormDataContainer['update_or_insert']))    &&
                (self::$objFormDataContainer['update_or_insert'] == new S ('update'))) {
                // Check to see if update_id is set. We can't update on something that doesn't exist!
                if (isset (self::$objUpdateWhere)) {
                    // For each _SESSION['POST'] key, do an update ...
                    foreach ($_SESSION['POST'] as $k => $v) {
                        if (in_array ($k, self::$objUpdateTableFields->toArray ())) {
                            $q = new S ('UPDATE %table SET %who = "%what" WHERE %condition');
                            $q->doToken ('%table', self::$objUpdateTableName)
                            ->doToken ('%who', $k)->doToken ('%what', $v)
                            ->doToken ('%condition', self::$objUpdateWhere);
                            self::getQuery ($q);
                        }
                    }
                    // Do additional SQLs ...
                    foreach (self::$objOtherSQLData as $k => $v) {
                        $q = new S ('UPDATE %table SET %who = "%what" WHERE %condition');
                        $q->doToken ('%table', self::$objUpdateTableName)
                        ->doToken ('%who', $k)->doToken ('%what', $v)
                        ->doToken ('%condition', self::$objUpdateWhere);
                        self::getQuery ($q);
                    }
                }
            } else if ((isset (self::$objFormDataContainer['update_or_insert']))   &&
                (self::$objFormDataContainer['update_or_insert'] == new S ('insert'))) {
                if (isset (self::$objFormDataContainer['table_save_into'])) {
                    $insertTableArray   = self::$objFormDataContainer['table_save_into'];
                    $insertTable        = self::$objFormDataContainer['table_save_into']->toValues ();
                    $insertTableCount   = count ($insertTable);
                    for ($i = 0; $i < $insertTableCount; ++$i) {
                        foreach ($_SESSION['POST'] as $k => $v) {
                            if ((isset ($insertTableArray[$k])) &&
                                (isset ($insertTable[$i]))      &&
                                ($insertTableArray[$k] == $insertTable[$i])) {
                                $detectedMULTIPLEInputs[$insertTable[$i]][] = $k;
                            }
                        }
                    }
                    foreach ($detectedMULTIPLEInputs as $k => $v) {
                        $a = new A;
                        $q = new S ('INSERT INTO %table SET' . _SP);
                        $q->doToken ('%table', $k);
                        $detectedMULTIPLEInputs[$k] = array_unique ($v);
                        $tableFields = self::getFieldsFromTable (new S ($k));
                        foreach ($_SESSION['POST'] as $kv => $vv) {
                            $r = new S ('%who = "%what"');
                            if (in_array ($kv, $tableFields->toArray ())) {
                                $a[] = $r->doToken ('%who', $kv)->doToken ('%what', $vv);
                            }
                        }
                        foreach (self::$objOtherSQLData as $k => $v) {
                            $r = new S ('%who = "%what"');
                            if (in_array ($k, $tableFields->toArray ())) {
                                $a[] = $r->doToken ('%who', $k)->doToken ('%what', $v);
                            }
                        }
                        $q->appendString (implode (', ', $a->toArray ()));
                        self::getQuery ($q);
                    }

                    // Do for the native table ...
                    if (isset (self::$objFormDataContainer['table_name'])) {
                        $a = new A;
                        $q = new S ('INSERT INTO %table SET' . _SP);
                        $q->doToken ('%table', self::$objFormDataContainer['table_name']);
                        $tableFields = self::getFieldsFromTable (self::$objFormDataContainer['table_name']);
                        foreach ($_SESSION['POST'] as $k => $v) {
                            if (in_array ($k, $tableFields->toArray ())) {
                                $r = new S ('%who = "%what"');
                                $a[] = $r->doToken ('%who', $k)->doToken ('%what', $v);
                            }
                        }
                        foreach (self::$objOtherSQLData as $k => $v) {
                            $r = new S ('%who = "%what"');
                            if (in_array ($k, $tableFields->toArray ())) {
                                $a[] = $r->doToken ('%who', $k)->doToken ('%what', $v);
                            }
                        }
                        $q->appendString (implode (', ', $a->toArray ()));
                        self::getQuery ($q);
                    }
                } else {
                    $a = new A;
                    $q = new S ('INSERT INTO %table SET' . _SP);
                    $q->doToken ('%table', self::$objFormDataContainer['table_name']);
                    $tableFields = self::getFieldsFromTable (self::$objFormDataContainer['table_name']);
                    foreach ($_SESSION['POST'] as $k => $v) {
                        $r = new S ('%who = "%what"');
                        if (in_array ($k, $tableFields->toArray ())) {
                            $a[] = $r->doToken ('%who', $k)->doToken ('%what', $v);
                        }
                    }
                    foreach (self::$objOtherSQLData as $k => $v) {
                        $r = new S ('%who = "%what"');
                        if (in_array ($k, $tableFields->toArray ())) {
                            $a[] = $r->doToken ('%who', $k)->doToken ('%what', $v);
                        }
                    }
                    // Just do it;
                    $q->appendString (implode (', ', $a->toArray ()));
                    self::getQuery ($q);
                }
            }
        }
    }

    /**
     * Will generate the HTML element;
     *
     * This method, is the main engine of our form generator. It takes two parameters, the input type, input array and .tp file,
     * and it executes the .tp file, thus generating the proper HTML needed for our forms to work properly;
     *
     * @param string $inputType The type of input to generate;
     * @param array $inputAttributeArray The array containing the attributes;
     * @param strign $tp The .tp file to be executed;
     * @return void Doesn't return a thing, it doesn't have to;
    */
    private static final function generateHTML (S $inputType, A $inputAttributeArray, FilePath $tp) {
        if (self::$objPostPassedToSession->toInt () == 1) {
            if (!empty ($inputAttributeArray['name'])) {
                if (!empty ($_SESSION['POST'][$inputAttributeArray['name']])) {
                    if (isset ($inputAttributeArray['reg_type']) && isset ($inputAttributeArray['reg_check'])) {
                        switch ($inputAttributeArray['reg_type']) {
                            case 'ereg':
                                if (!ereg ($inputAttributeArray['reg_check'],
                                    self::getPOST ($inputAttributeArray['name']))) {
                                    self::$objFormErrorField[$inputAttributeArray['name']] =
                                    $inputAttributeArray['reg_err_msg'];
                                    self::$objFormPassedExecution->setInt (0);
                                }
                            break;
                            case 'preg':
                                if (!preg_match ($inputAttributeArray['reg_check'],
                                    self::getPOST ($inputAttributeArray['name']))) {
                                    self::$objFormErrorField[$inputAttributeArray['name']] =
                                    $inputAttributeArray['reg_err_msg'];
                                    self::$objFormPassedExecution->setInt (0);
                                }
                            break;
                            default:
                                if (!ereg ($inputAttributeArray['reg_check'],
                                    self::getPOST ($inputAttributeArray['name'])) ||
                                    !preg_match ($inputAttributeArray['reg_check'],
                                    self::getPOST ($inputAttributeArray['name']))) {
                                    self::$objFormErrorField[$inputAttributeArray['name']] =
                                    $inputAttributeArray['reg_err_msg'];
                                    self::$objFormPassedExecution->setInt (0);
                                }
                            break;
                        }
                    } else {
                        self::$objFormPassedExecution->setInt (1);
                    }
                } else {
                    self::$objFormPassedExecution->setInt (0);
                }
            }
        } else {
            self::$objFormPassedExecution->setInt (0);
        }
        self::setCoreAttributes ($inputAttributeArray, $tp);
        self::setCoreInputAttributes ($inputAttributeArray, $tp);
        foreach ($inputAttributeArray as $k => $v) {
            switch ($inputType) {
                case 'radio':
                case 'checkbox':
                    switch ($k) {
                        case 'checked':
                            switch (isset ($_SESSION['POST'][$inputAttributeArray['name']])) {
                                case TRUE:
                                    self::tpSet (new S ($_SESSION['POST'][$inputAttributeArray['name']]), new S ($k), $tp);
                                break;
                                case FALSE:
                                    self::tpSet ($v, new S ($k), $tp);
                                break;
                            }
                        break;
                        default:
                            self::tpSet ($v, new S ($k), $tp);
                        break;
                    }
                break;
                case 'textarea':
                case 'select':
                case 'option':
                case 'optgroup':
                case 'file':
                case 'hidden':
                case 'reset':
                case 'submit':
                case 'button':
                case 'image':
                case 'text':
                case 'password':
                case 'optgroup_ending':
                case 'select_ending':
                    self::tpSet (new O ($v), new S ($k), $tp);
                break;
                default:
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (INVALID_INPUT_TYPE_OR_NOT_RECOGNIZED),
                    new S (INVALID_INPUT_TYPE_OR_NOT_RECOGNIZED_FIX));
                break;
            }
        }
        self::tpSet ($inputType, new S ('type'), $tp);
        self::tpExe ($tp);
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param string $formSubmitName The name of the input to check if it was submitted;
     * @return boolean Return boolean if it was submitted;
    */
    public final function wasSubmitted (S $formSubmitName) {
        if ((self::checkPOST ()->toBoolean () == TRUE)) {
            if (sizeof ($_SESSION['POST']) > 0) {
                // Do return ...
                return new B (isset ($_SESSION['POST'][$formSubmitName]));
            } else {
                // Do return ...
                return new B (FALSE);
            }
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set extra keys with content in the post;
     *
     * This method will set an extra key to _POST input, which should allow us to manually add an extra field to be auto-updated
     * upon execution of the form. Easy, no?
     *
     * @param string $nameOfKey The name of the key (thus field) to update;
     * @param string $keyContent The content to update the field with;
    */
    public static final function setPOST (S $nameOfKey, S $keyContent) {
        // Set the session value ...
        $_SESSION['POST'][$nameOfKey] = $keyContent;
    }

    /**
     * Will unset the given post key ...
     *
     * This method will unset the given post key. If no key is given or if it defaults to NULL, then the whole _SESSIOn['POST']
     * is emptyied. Usefull in cases like passwords, encryption etc.
     *
     * @param S $nameOfKey The name of the key to unset ...
     */
    public static final function unsetPOST (S $nameOfKey = NULL) {
    	if ($nameOfKey != NULL) {
	    	if (isset ($_SESSION['POST'][$nameOfKey])) {
	    		unset ($_SESSION['POST'][$nameOfKey]);
	    	}
    	} else {
    		// Unset ALL _SESSION['POST'];
    		unset ($_SESSION['POST']);
    	}
    }

    /**
     * Will get a key from _POST;
     *
     * This method will retrieve a key from _POST, only if the key was set. Else, it will return void, which in the context of our
     * framework should issue an error, that will make the developer aware that his programming practices aren't all that good.
     *
     * @param string $formInputToGet The name of the input to get its _POST value from;
     * @return string Will return a string, only if the requested key was set;
    */
    public static final function getPOST (S $formInputToGet = NULL) {
        switch ($formInputToGet == NULL) {
            case TRUE:
                // Do return ...
                return $_SESSION['POST'];
                break;

            case FALSE:
                // Do return ...
                return $_SESSION['POST'][$formInputToGet];
                break;
        }
    }

    /**
     * Will check to see if the requested _POST key was set;
     *
     * This method will check that the requested _POST key was set. If no argument is passed, it will check that the _POST
     * was set. That's easy to determine that any form was submitted. For individual forms, you need to determine what button
     * was pushed, by checking its key in the _POST;
     *
     * @param string $varToCheck The key to check in _POST;
     * @return boolean Will return true if the key was set;
    */
    public static final function checkPOST (S $varToCheck = NULL) {
        switch ($varToCheck == NULL) {
            case TRUE:
                // Just return the darn thing;
                return new B ((isset ($_SESSION['POST'])) &&
                (sizeof ($_SESSION['POST']) != 0));
            break;
            case FALSE:
                // To a check on that varToCheck;
                return new B (isset ($_SESSION['POST'][$varToCheck]));
            break;
        }
    }

    /**
     * Will render the form header;
     *
     * This method will render the form start, meaning all the necesarry <form tags & attributes. Without a proper nesting of
     * tags, information cannot be passed properly by browsers to the PHP enabled server.
     *
     * @return void Doesn't return anything, cause it doesn't have to ...
    */
    private static function setFormHeaderOnInputStart () {
        self::convertPOSTToSESSIONString ();
        if (self::$objFormStarted->toInt () == 0) {
            if (self::checkPOST ()->toBoolean () == TRUE) {
                if (sizeof ($_SESSION['POST']) != 0 && sizeof ($_FILES) != 0) {
                    self::checkFILEOperationsOnForm ();
                }
            }
            // Make an array, for data collection;
            $coreSetAttributes = new A;
            // Remember the fact that we started a <form ...
            self::$objFormStarted->setInt (1);
            // Set the <form .tp file;
            $tp = new FilePath (FORM_TP_DIR . _S . 'frm_input_form_started.tp');
            // Set core attributes;
            self::setCoreAttributes (self::$objFormDataContainer, $tp);
            // Set core <input event attributes;
            self::setCoreInputAttributes (self::$objFormDataContainer, $tp);
            foreach (self::$objFormDataContainer as $k => $v) {
                switch ($k) {
                    case 'action':
                        !empty ($v)                 ?
                        $coreSetAttributes[$k] = $v :
                        $coreSetAttributes[$k] = URL::rewriteURL ();
                    break;
                    case 'fieldset':
                        // Set the form in a <fieldset;
                        self::$objFormInFieldSet->setInt (1);
                        $coreSetAttributes[$k] = $v;
                    break;
                    default:
                        $coreSetAttributes[$k] = $v;
                    break;

                }
            }
            self::tpSet ($coreSetAttributes, new S ('coreSetAttributes'), $tp, new S ('explode'));
            self::tpExe ($tp);
        }
    }

    /**
     * Will check that proper uploading has been done;
     *
     * This method will check that the upload was properly done. If not, it will automatically set an error on the proper
     * erorred input. Good way of integrating PHP upload error checking, and our form generation capabilities.
     *
     * @return void It doesn't need to return anything;
    */
    private static function checkFILEOperationsOnForm () {
        foreach ($_FILES as $k => $v) {
            switch ($v['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    self::setErrorOnInput (new S ($k),
                    new S (UPLOADED_FILE_EXCEEDS_INI_SIZE));
                break;
                case UPLOAD_ERR_FORM_SIZE:
                    self::setErrorOnInput (new S ($k),
                    new S (UPLOADED_FILE_EXCEEDS_MAXFILESIZE));
                break;
                case UPLOAD_ERR_PARTIAL:
                    self::setErrorOnInput (new S ($k),
                    new S (UPLOAD_WAS_PARTIAL));
                break;
                case UPLOAD_ERR_NO_FILE:
                    self::setErrorOnInput (new S ($k),
                    new S (UPLOAD_EMPTY_FILE_SPECIFIED));
                break;
                case UPLOAD_ERR_CANT_WRITE:
                    self::setErrorOnInput (new S ($k),
                    new S (UPLOAD_CANNOT_WRITE_DISK));
                break;
                case UPLOAD_ERR_EXTENSION:
                    self::setErrorOnInput (new S ($k),
                    new S (UNKNOWN_FILE_TYPE_WAS_UPLOADED));
                break;
            }
        }
        // Check accepted MIME types;
        if (isset (self::$objFormDataContainer['accepted_mime_types'])) {
            $aNotSoRandomString = _PIPE . $_SERVER['REQUEST_TIME'];
            if (strpos (self::$objFormDataContainer['accepted_mime_types'], _PIPE) == FALSE) {
                // Add something more to the array, if we didn't find a PIPE, for explode () below;
                self::$objFormDataContainer['accepted_mime_types'] .= $aNotSoRandomString;
            }

            $acceptedMIMETypes = explode (_PIPE, self::$objFormDataContainer['accepted_mime_types']);
            foreach ($_FILES as $k => $v) {
                if (!in_array ($v['type'], $acceptedMIMETypes)) {
                    if (count ($acceptedMIMETypes) == 2) {
                        self::$objFormDataContainer['accepted_mime_types'] = str_replace ($aNotSoRandomString,
                        _NONE, self::$objFormDataContainer['accepted_mime_types']);
                    }
                    if (isset (self::$objFormDataContainer['upload_error_message'])) {
                        self::setErrorOnInput (new S ($k), self::$objFormDataContainer['upload_error_message']);
                    } else {
                        self::setErrorOnInput (new S ($k), new S (INVALID_FILE_TYPE . _SP .
                        implode (', ', $acceptedMIMETypes)));
                    }
                }
            }
        }
    }

    /**
     * Will move uploaded files to our own temporary directory;
     *
     * This method will move uploaded files to our own temporary directory, while transfering the _FILES array, to the session
     * storage. This way, for multi-paged forms, we have a way of keeping information saved for every new form page.
     *
     * @return void It doesn't need to return anything;
    */
    private static function setFILEOperationsOnForm () {
        $uploadDir = DOCUMENT_ROOT . UPLOAD_DIR . _S . TEMP_DIR . _S;
        foreach ($_FILES as $k => $v) {
            is_uploaded_file   ($_FILES[$k]['tmp_name'])                                                     ?
            move_uploaded_file ($_FILES[$k]['tmp_name'], $uploadDir . basename ($_FILES[$k]['tmp_name']))    :
            self::renderScreenOfDeath (new S (__CLASS__), new S (UPLOAD_ERROR), new S (UPLOAD_ERROR_FIX));
        }
        $_SESSION['FILES'] = $_FILES;
        // Add names to our _SESSION['POST'], so we can auto-insert/auto-update them;
        if (!empty ($_SESSION['FILES'])) {
            foreach ($_SESSION['FILES'] as $k => $v) {
                $_SESSION['POST'][$k] = basename ($v['name']);
            }
        }
    }

    /**
     * Will sanitize given paths, so that we are as compatibles as we possibly can with every other Web technology out there;
     *
     * This method, sanitizePATH, will catch the path string and change it so that it will rename it to something with just
     * [a-zA-Z0-9 and a few other allowed chars]. We do this because it was proven that some Web technologies are sensitive to
     * for example commas, like Javascript, like Flash and some other web techs;
     *
     * @param S $objStringToCLEAN The path to sanitize ...
     * @return string Will return the same string, only cleaned ...
     */
    public static function sanitizePATH (S $objStringToCLEAN) {
        return $objStringToCLEAN->stripTags ()->eregReplace ('[^a-zA-Z0-9_.-]', _SP)
        ->trimLeft ()->trimRight ()->eregReplace (_SP, _U)->toLower ();
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static function moveFILEUploads () {
        if (isset (self::$objFormDataContainer['upload_dir'])) {
            $uploadDirectory = DOCUMENT_ROOT . UPLOAD_DIR . _S . self::$objFormDataContainer['upload_dir'] . _S;
            if (!is_dir ($uploadDirectory)) {
                // Issue a mkdir, to make the directory;
                if (!mkdir ($uploadDirectory, 0777, TRUE)) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (UPLOAD_ERROR),
                    new S (UPLOAD_ERROR_FIX));
                }
            }
            // We use the /tmp/php*****, generated by php, which has a slash;
            self::$objImageTimestampPrefix = $_SERVER['REQUEST_TIME'];
            foreach ($_SESSION['FILES'] as $k => $v) {
                // Process the K ...
                $k = new S ($k);
                $_SESSION['FILES'][$k->toString ()]['name'] = new S (self::$objImageTimestampPrefix . _U .
                $_SESSION['FILES'][$k->toString ()]['name']);
                $_SESSION['FILES'][$k->toString ()]['name'] = self::sanitizePATH ($_SESSION['FILES'][$k->toString ()]['name']);

                // Process uploaded files, so that they contain the prefix;
                $uploadInputFromPOST = new S (self::$objImageTimestampPrefix . _U . self::getPOST ($k));
                $uploadInputFromPOST = self::sanitizePATH ($uploadInputFromPOST);
                self::setPOST ($k, $uploadInputFromPOST);
                $uploadedFileMovedToDirectory = $uploadDirectory . $_SESSION['FILES'][$k->toString ()]['name'];

                // Do something specific for WIN ...
                if (WIN == TRUE) {
                    $_SESSION['FILES'][$k->toString ()]['tmp_name'] = new S ($_SESSION['FILES'][$k->toString ()]['tmp_name']);
                    $_SESSION['FILES'][$k->toString ()]['tmp_name']->doToken (DIRECTORY_SEPARATOR, _S);
                    $_SESSION['FILES'][$k->toString ()]['tmp_name']->doToken (dirname (dirname (DOCUMENT_ROOT)), _NONE);
                    $temporaryDirectory = new S (DOCUMENT_ROOT . UPLOAD_DIR . $_SESSION['FILES'][$k->toString ()]['tmp_name']);
                    rename ($temporaryDirectory, $uploadedFileMovedToDirectory);
                } else {
                    // Rename, on Linux;
                    $temporaryDirectory = new S (DOCUMENT_ROOT . UPLOAD_DIR . $_SESSION['FILES'][$k->toString ()]['tmp_name']);
                    rename ($temporaryDirectory, $uploadedFileMovedToDirectory);
                }
            }
        } else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (UPLOAD_DIR_NOT_SPECIFIED),
            new S (UPLOAD_DIR_NOT_SPECIFIED_FIX));
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static function resizeFILEUploads () {
        if (isset (self::$objFormDataContainer['upload_dir']) && isset (self::$objFormDataContainer['upload_resize_img'])) {
            $uploadDirectory = new FileDirectory (UPLOAD_DIR . _S . self::$objFormDataContainer['upload_dir'] . _S);
            $temporaryResizeArray = self::$objFormDataContainer['upload_resize_img'];
            self::resizeImageFromUploadPATH ($uploadDirectory, new A ($_SESSION['FILES']), $temporaryResizeArray);
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static function setFormFooterOnInputEnd () {
        self::$objFormStarted->setInt (0);
        # REMEMBER ME: Removed check for empty ACTION ...
        # If other bugs apear, be sure to check!
        if (self::checkPOST ()->toBoolean () == TRUE) {
            if (sizeof (self::$objFormErrorField) != 0) {
                self::$objFormPassedExecution->setInt (0);
            } else {
                self::$objFormPassedExecution->setInt (1);
            }

            if (self::$objFormPassedExecution->toInt () == 1) {
                if (sizeof ($_SESSION['POST']) != 0 && sizeof ($_FILES) != 0 &&
                self::$objFormDataContainer['enctype'] == 'multipart/form-data') {
                    // Check we're tied to the INPUT ...
                    if (isset (self::$objFormDataContainer['SQL_operations_tied_to_input'])) {
                        if (isset ($_SESSION['POST'][self::$objFormDataContainer['SQL_operations_tied_to_input']])) {
                            self::checkFILEOperationsOnForm ();
                            self::setFILEOperationsOnForm ();
                            self::moveFILEUploads ();
                            self::resizeFILEUploads ();
                        }
                    }
                }

                if (sizeof ($_SESSION['POST']) != 0) {
                    if (isset (self::$objFormDataContainer['SQL_operations_tied_to_input'])) {
                        if (isset ($_SESSION['POST'][self::$objFormDataContainer['SQL_operations_tied_to_input']])) {
                            self::setSQLOperationsOnForm ();
                        }
                    }

                    if (isset (self::$objFormDataContainer['redirect_if_ok'])) {
                        if (isset (self::$objFormDataContainer['redirect_after_exec'])) {
                            $redirectFromLastPage = $_SERVER['REQUEST_URI'];
                            $redirectFromLastPage = str_replace (RELATIVE_PATH, _NONE, $redirectFromLastPage);
                            $redirectFromLastPage = substr ($redirectFromLastPage, 2);
                            $redirectFromLastPage = DOCUMENT_HOST . $redirectFromLastPage;
                            self::setPOST ('redirect_last_page', $redirectFromLastPage);
                            # Set redirect after execution;
                            self::setPOST ('redirect_after_exec', self::$objFormDataContainer['redirect_after_exec']);
                        }

                        // Unset, _SESSION['POST'];
                        if (self::checkPOST ()->toBoolean () == TRUE) {
                            unset ($_SESSION['POST']);
                        }

                        // Unset, _SESSION['FILE'];
                        if (isset ($_SESSION['FILES'])) {
                            unset ($_SESSION['FILES']);
                        }

                        # REMEMBER ME: We've added this to avoid stupid redirections with ->setRedirect ...
                        # Should remember to check back at it ...
                        // Add the redirect_if_ok header;
                        self::setHeaderKey (self::$objFormDataContainer['redirect_if_ok'], new S ('Location'));
                        // Exit, the script executed;
                        exit (0);
                    }
                }
            }
        }
        // Do the form ending;
        $tp = new FilePath (FORM_TP_DIR . _S . 'frm_input_form_ended.tp');
        if (self::$objFormInFieldSet->toInt () == 1) {
            $formFieldSet = new B (TRUE);
            self::tpSet ($formFieldSet, new S ('formFieldSet'), $tp);
            self::$objFormInFieldSet->setInt (0);
        }
        self::tpSet (new O ($_SERVER['REQUEST_TIME']), new S ('dummy'), $tp);
        self::tpExe ($tp);
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static function setCoreInputAttributes (A $inputAttributes, &$tp) {
        $coreSetAttributes = new A;
        foreach ($inputAttributes as $k => $v) {
            switch ($k) {
                case 'reg_javascript_replace':
                    // On Key Pressed ...
                    $jssTpPath = new FileContent (FORM_TP_DIR . _S . JAVASCRIPT_DIR . _S . 'checkKeyEvent.js');
                    $coreSetAttributes['onkeypress'] = $jssTpPath->doToken ('[%REPLACE_VALUE_EVENT%]', $v);
                    // On Lost Focus  ...
                    $jssTpPath = new FileContent (FORM_TP_DIR . _S . JAVASCRIPT_DIR . _S . 'checkKeyEventRegExp.js');
                    $coreSetAttributes['onblur'] = $jssTpPath->doToken ('[%REGEXP_REPLACE_JS%]', $v);
                    self::manageJSS (new FilePath (FORM_TP_DIR . _WS . JAVASCRIPT_DIR . _WS . 'checkKey.js'),
                    new S ('RA_checkKey'));
                break;
                default:
                    $coreSetAttributes[$k] = $v;
                break;
            }
        }
        self::tpSet ($coreSetAttributes, new S ('coreSetAttributes'), $tp, new S ('explode'));
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static function setCoreAttributes (A $inputAttributes, FilePath $tp) {
        $coreSetAttributes = new A;
        foreach ($inputAttributes as $k => $v) {
            switch ($k) {
                case 'value':
                    // Do a checking for 'space' or 'zero' value content, where that == FALSE usually ...
                    if (isset ($v) && empty ($v) && ($v != '0')) {
                        // Due to some weird PHP passing of variables
                        $coreSetAttributes[$k] = 'non_space_or_false_replacement_string';
                    } else if (isset ($v) && ($v == '0')) {
                        // Due to some weird PHP passing of variables, if coreInputValue == 0, we make it a string.
                        $coreSetAttributes[$k] = 'non_zero_or_false_replacement_string';
                    }
                break;
                case 'id':
                    // Set a tag ID, same as the coresponding <input
                    if (isset ($inputAttributes['tag'])) {
                        $coreSetAttributes['tagfid'] = $v;
                        $coreSetAttributes['tagtxt'] = $inputAttributes['tag'];
                    }
                    // Set the <input id ...
                    $coreSetAttributes[$k] = $v;
                break;
                case 'name':
                    // Add some tags, error messages and other stuff that are strictly mapped to the 'name' of an input;
                    if (array_key_exists ($v->toString (), self::$objFormErrorField->toArray ())) {
                        $coreSetAttributes['err_msg'] = self::$objFormErrorField[$v];
                        // If we detect an error, set the css class, needed to highlight the input ...
                        (isset ($coreSetAttributes['class']))   ?
                        (FALSE)                                 :
                        ($coreSetAttributes['class'] = DEFAULT_ERROR_CSS_CLASS);
                        // Set the alignment of the error message;
                        if (isset ($inputAttributes['err_msg_align'])) {
                            $coreSetAttributes['err_msg_align'] = $inputAttributes['err_msg_align'];
                        }
                    }
                    $coreSetAttributes[$k] = $v;
                break;
                case 'class':
                    $coreSetAttributes[$k] = $v;
                    if (array_key_exists ($inputAttributes['name']->toString (), self::$objFormErrorField)) {
                        // Add the DEFAULT_ERROR_CSS_CLASS, if we detect an error ...
                        $coreSetAttributes[$k] .= _SP . DEFAULT_ERROR_CSS_CLASS;
                    }
                break;
                default:
                    $coreSetAttributes[$k] = $v;
                break;
            }
        }
        // Explode the coreSetAttributes array, to every key/var pair;
        self::tpSet ($coreSetAttributes, new S ('coreSetAttributes'), $tp, new S ('explode'));
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static final function optionSELECTOnPOST (I $objKey) {
        if (self::$objDataContainer[$objKey]['type'] == new S ('option')) {
            if (isset ($_SESSION['POST'][self::$objDataContainer[$objKey]['bound_to']])) {
                if ($_SESSION['POST'][self::$objDataContainer[$objKey]['bound_to']] == self::$objDataContainer[$objKey]['value']) {
                    self::$objDataContainer[$objKey]['selected'] = new S ('yes');
                }
            }
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static final function checkboxRadioSQLOperations (I $objKey) {
        if (self::$objDataContainer[$objKey]['type'] == new S ('checkbox') ||
        self::$objDataContainer[$objKey]['type'] == new S ('radio')) {
            if (isset ($_SESSION['POST'][self::$objDataContainer[$objKey]['name']])) {
                self::$objDataContainer[$objKey]['checked'] = new S ('yes');
                self::$objDataContainer[$objKey]['value'] = $_SESSION['POST'][self::$objDataContainer[$objKey]['name']];
            } else {
                # Update radio and checkboxes ...
                $q = new S ('UPDATE %table SET %who = "" WHERE %condition');
                $q->doToken ('%table', self::$objUpdateTableName)
                ->doToken ('%who', self::$objDataContainer[$objKey]['name'])
                ->doToken ('%condition', self::$objUpdateWhere);
                self::getQuery ($q);
                if (!isset (self::$objDataContainer[$objKey]['value'])) {
                    self::$objDataContainer[$objKey]['value'] = new S ('on');
                }
            }
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static final function sessionToInputValueSetter (I $objKey) {
        if (isset (self::$objDataContainer[$objKey]['name'])) {
            if (isset ($_SESSION['POST'][self::$objDataContainer[$objKey]['name']])) {
                self::$objDataContainer[$objKey]['value'] = new S ($_SESSION['POST'][self::$objDataContainer[$objKey]['name']]);
            }
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static final function queryToInputValueSetter (I $objKey) {
        if (self::$objDataContainer[$objKey]['type'] == 'checkbox' || self::$objDataContainer[$objKey]['type'] == 'radio') {
            if (self::$objUpdateSELECTData[0][self::$objDataContainer[$objKey]['name']] != _NONE) {
                if (self::$objDataContainer[$objKey]['type'] == 'radio') {
                    if (self::$objDataContainer[$objKey]['value'] ==
                    self::$objUpdateSELECTData[0][self::$objDataContainer[$objKey]['name']]) {
                        self::$objDataContainer[$objKey]['checked'] = new S ('yes');
                    }
                } else {
                    self::$objDataContainer[$objKey]['checked'] = new S ('yes');
                }
            } else {
                if (isset (self::$objDataContainer[$objKey]['value'])) {
                    if (self::$objDataContainer[$objKey]['value'] == _NONE) {
                        self::$objDataContainer[$objKey]['value'] = new S ('on');
                    }
                } else {
                    self::$objDataContainer[$objKey]['value'] = new S ('on');
                }
            }
        } else {
            if (self::$objUpdateSELECTData[0][self::$objDataContainer[$objKey]['name']] != _NONE) {
                self::$objDataContainer[$objKey]['value'] =
                self::$objUpdateSELECTData[0][self::$objDataContainer[$objKey]['name']];
            }
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static final function makeInputFileController (I $objKey) {
        if (isset (self::$objDataContainer[$objKey]['file_controller']) &&
            isset (self::$objDataContainer[$objKey]['id']) && self::$objDataContainer[$objKey]['type'] == new S ('file')) {
            self::manageJSS (new FilePath (FORM_TP_DIR . _S . JAVASCRIPT_DIR . _S .
            'enableDisableFormElement.js'), new S ('edFormElement'));
            self::$objDataContainer[$objKey]['disabled'] = new S ('yes');
            self::$objDataContainer[$objKey]['file_controller_id'] =
            self::$objDataContainer[$objKey]['id'];
        }
    }

    /**
     * Will execute necessary SQL operations on the current form ...
     *
     * This method, setSQLOperationsOnForm, you don't need to know the internal workings of it. This is because, as you can see
     * it is a private method, and thus, it's used internally by the class, to achieve it's goals of execution. Just pass on
     * and read any other 'public' or 'protected' method out there ...
     *
     * @param array $inputAttributes The core attributes (found in every HTML element) that can be set;
     * @param string $tp The path to the .tp file where to set those variables;
    */
    private static final function updateObjectPropertiesonSQLUpdateorInsert () {
        if (isset (self::$objFormDataContainer['update_or_insert']) && isset (self::$objFormDataContainer['table_name'])) {
            if (isset (self::$objFormDataContainer['update_where'])) {
                // Set it to what ever the developer wants it set to ...
                self::$objUpdateWhere = self::$objFormDataContainer['update_where'];
            } else {
                self::$objUpdateWhere = new S ('%who = "%what"');
                self::$objUpdateWhere->doToken ('%who',  self::$objUpdateUpdateField);
                self::$objUpdateWhere->doToken ('%what', self::$objUpdateUpdateId);
            }

            // Check to see which operation to execute ...
            if (self::$objFormDataContainer['update_or_insert'] == new S ('update')) {
                // Do we have multiple or simple updates ...
                if (isset (self::$objFormDataContainer['table_join_on']) &&
                    isset (self::$objFormDataContainer['table_save_into'])) {
                    self::$objUpdateTableName = new S (self::$objFormDataContainer['table_name'] . _SP
                                              . self::$objFormDataContainer['table_join_on']);
                    $updateTable = self::$objFormDataContainer['table_save_into']->toValues ();
                    $updateTableCount = count ($updateTable);
                    $updateTable[$updateTableCount++] = self::$objFormDataContainer['table_name'];
                    for ($i = 0; $i < $updateTableCount; ++$i) {
                        self::$objUpdateTableFields = new A (array_merge (self::$objUpdateTableFields->toArray (),
                        self::getFieldsFromTable (new S ($updateTable[$i]))->toArray ()));
                    }
                } else {
                    self::$objUpdateTableName   = self::$objFormDataContainer['table_name'];
                    self::$objUpdateTableFields = self::getFieldsFromTable (self::$objUpdateTableName);
                }
            }

            if (!self::checkPOST ()->toBoolean () == TRUE) {
                $q = new S ('SELECT * FROM %table WHERE %condition LIMIT 1');
                $q->doToken ('%table', self::$objUpdateTableName)
                ->doToken ('%condition', self::$objUpdateWhere);
                self::$objUpdateSELECTData = self::getQuery ($q);
            }
        }
    }

    /**
     * Will retrieve all ajax errors registered for this form;
     *
     * This method will return the exact code we need to to AJAX form validation. We need at the point of calling to discard all
     * output that's been done by the script since now, and we need to JSON encode the error array we received. It's a nice way
     * to enable ajaxified forms, with as little code as possible;
     */
    private static final function getAjaxErrorsOnInput () {
        // Do a foreach, on the PHP array;
        $objArray = new A (Array ('ajax_error_show_before_input' => 0));
        foreach (self::$objFormErrorField as $k => $v) { $objArray[$k] = $v->toString (); }

        // Encode the stuff ...
        self::outputAjaxJSON ($objArray);
    }

    /**
     * Will convert the _SESSION['POST'] to framework strings;
     *
     * This method will take any information sent by the _POST method, and convert them to strings, putting them in the _SESSION
     * also, to allow our mechanism of redirection if the form is valid to work properly;
     */
    private static function convertPOSTToSESSIONString () {
        if (self::checkPOST ()->toBoolean () == TRUE) {
            $objDoEncodeAgain = new B (FALSE);
            // Check if we should ENCODE it again;
            foreach ($_SESSION['POST'] as $k => $v) {
                if (!($_SESSION['POST'][$k] instanceof S)) {
                    $objDoEncodeAgain = new B (TRUE);
                    break;
                }
            }

            if ($objDoEncodeAgain->toBoolean () == TRUE) {
                foreach ($_SESSION['POST'] as $k => $v) {
                    // Do the samba ...
                    $v = new S ($v);
                    $_SESSION['POST'][$k] = $v;
                    # REMEMBER ME: Could help, at searching with QUOTES,
                    # to do an encoding, and stripslashes afterwards; TO BE TRIED;
                    # Code: $_SESSION['POST'][$k] = $v->entityDecode (ENT_QUOTES)->stripSlashes ();
                }
            }
        }
    }
}
?>
