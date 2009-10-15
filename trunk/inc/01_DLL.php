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
############# Motto: 'There's always a first time! so said an ancient philosopher ...';
interface IFaceEXE {
    // EXE
    public static function setExeTime (S $timeSlice);
    public static function getExeTime (S $timeSliceA, S $timeSliceB);
    public static function getUniqueCode (I $objLength = NULL);
    public static function getUniqueHash (S $objHashAlgo, S $objStringData);
    public function getObjectCLASS (M $checkedObject = NULL);
    public function executeCommand (S $objCommandName, A $objPassedParameters = NULL);

    // INI (1) & OBS (2) & HDR (2) & APC (1) & ERR (2) ...
    public static function getRAPHPFrameworkAverageLoading ();
    public static function discardOutputStream (B $whatKindOfCleaning = NULL);
    public static function disableFurtherOutput ();
    public static function setHeaderKey (S $headerContent, S $headerType);
    public static function setHeaderStr (S $headerString);
    public static function rewriteHTLine (S $htString);
    public static function executionStreamedOutput ($bufferedStringToParse, $bufferState);
    public static function executionCatchPHPErrors ($errorType, $errorString, $errorFile, $errorFileNo);
}

interface IFaceURL extends IFaceEXE {
    // URL
    public static function doCleanURLPath ();
    public static function getURLFromString (S $objURLString);
    public static function staticURL  (A $getKey = NULL, A $getVar = NULL, S $getSuffix = NULL);
    public static function rewriteURL (A $getKey = NULL, A $getVar = NULL, S $getSuffix = NULL);
}

interface IFaceTPL extends IFaceEXE {
    // TPL
    public static function switchTTL ();
    public static function switchHTML();
    public static function setDocumentType (S $documentType);
    public static function manageTTL (S $webPageStringTitle);
    public static function manageCSS (FilePath $relativeWebCSSFile, S $relativeWebCSSFileTag = NULL);
    public static function manageLNK (S $metaTAGName, S $metaTAGRelated = NULL, S $metaTAGType = NULL, S $metaTAGContent = NULL);
    public static function manageEQV (S $metaTAGName, S $metaTAGContent = NULL);
    public static function manageTAG (S $metaTAGName, S $metaTAGContent = NULL);
    public static function manageJSS (FilePath $relativeWebJSSFile, S $relativeWebJSSFileTag = NULL);
    public static function setJSSKey (S $jssSESSIONKey, M $jssSESSIONVariable, FilePath $jssFile);
    public static function getJSSKey (S $jssSESSIONKey);
    public static function setCSSKey (S $cssSESSIONKey, M $cssSESSIONVariable, FilePath $cssFile);
    public static function getCSSKey (S $cssSESSIONKey);
    public static function outputAjaxJSON (A $objArrayToJSON);
    public static function outputAjaxString (S $objStringToOutput);
    public static function outputXMLString (S $objStringToOutput);
    public static function getSitemapRSSOrXML (S $objType);
    public static function tpIni (FilePath $tpFileName, I $templateCacheTime = NULL, B $tpInvariable = NULL);
    public static function tpSet (M $tpVar, S $tpVarString, FilePath $tpFileName, S $tpAction = NULL);
    public static function tpExe (FilePath $tpFileName, B $tpAsAJAXResponse = NULL);
    public static function tpEnd (S $cacheFile, B $getContent = NULL);
}

interface IFaceFRM extends IFaceTPL {
    // FRM
    public function getAjaxErrors ();
    public function checkFormHasErrors ();
    public function wasSubmitted (S $formSubmitName);
    public static function setErrorOnInput (S $inputWithError, S $errorMessage);
    public static function setExtraUpdateData (S $fieldKeyName, S $fieldValue);
    public static function setAction (S $objFormAttributeVar);
    public static function setEnctype (S $objFormAttributeVar);
    public static function setMethod (S $objFormAttributeVar);
    public static function setTarget (S $objFormAttributeVar);
    public static function setId (S $objFormAttributeVar);
    public static function setMask (S $objFormAttributeVar);
    public static function setClass (S $objFormAttributeVar);
    public static function setName (S $objFormAttributeVar);
    public static function setTitle (S $objFormAttributeVar);
    public static function setStyle (S $objFormAttributeVar);
    public static function setTextDirection (S $objFormAttributeVar);
    public static function setAccessKey (S $objFormAttributeVar);
    public static function setTabIndex (S $objFormAttributeVar);
    public static function setLanguage (S $objFormAttributeVar);
    public static function setOnSubmit (S $objFormAttributeVar);
    public static function setOnReset (S $objFormAttributeVar);
    public static function setOnClick (S $objFormAttributeVar);
    public static function setOnDblClick (S $objFormAttributeVar);
    public static function setOnMouseDown (S $objFormAttributeVar);
    public static function setOnMouseUp (S $objFormAttributeVar);
    public static function setOnMouseOver (S $objFormAttributeVar);
    public static function setOnMouseMove (S $objFormAttributeVar);
    public static function setOnMouseOut (S $objFormAttributeVar);
    public static function setOnKeyPress (S $objFormAttributeVar);
    public static function setOnKeyDown (S $objFormAttributeVar);
    public static function setOnKeyUp (S $objFormAttributeVar);
    public static function setInputType (S $objFormAttributeVar);
    public static function setValue (S $objFormAttributeVar);
    public static function setSize (S $objFormAttributeVar);
    public static function setAcceptFileType (S $objFormAttributeVar);
    public static function setMaxLength (S $objFormAttributeVar);
    public static function setRegExpType (S $objFormAttributeVar);
    public static function setJSRegExpReplace (S $objFormAttributeVar);
    public static function setRegExpErrMsg (S $objFormAttributeVar);
    public static function setPHPRegExpCheck (S $objFormAttributeVar);
    public static function setInputInfoMessage (S $objFormAttributeVar);
    public static function setToolTip (S $objFormAttributeVar);
    public static function setImageSource (S $objFormAttributeVar);
    public static function setImageAlternative (S $objFormAttributeVar);
    public static function setRows (S $objFormAttributeVar);
    public static function setColumns (S $objFormAttributeVar);
    public static function setTinyMCETextarea (B $objFormAttributeVar);
    public static function setOnBlur (S $objFormAttributeVar);
    public static function setOnFocus (S $objFormAttributeVar);
    public static function setOnChange (S $objFormAttributeVar);
    public static function setTableName (S $objFormAttributeVar);
    public static function setTableJoinOn (S $objFormAttributeVar);
    public static function setTableMapping (A $objFormAttributeVar);
    public static function setUpdateId (S $objFormAttributeVar);
    public static function setUpdateWhere (S $objFormAttributeVar);
    public static function setUpdateField (S $objFormAttributeVar);
    public static function setSQLAction (S $objFormAttributeVar);
    public static function setRedirect (S $objFormAttributeVar);
    public static function setUploadType (S $objFormAttributeVar);
    public static function setUploadErrMsg (S $objFormAttributeVar);
    public static function setUploadDirectory (S $objFormAttributeVar);
    public static function setUploadImageResize (A $objFormAttributeVar);
    public static function setExtra (S $objFormAttributeVar);
    public static function setFieldset (S $objFormAttributeVar);
    public static function setLabel (S $objFormAttributeVar);
    public static function setContainerDiv (B $objFormAttributeVar);
    public static function setAJAXEnabledForm (B $objFormAttributeVar);
    public static function setMultiple (B $objFormAttributeVar);
    public static function setSelected (B $objFormAttributeVar);
    public static function setDisabled (B $objFormAttributeVar);
    public static function setChecked (B $objFormAttributeVar);
    public static function setReadOnly (B $objFormAttributeVar);
    public static function setFileController (B $objFormAttributeVar);
    public static function setMPTTRemoveUnique (B $objFormAttributeVar);
    public static function setFormEndAndExecute (B $objFormAttributeVar);
    public static function registerFormHook (S $actionFunctionOfUser, S $whereToRegister);
    public static function setPOST (S $nameOfKey, S $keyContent);
    public static function unsetPOST (S $nameOfKey = NULL);
    public static function getPOST (S $formInputToGet = NULL);
    public static function checkPOST (S $varToCheck = NULL);
}

interface IFaceCNF extends IFaceFRM {
    // CNF
    public function setConfigKey (S $objConfigKey, S $objConfigVar);
    public function getConfigKey (S $objConfigKey);
}

interface IFaceMOD extends IFaceCNF {
    // MOD
    public static function checkModuleIsRegistered (S $modNameToCheck);
    public static function activateModule (FilePath $modNameToActivate, B $modGetCLASS = NULL);
}

    /**
     * Abstract mapping class, used to MAP DataType (DT) methods to already existing PHP functions.
     *
     * For example, we have the PHP str_replace function, that we can 'map' to the S (String) DataType, by using one of the below.
     * This way we add functionality by ordering passed parameters in an array that we pass when calling the PHP function,
     * without much work, which is exactly what we need for quick, bug-free features ...
     *
     * @package RA::DataTypes
     *
     * @method object escapeCStr ()			PHP mapped function:	addcslashes;
     * @method object escapeStr () 			PHP mapped function:	addslashes;
     * @method object toHex () 				PHP mapped function:	bin2hex;
     * @method object toChunk ()			PHP mapped function:	chunk_split;
     * @method object encryptIt ()			PHP mapped function:	crypt;
     * @method object chrToASCII ()			PHP mapped function:	chr;
     * @method object convertCYR ()			PHP mapped function:	convert_cyr_stirng;
     * @method object uDecode ()			PHP mapped function:	conver_uudecode;
     * @method object uEncode ()			PHP mapped function:	conver_uuencode;
     * @method object counteChar ()			PHP mapped function:	count_chars;
     * @method object toCRC32 ()			PHP mapped function:	crc32;
     * @method object toHebrew ()			PHP mapped function:	hebrev;
     * @method object toNLHebrew ()			PHP mapped function:	hebrevc;
     * @method object entityDecode ()		PHP mapped function:	html_entity_decode;
     * @method object entityEncode ()		PHP mapped function:	htmlentities;
     * @method object charDecode ()			PHP mapped function:	htmlspecialchars_decode;
     * @method object charEncode ()			PHP mapped function:	htmlspecialchars;
     * @method object trimLeft ()			PHP mapped function:	ltrim;
     * @method object trimRight ()			PHP mapped function:	rtrim;
     * @method object toMD5File ()			PHP mapped function:	md5_file;
     * @method object toMD5 ()				PHP mapped function:	md5;
     * @method object toMetaphoneKey ()		PHP mapped function:	metaphone;
     * @method object toMoneyFormat ()		PHP mapped function:	money_format;
     * @method object nL2BR ()				PHP mapped function:	nl2br;
     * @method object ordToASCII ()			PHP mapped function:	ord;
     * @method object qpDecode ()			PHP mapped function:	quoted_printable_decode;
     * @method object qpEncode ()			PHP mapped function:	quoted_printable_encode;
     * @method object toSHA1File ()			PHP mapped function:	sha1_file;
     * @method object toSHA1 ()				PHP mapped function:	sha1;
     * @method object toSoundEx ()			PHP mapped function:	soundex;
     * @method object doCSV ()				PHP mapped function:	str_getcsv;
     * @method object replaceIToken ()		PHP mapped function:	strireplace;
     * @method object doPad ()				PHP mapped function:	str_pad;
     * @method object doRepeat ()			PHP mapped function:	str_repeat;
     * @method object doShuffle ()			PHP mapped function:	str_shuffle;
     * @method object toROT13 ()			PHP mapped function:	str_rot13;
     * @method object doSplit ()			PHP mapped function:	str_split;
     * @method object toWordCount ()		PHP mapped function:	word_count;
     * @method object compareCaseTo ()		PHP mapped function:	strcasecmp;
     * @method object compareNCaseTo ()		PHP mapped function:	strncasecmp;
     * @method object compareTo ()			PHP mapped function:	strcmp;
     * @method object compareNTo ()			PHP mapped function:	strncmp;
     * @method object stripTags ()			PHP mapped function:	strip_tags;
     * @method object removeCStr ()			PHP mapped function:	stripcslashes;
     * @method object removeStr ()			PHP mapped function:	stripslashes;
     * @method object findIPos ()			PHP mapped function:	stripos;
     * @method object findPos ()			PHP mapped function:	strpos;
     * @method object findILPos ()			PHP mapped function:	strripos;
     * @method object findLPos ()			PHP mapped function:	strrpos;
     * @method object findIFirst ()			PHP mapped function:	stristr;
     * @method object findFirst ()			PHP mapped function:	strstr;
     * @method object findLast ()			PHP mapped function:	strrchr;
     * @method object doReverse ()			PHP mapped function:	strrev;
     * @method object toLength ()			PHP mapped function:	strlen;
     * @method object natCaseCmp ()			PHP mapped function:	strnatcasecmp;
     * @method object natCmp ()				PHP mapped function:	strnatcmp;
     * @method object charSearch ()			PHP mapped function:	strpbrk;
     * @method object doTokenize ()			PHP mapped function:	strtok;
     * @method object toLower ()			PHP mapped function:	strlower;;
     * @method object toUpper ()			PHP mapped function:	strupper;
     * @method object doTranslate ()		PHP mapped function:	strtr;
     * @method object doSubStr ()			PHP mapped function:	substr;
     * @method object doSubCompare ()		PHP mapped function:	substr_compare;
     * @method object doSubCount ()			PHP mapped function:	substr_count;
     * @method object doSubReplace ()		PHP mapped function:	substr_replace;
     * @method object doWrap ()				PHP mapped function:	wordwrap; (and others);
     * # See the INI.php object, along-side the MAP.php object where they're mapped further;
     */
abstract class M {
	/**
	 * @staticvar array $objFuncMapper Contains the PHP functions, mapped to a DataType method. Thus, it's easy for us to
	 * determine which PHP function was mapped to what DataType method, and act accordingly;
	 */
	private static $objFuncMapper              = NULL;
	/**
	 * @staticvar string $objCLASS Contains the result of the current object CLASS. It's automatically determined by a call to
	 * the PHP get_class (&$this) function, do be sure that it will be mapped accordingly to the proper type of CLASS;
	 */
	private static $objCLASS                   = NULL;
	/**
     * @var mixed $varContainer Contains whatever the object must contain. The 'O' class, which is a direct descendant of the 'M'
     * class can contain anything, while S, I, F, etc. will contain specific data types according to their type of object. We
     * get the advantage of Strong Type Hinting (STH) by working with objects rather than PHP type, which are prone to wild
     * casting without notice;
    */
	protected $varContainer                    = NULL;

	/**
	 * Map a PHP function/object method, to a DataType method.
	 *
	 * What this actually means is that we can add a mapping between PHP functions (or object methods, as long as the calling
	 * parameter is call_user_func/call_user_func_array compatible) and DataType methods. We do this mapping in a special called
	 * user-land function, found in the dev/hdr directory;
     *
     * Why?! Because we can map multiple methods to the same PHP function, which should make us be able to call tr_replace
     * or any other function by a common name, (ex. 'replaceStr') ... and issue THE SAME features with our DataTypes, without
     * touching a single line of code, which in fact is a good thing I might add;
     *
     * This is such a special feature, that a couple of tutorials/how-to must be written along side the documentation that we
     * introduce here, where we will be able to clearly show all the advantages that way for working with the framework has, which
     * should make quite a few developers understand why we called it RA [Revolutionary Algorithms] PHP Framework;
     *
     * @return boolean Will map a PHP function to a DT method;
     * @param string $objCalledMethod The called object method that needs to be mapped;
     * @param string $objPHPFunction The PHP function that will take the arguments;
    */
    public static function mapMethodToFunction ($objCalledMethod, $objPHPFunction) {
    	switch (self::$objFuncMapper == NULL) {
    		case TRUE:
    			// Make it an array;
    			self::$objFuncMapper = new A;
    		default:
    			// Go ... Map-out called methods, to PHP functions;
    			self::$objFuncMapper[$objCalledMethod] = $objPHPFunction;
    		break;
    	}

    	// Else, do something;
    	return new B (FALSE);
    }

    /**
     * Using the magic __toString method, to output the contents of our DataType;
     *
     * Here we use the PHP magic __toString, to enable us to do simple operations like 'echo $theObject', and return a really nice
     * string representation of that object. It's really interesting how you can actually evolve this __toString method in
     * something really complicated and complex. For example, we could return an object that contains a table from the database,
     * but when we will echo it, it will render a JS data-grid for us, with full functionality.
     *
     * The way we implemented DataTypes can make the average 'Joe' user, extend the framework DataTypes, and develop his own
     * DataTypes, while keeping compatibility with the core of the framework. This way, many times he will overload the __toString
     * method also, implementing some kind of 'specific HTML code' for it, which should make usage of his objects simpler. For
     * example, he could make a 'Recent Articles' Widget, and use that in many contexts issuing just an 'echo $recentArticles' to
     * display the output.
     *
     * @return string Returns a casted string of the contents of the object
    */
    public function & __toString () {
        // First, we check if this container, is something that can be represented as a string,
        // If not, we do a manual casting to a string
        // If that's OK, than we have no problem at echoing such a content,
        // Else, we issue an error. The developer using the code didn't use it properly.
        if (is_string ($this->varContainer)) {
            return $this->varContainer;
        } else if (is_string ($castedVarContainer = (string) $this->varContainer)) {
            return $castedVarContainer;
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_SET),
                new S (VAR_NOT_SET_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_SET);
            }
        }
    }

    /**
     * Will copy the contents of A to B, if A and B are both DataTypes of the framework;
     *
     * What is this method used for!? Well, doing some copying from A to B, without destroying to much stuff in the process. Why?
     * For starters, it uses the objects setMix () method which sets the proper type of DataType, and it doesn't copy an objects
     * entire properties, but only the value contained in the this->varContainer; We've already stated that one instance of a DT
     * contains one, and only one value, compatible to that specific DT (arrays are special, they don't count!);
     *
     * We could rely for example on A = B, but that would mean copying any other properties from A to B, which is something we
     * really don't want every time. For example, if by any change we extend the core DataTypes of our framework, than for sure
     * we don't want something as destructive as an A = B copying, but we just want to copy the contents of A to B, in a manner
     * compatible to the framework. Uses: in edge-cases probably, where some weird object data movement/copying must be done;
     *
     * @return object Given an object, it will modify the contents of that object to the one the current one has.
     * @param object $modifiedArgument The passed argument as reference, where it needs to be modified ...
    */
    public final function & copyTo (O $modifiedArgument) {
        // Warning, this IS_NOT_METHOD_CHAIN, BUT RATHER CONTINUING FROM modifyReference, with the passed object;
        // Modify an external reference given to this object && method-chain;
        // We assume is of type O, which by default has a ->setMix method that we can use;
        return $modifiedArgument->setMix ($this->varContainer);
    }

    /**
     * Will move the contents of A to B, being concept compatible to the way the copyTo method works;
     *
     * Well, as above, this function actually "moves the content". Ok ... I must be honest. It doesn't move it as it should, it
     * actually copies it from A to B, than deletes it from A. It 'continues' the method-chain from where B left of. This is an
     * interesting aspect of the framework, in that you can do variable object chains, jumping from object to object. Well, this
     * kind of 'jumping beans' are a subject of a long tutorial that tries to teach the best way to use it.
     *
     * @return object Given an object, it will modify the contents of that object to the one the current one has.
     * @param object $modifiedArgument The passed argument as reference, where it needs to be modified ...
    */
    public final function & moveTo (O $modifiedArgument) {
        // Warning, this IS_NOT_METHOD_CHAIN, BUT RATHER CONTINUING FROM modifyReference, with the passed object;
        // Modify an external reference given to this object && method-chain;
        // We assume is of type O, which by default has a ->setMix method that we can use;
        $modifiedArgument->setMix ($this->varContainer);
        $this->varContainer = NULL;
        return $modifiedArgument;
    }

	/**
	 * Will make a check on the contents of the current DataType, returning a boolean result;
	 *
	 * This method, checkIs, will make one of the predefined checks to see if a variable is of a set type or not. The good
     * thing is that hooks can be added, so for example, if an developer needs to map-out the checkIs () method to some
     * specific abstract data type, it can do so ...
     *
     * These are the LISTED:
     * 	<ul>
     *      <li>set :: checks if it's != NULL or empty ...</li>
     *      <li>arr :: checks if it's array;</li>
     *      <li>bln :: checks if it's boolean;</li>
     *      <li>flt :: checks if it's float;</li>
     *      <li>int :: checks if it's integer;</li>
     *      <li>nbr :: checks if it's number;</li>
     *      <li>obj :: checks if it's object;</li>
     *      <li>res :: checks if it's resource;</li>
     *      <li>str :: checks if it's string;</li>
     *      <li>default: check it's NULL;</li>
     * 	</ul>
     *
     * @return string Returns boolean, and it's a generic function for many is_* PHP functions;
     * @param string $whatType What type of checkIs ('verification') to do ...
     * @todo Implement hooks to the checkIs () method ... Thus it will allow the developer to dynamically "hook" into the
     * checkIs method, with his user-defined code ... Nice one, doc!
    */
    public final function checkIs ($whatType = NULL) {
        switch ($whatType) {
            case 'set':
                return new B (
                ($this->varContainer != NULL) ||
                !(empty ($this->varContainer)));
            case 'arr':
                return new B (
                is_array
                ($this->varContainer));
            break;
            case 'bln':
                return new B (
                is_bool
                ($this->varContainer));
            break;
            case 'flt':
                return new B (
                is_float
                ($this->varContainer));
            break;
            case 'int':
                return new B (
                is_int
                ($this->varContainer));
            break;
            case 'nbr':
                return new B (
                is_numeric
                ($this->varContainer));
            break;
            case 'obj':
                return new B (
                is_object
                ($this->varContainer));
            break;
            case 'res':
                return new B (
                is_resource
                ($this->varContainer));
            break;
            case 'str':
                return new B (
                is_string
                ($this->varContainer));
            break;
            default:
                return new B (
                is_null
                ($this->varContainer));
            break;
        }
    }

    /**
     * Using the magic PHP method __call, as the engine for our 'mapping' scheme;
     *
     * This method, __call, will make a call to a user-defined-function or a PHP-specific function, and process the result, while
     * trying to map the returned result to one of our DataTypes: for short, if the return result is compatible to one of our
     * DataTypes which should be by default, it will return an object of that kind, assuring ST (strong types);
     *
     * In simple terms, it's a nice way to retrieve the result of an action, and making sure the chain is still working. It will
     * return a basic DT, defined in RA, meaning that a return type can make the method-chain go further. Oh, and I forgot to tell
     * you, THE MECHANISM ISN'T RESTRICTED JUST TO PHP FUNCTIONS, MEANING: that if you define a CLASS, or a function that can
     * be called compatible with how call_user_func/call_user_func_array CALLs them, than the power of the mapping mechanism
     * is infinite by definition;
     *
     * Why?! Think of inheritance, what can be done if we're able to call a static method of one of our children?!
     * You guessed, it helps us do 'magic' things ... that are truly ... 'magic' :D. We will document this in a
     * tutorial, because the concept is so complicated in how it was developed, but so simple in how you're going to use it,
     * as it's a shame to let it pass by. The next documentation revision will add the necessary example code;
     *
     * @return mixed Does a call to the user-defined-function and processes the result ...
     * @param string $nameOfHook Name of the invoked method;
     * @param string $argumentsOfHook Arguments passed to that invoked method, that are in a numerically indexed array;
    */
    public function __CALL ($nameOfHook, $argumentsOfHook) {
        // Get the current CLASS;
    	self::$objCLASS = get_class ($this);

    	// Mapping PHP to DataTypes;
    	if (isset (self::$objFuncMapper[$nameOfHook])) {
    		// Map to PHPF:
    		return CALL_USER_FUNC (self::$objCLASS, $this, $nameOfHook, self::$objFuncMapper[$nameOfHook], $argumentsOfHook);
    	} else {
    		// Map to USER:
    		return CALL_USER_FUNC ($nameOfHook, $this, $argumentsOfHook);
    	}
    }

    /**
     * This method, renderScreenOfDeath, would do it's best to try to render an error screen using the static method of
     * ERR::renderScreenOfDeath, which is the first non-abstract descendant CLASS that takes care of error reporting & handling;
     *
     * The name of the 'ERR' CLASS is subject to change, so THIS IS THE ONLY PLACE where the complex auto-loading, loose-coupling
     * RA PHP Framework is able to fail. Thus, the lack of knowledge that a parent object has over it's children, makes it quite
     * hard to output any kind of useful information unless statically called. THAT MEANS: that if the [ERR] CLASS changes its
     * name, you, the developer are obligated to change it here also. This can be done through a smart editor, like Eclipse PDT
     * to ensure that this kind of 'code refactoring' is done OK;
     *
     * By default, any error screen assumes the following:
     * 	<ol>
     *  	<li>geSHi and Firebug plugins work as expected;</li>
     *  	<li>ERROR_REPORTING_LEVEL is set to E_ALL;</li>
     *	</ol>
     *
     * @return string Tries to render the 'Screen Of Death', or fails throwing an Exception, at least.
     * @param string $errFrom From what SOURCE did the error come from;
     * @param string $errString What's the error string we're interested in;
     * @param string $errTip Can we help the developer, by providing an error tip?!
     * @param string $debugErrString If we hava a PHP generated debugString, use it ...
    */
    protected static function renderScreenOfDeath (S $errFrom, S $errString, S $errTip, S $debugErrString = NULL) {
        if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
            // IF the 'ERR' CLASS exists;
            // IF the 'ERR' screen doesn't show-up sometime, this is __THE PLACE__ to search for errors;
            return ERR::renderScreenOfDeath ($errFrom, $errString, $errTip, $debugErrString);
        } else {
            // Else, throw an even bigger error;
            throw new Exception (CANNOT_OUTPUT_ERROR_SCREEN);
        }
    }

    /**
     * This method, checkCanOutputErrorScreen, will check if we can use the error screen output mechanism, or we must resort
     * to throwing an Exception, that will cause a FATAL screen at the end.
     *
     * It's a basic mechanism, so instead of returning boolean (B) DataType, which in case of error will cause an inherent
     * infinite loop, it will return a basic PHP  boolean. Many of the 'above M' CLASS usually return their instance of the object
     * but because we're still making a 'map between (our) DataTypes and PHP', the 'M' class must convey to PHP and its use;
     *
     * @return boolean Will check if we can output a proper error screen, or must we throw an error ...
    */
    protected static function checkCanOutputErrorScreen () {
        // Just check if the ERR object exists ...
        return new B (class_exists ('ERR'));
    }

	/**
     * Method to do chain appending, and the fact that we actually wanted to use a 'method' to return the 'this' instance, is
     * quite simple: for future use!
     *
     * We, for the moment don't really know what kind of weird, dynamic, not-yet-thought conditions we may end-up putting here,
     * but instead of modifying 10, 100, N files or N^N code lines, we can just return to this method, and do the necessary
     * conditioning. God knows what we'll be able to think of ...
     *
     * return object The current instance of the DataType;
     */
    protected function & returnToChain () {
        // Easier, than writing return (this) every time;
        return $this;
    }
}

	/**
     * The 'O' (Object) DataType, that can hold any PHP-compatible value that can be set by it's setter methods;
     *
     * Before we started to actually build the S (String), I (Integer), F (Float) and any other DataType in the system, we needed
     * a base for all of our DTs. Why this?! Well, what if we have a method that just DOESN'T care what you pass to it, as long
     * as the value inside is of more importance than the kind of data type it needs. That's why, the 'O' DataType was made for,
     * to be the backbone of all the other types.
     *
     * @package RA::DataTypes
	*/
class O extends M {
    /**
     * This method, __construct, will take the passed argument and issue it to ->setMix () which is going to be overloaded for
     * every object from here onwards.
     *
     * Thus, Strings, Integers, Floats will also have the setMix () method, but based on their specific setSt/setInt/setBoolean
     * methods, which for example can be a bridge between different DataTypes. What do I mean here?! Well, for DataType methods
     * that return something different than the base type, the setMix () method can be used to set the proper value, while doing
     * the necessary checks. Also, for bug-free cod, every __constructor will make a call either to this->setMix () or to the
     * specific DT setting method, to set the value of that DT;
     *
     * @return object Will return the current object instance;
     * @param mixed $passedArgument The passed argument at construction time;
    */
    public function & __construct ($passedArgument) {
        // Set the container, return what setMix returns;
        return $this->setMix ($passedArgument);
    }

	/**
     * Returns the contents of the current DataType, going to any depth to find a non-DataType PHP compatible value;
     *
     * If by any chance, the current DataType contains another DataType as it's value, the toMix () method will go to any possible
     * recursive depth to find the needed information that the user has stored in. This way we only have a "virtual" one layer
     * of depth, because anything else will make the framework go at any depth to retrieve the information, which for the user
     * will seem like it has only one possible depth, and nothing more;
     *
     * @return mixed Will return the mixed content ...
    */
    public final function & toMix () {
        if ($this->varContainer instanceof O) {
            // This, in theory, should end the cycle. Anyway, if you see this comment
            // it means that tests have been OK, and I've been to lazy to go back and remove this comment;
            return $this->varContainer->toMix ();
        } else {
            // If the container contains another object, get the contents from it;
            // This, I hope, will go recursive, because if have any kind of instanceof O,
            // it will go again, until it reaches a toMix, that will return it's varContainer;
            return $this->varContainer;
        }
    }

	/**
     * It gets and checks the passed parameter, and if it passes it will set the DataType to the content of the passed parameter;
     *
     * We will actually need to re-implement this method in any of the specific S, I, F, etc. DataTypes, because in it's current
     * form it will allow the framework to accept non-strong DataTypes, which is a big DOOR for errors. For example, considering
     * we have a S DataType, but we somehow use this method to set the contents of the DataType to what we need. Then, we have
     * a bug, a logical error that can cause problems. Big ones! Why?! Because the S (String) would not contain a string anymore!
     *
     * As you can see from the code, this method, if passed an instance of a DataType, will go to any depth in that DataType, to
     * find the proper content it needs to pass. This way, we can actually make a copy variable out of another. This feature must
     * be documented because many times, because of it's simplicity is left outside the scope or the user of the framework user,
     * maybe due to the lack of "publicity" between other features.
     *
     * @return object Returns the current instance, after the operations on the container have been finished;
     * @param mixed $passedArgument Passed method argument;
    */
    public function & setMix ($passedArgument) {
        if ($passedArgument instanceof O) {
            // This is to copy the value from object A to B, not to make it an object;
            // Set the container to passedArgument->toMix ();
            $this->varContainer = $passedArgument->toMix ();
        } else {
            // Set the container to passedArgument;
            $this->varContainer = $passedArgument;
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * This method, getDataType, taking the current contents of the container, will return the type of data it contains by
     * using the PHP gettype function.
     *
     * And yes, I know 'gettype' is rather SLOW, but taking in account the fact that it's not used that often, we guess it's a
     * good decision to live it here. A switch with is_* functions may have been quicker, in theory, but nevertheless, relying
     * on a PHP function is better than implementing user-land code. The chances that we're ever going to need it that much ar
     * quite slim, because of the fact that with STH, we already know what type of variable we're using. The only exception is
     * the 'O' DataType, for which we needed this.
     *
     * @return string Will return he current object type as a string ...
    */
    public final function getDataType () {
        // Do return ...
        return new S (gettype ($this->varContainer));
    }

    /**
     * This method, setDataType, will set the container to the data type specified in the argument. It will take either a PHP
     * string or a S DataType, and make the conversion ...
     *
     * As above, it's somewhat of an 'O' specific method, meaning that it's use is common only in in the 'O' DataType, and quite
     * questionable in DataTypes that inherit from O. In any case, you guessed it, it changes the PHP-type of the content inside
     * the DataType, to anything we pass as the argument. (a kind of casting some may say ...)
     *
     * @return string Will set the data type of the container to the specified argument ...
     * @param string The PHP type to convert the container to ...
    */
    public final function setDataType ($typeToConvertTo) {
        if (is_string ($typeToConvertTo)) {
            # Change the type of the container;
            if (settype ($this->varContainer, $typeToConvertTo)) {
                // Return to chain;
                return $this->returnToChain ();
            }
        } else if ($typeToConvertTo instanceof S) {
            if (settype ($this->varCotnainer, $typeToConvertTo->toString ())) {
                // Return to chain;
                return $this->returnToChain ();
            }
        }
    }
}
    /**
     * B: Boolean DataType, used to represent a TRUE/FALSE value, which can be at any time converted to an integer or even string
     * if the proper __toString/toString methods are implemented!
     *
     * A rather complex way of representing a boolean, by assigning it's value to an object. The good part of having and passing
     * objects instead of true PHP types is that abnormal changes in value can be detected. It would be horrible for a boolean
     * value to wildly change it's value due to weird assignments. We control this behaviour by the need to use the proper 'set'
     * methods, instead of doing A = B assignments. Clearer && Cleaner Code == poetry! as far as I'm concerned ...
     *
     * @package RA::DataTypes
     */
class B extends M {
    /**
     * The __constructor is used to pass the proper variable at construction time. It relies on the setBoolean method, which can
     * be used specific to this method, to enable proper variable initialization.
     *
     * Most of the methods should however rely on an overloaded setMix () method, or vice-versa, where the overloaded setMix ()
     * method is just a call to the set[SpecificDataType] method. Either way, the code here must be 100% sure that the type of
     * the CLASS == the type of what that object contains. Anything else, and it should output an error;
     *
     * @return object Will return the current instance of the object;
     * @param boolean $passedArgument The passed argument at construction time;
     */
    public function & __construct ($passedArgument) {
        return $this->setBoolean ($passedArgument);
    }

    /**
     * This method, toBoolean, will return the boolean value of the container, doing the same thing as toMix, making objects
     * inside the current object transparent to any depth making it a good way to avoid recursion ...
     *
     * We actually want to avoid having objects inside objects, inside objects to any depth possible, because that would just
     * create CHAOS in our code, and it's a decent way for novice PHP programmers to screw up things. Yes, in theory, but just in
     * theory, that's a good way to "discover bugs", but it's better to prevent them in the end, and concentrate on adding
     * feature rather than stopping and fixing bugs.
     *
     * @return boolean Will return the boolean value of the container ...
    */
    public final function & toBoolean () {
        # As previously written, go recursive down to find the toBoolean value of
        # a DT object;
        if ($this->varContainer instanceof B) {
            # Return the toBoolean () call of the function;
            return $this->varContainer->toBoolean ();
        } else {
            # Return the varContainer;
            return $this->varContainer;
        }
    }

    /**
     * This method, switchType, will switch the boolean value of the container, by negating it as is the quickest way in the west
     * to do it, and the chosen way for us.
     *
     * Why we use a method like this to do a value switching?! Because we have a boolean, and we may have an if condition where
     * we need to change the value by some weird condition. In short, it's just a shorthand for some edge-cases, so edge we're
     * going to need to document them or at least give some examples on how they work.
     *
     * @return object Will switch the boolean value of the container, by negating it ...
    */
    public final function & switchType () {
    	$this->varContainer = !($this->varContainer);
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * This method, getAsInt, will return the boolean value as an integer.
     *
     * It does the comparison between the container, and the booleans TRUE/FALSE. It issues 1 or 0 in those cases, or an error
     * screen if something else has happened ... It's error-proof, because it doesn't test THE absolute TRUE/FALSE values, leaving
     * that kind of debate to the day to day programmer, where he would to the comparison by returning the proper value.
     *
     * @return integer Will return the boolean value as an integer (1 or 0) ...
    */
    public final function getAsInt () {
        if ($this->varContainer == TRUE) {
            # Return 1 if it's TRUE;
            return new I (1);
        } else if ($this->varContainer == FALSE) {
            # Elese return 0;
            return new I (0);
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_BOOLEAN),
                new S (VAR_NOT_BOOLEAN_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_BOOLEAN);
            }
        }
    }

    /**
     * This method, setBoolean, will check if the passed arguments is of boolean type, and if it is, it will set the container
     * to that value or else, it will output an error indicating that the code has either executed wrong, or the developer
     * is using this function sideways ...
     *
     * This method is specific to the B (Boolean) DataType, in the fact that what it will do is set the contained variable to
     * TRUE/FALSE, only if the passed argument is of type Boolean or Integer. In the second case it will try to determine if the I
     * DataType is 0 or != 0, and set the TRUE/FALSE boolean types accordingly. This way we ensure a little layer of compatibility
     * between the B/I DataTypes, again by using methods, but that's life ...
     *
     * @return object Will set the container boolean value to the passed argument ...
     * @param boolean $passedArgument The passed argument, when calling the setBoolean method;
    */
    public final function & setBoolean ($passedArgument) {
        if (is_bool ($passedArgument)) {
            $this->varContainer = $passedArgument;
        } else if ($passedArgument instanceof B) {
            $this->varContainer = $passedArgument->toBoolean ();
        } else if ($passedArgument instanceof I) {
            if ($passedArgument->toInt () != 0) {
                $this->varContaienr = TRUE;
            } else {
                $this->varContainer = FALSE;
            }
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_BOOLEAN),
                new S (VAR_NOT_BOOLEAN_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_BOOLEAN);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }
}

    /**
     * I: Integer DataType, used to represent integers in the context of our framework;
     *
     * There's a warning to be set here: because of the fact that PHP doesn't support operator overloading, we're forced to add
     * methods that actually do incrementation/decrementation, etc. (and other) possible integer operations, which on a daily
     * basis is quite annoying. If we don't pass integers as parameters, than the PHP integer type should be used, but if a method
     * requires a specific integer to be passed, than we are forced to use the framework I (Integer) DataType, which will allow
     * our famous Strict Type Hinting to come in effect and assure us of proper functionality;
     *
     * @package RA::DataTypes
     */
class I extends M {
    /**
     * The __constructor is used to pass the proper variable at construction time. It relies on the setInt method, which can
     * be used specific to this method, to enable proper variable initialization.
     *
     * Most of the methods should however rely on an overloaded setMix () method, or vice-versa, where the overloaded setMix ()
     * method is just a call to the set[SpecificDataType] method. Either way, the code here must be 100% sure that the type of
     * the CLASS == the type of what that object contains. Anything else, and it should output an error;
     *
     * @return object Will return the current instance of the object;
     * @param boolean $passedArgument The passed argument at construction time;
     */
    public function & __construct ($passedArgument) {
        return $this->setInt ($passedArgument);
    }

    /**
     * This method, toInt, will return the integer value of the container. It doesn't do any further checks, but relies on the
     * fact that the setInt/setMix methods do their job right.
     *
     * As you can see from the code, the toInt () method is quite different from the toMix () method, because of the fact that
     * the setter methods do the actual checking of the code. What I mean is that because of the fact that the 'depth recursion'
     * problem is done when 'inserting' the contents of the DataType, we're free of doing the same functionality when we want to
     * return the value from it;
     *
     * You may wonder why we don't have a __toString/toString method. Well, in theory (but just in theory), PHP integer to string
     * casting is done Ok and bug-free, meaning that our M::__toString/toString methods will do their job right. It would've been
     * a nice thing if the double conversion from integer to string and backwards could have been done in A + B = C operation, but
     * for the moment that is just a dream;
     *
     * @return integer Will return the integer value of the container ...
    */
    public function & toInt () {
        return $this->varContainer;
    }

    /**
     * This method, setInt, will check that the passed argument is of integer type, or will output an error if not. Along with
     * ->setMix, this method assures that the container can hold ONLY one integer type.
     *
     * Looking at the toInt () method, you can see that, contrary to the parent DataType, we're doing the check for recursiveness
     * when calling the setInt () method, instead of "hiding the damage" in the toInt () method. In some edge cases this is little
     * check would've been much faster than doing depth recursion;
     *
     * @return object Will set the container to the specified passed argument ...
    */
    public function & setInt ($passedArgument) {
        if (is_int ($passedArgument)) {
            $this->varContainer = $passedArgument;
        } else if ($passedArgument instanceof I) {
            $this->varContainer = $passedArgument->toInt ();
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_INT),
                new S (VAR_NOT_INT_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_INT);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * Increments the content of the DataType, with the specified amount;
     *
     * This method, doInc, will increment the container with the required amount. If no argument is passed, the doInc method
     * will increment the container by one. (Like ++$i) ... We could also do a switch here that's set on the object, for
     * increases after use (Like $i++) ... but it's a discussion on whether that is as useful as it seems ...
     *
     * @return object Will increment the container by an amount, given in the passed parameter ...
     * @param integer $amountTo Needs to be an integer or NULL;
    */
    public function & doInc ($amountTo = NULL) {
        # To adding or instanceof;
        if ($amountTo instanceof I) {
            # Get the value from the amountTo;
            $this->varContainer += $amountTo->toInt ();
        } else {
            # One line, to rule'em all;
            ($amountTo != NULL)                 ?
            ($this->varContainer += $amountTo)  :
            (++$this->varContainer);
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * Decrements the content of the DataType, with the specified amount;
     *
     * This method, doDec, will decrement the container by the given amount, or if the passed argument is of NULL type (the
     * default parameter), it will reduce the container by one. (Same as doing --$i) ...
     *
     * @return object Will decrement the container by the given amount, or just by one ...
     * @param integer $amountTo Needs to be an integer or NULL;
    */
    public function & doDec ($amountTo = NULL) {
        if ($amountTo instanceof I) {
            # Get the value fromt the amountTo;
            $this->varContainer -= $amountTo->toInt ();
        } else {
            # One line, to rule me all ...
            ($amountTo != NULL)                 ?
            ($this->varContainer -= $amountTo)  :
            (--$this->varContainer);
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * Multiplies the content of the DataType, with the specified amount and if it's NULL it will double the value;
     *
     * This method, doMtp, will double the container (container * 2) or multiply it by a given value. It's the same mechanism
     * as in the case of doInc/doDec.
     *
     * @return object Will multiply the container by the given amount, or just by two (because it's common)
     * @param integer $withWhat Needs to be an integer or NULL;
    */
    public function & doMtp ($withWhat = NULL) {
        if ($withWhat instanceof I) {
            $this->varContainer *= $withWhat->toInt ();
        } else {
            # Multiplty;
            ($withWhat != NULL)                 ?
            $this->varContainer *= $withWhat    :
            $this->varContainer *= 2;
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * Will divide the content of the DataType, by the given amount, checking to be sure that no N/0 operations are done;
     *
     * This method, doDiv, will divide the container by the givena mount, taking great care so that divisions by 0, won't
     * output weird errors. We should at least throw some kind of error if the division argument is 0, so that the programmer
     * knows he's doing something terribly wrong ...
     *
     * @return object Will divide the container by the given amount ...
     * @param integer $byWhat Needs to be an integer, and != 0;
    */
    public function & doDiv ($byWhat) {
        if ($this->varContainer == 0) {
            # Just go further;
            return $this->returnToChain ();
        } else {
            if ($byWhat instanceof I) {
                if ($byWhat->toInt () != 0) {
                    $this->varContainer /= $byWhat->toInt ();
                    return $this->returnToChain ();
                }
            } else {
                if ($byWhat != 0) {
                    $this->varContainer /= $byWhat;
                    // Return to chain;
                    return $this->returnToChain ();
                }
            }
        }
    }

    /**
     * Will set the content of the DataType, to the modulus of the content and the passed modulus parameter;
     *
     * This method, doMod, will module the container by the given amount, taking care of the instanceof I, or simple integer
     * scheme. It will set the container to that modulo. It does the quick modulus operations, so the processor won't be to
     * caught-up with parsing tokens (reference to how the PHP parser treats PHP code);
     *
     * @return object Will module the container by the given amount ...
     * @param integer $byWhat Needs to be an integer that can be used in a modulus operation;
    */
    public function & doMod ($byWhat) {
        if ($byWhat instanceof I) {
            $this->varContainer %= $byWhat->toInt ();
        } else {
            $this->varContainer %= $byWhat;
        }
        // Return to chain;
        return $this->returnToChain ();
    }
}

    /**
     * F: Float DataType, extended from I, it allows us to retain and do calculations on floating values;
     *
     * As you can see, we extend from the I (Integer) DataType, because we want to use the basic operations that the doInc, doDec,
     * doMtp, doDiv, doMod methods do. If you look at the code in those methods you'll see that we don't do any checking of
     * the passed parameters, because we expect the developer to pass a proper I or integer as a parameter;
     *
     * If we really did some kind of checking than a layer of incompatibility between integers and floats would have appeared,
     * require a series of methods that would've fixed the issue. This way, we are sure that a division by string for example,
     * if the passed parameter is not a numeric value will raise an error which we'll be able to catch in our error handler, thus
     * reaching our goal of identifying such mistakes. It's probable that future versions of the framework will implement a better
     * integer/float DataType, while keeping backward compatibility with this code.
     *
     * @package RA::DataTypes
     */
class F extends I {
    /**
     * Set the value of the DataType, upon construction;
     *
     * The constructor uses the setFlt method, which implements the actual code. As we see here, actually passing floats needs
     * that the passed parameter be an actual float. We don't test for integers or convert them to float, but we do accept our
     * types of I.
     *
     * As you can see, we're implementing the integer/float layer of our framework to be compatible only with the DataTypes
     * provided by our framework. Anything else would allow for some serious bugs or useless checks which isn't our goal for the
     * moment. Of course we will provide better support for this layer as soon as need for it is proven a de-facto standard;
     *
     * @return object The current DataType instance;
     * @param float $passedArgument The passed, expected float parameter;
     */
    public function & __construct ($passedArgument) {
        return $this->setFlt ($passedArgument);
    }

    /**
     * Gets the float value contained in the DataType;
     *
     * Again, a specific method is used to return the value contained in the DataType, while the parent toInt () method is still
     * callable. For the moment need has made that F be a direct descendat of I, but it seems that usage imposes that F be an
     * extension of M, rather than an extension of I, just to have a better layout of our basic DataTypes;
     *
     * @return float The contained DataType value;
     */
    public function & toFlt () {
        return $this->varContainer;
    }

    /**
     * Set the F (Float) DataType to a specified value;
     *
     * If you've read this far you're probably used to the architecture in which we have built the sistem. This method is not
     * that different. It implements the code needed to set the specific value of the DataType, and expects a float parameter or
     * at least an instance of I, whichever comes first;
     *
     * @return object The current object instance;
     * @param float $passedArgument The float parameter passed to the method;
     */
    public function & setFlt ($passedArgument) {
        if (is_float ($passedArgument)) {
            $this->varContainer = $passedArgument;
        } else if ($passedArgument instanceof I) {
            $this->varContainer = $passedArgument->toInt ();
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_FLOAT),
                new S (VAR_NOT_FLOAT_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_FLOAT);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }
}

    /**
     * S: String DataType, allows us to store strings, and have a bunch of mapped or un-mapped methods on them;
     *
     * This DataType, extends from M, as it's a base DataType for our framework. As the above already known DataTypes, it supports
     * mapping, and many of the string operation methods are implemented as mapped methods between the PHP and our framework. In
     * any case, the mapping strategy is a little slower than actual PHP code, so as time passes, we should implement mapped
     * methods as object methods, just for performance reasons.
     *
     * @package RA::DataTypes
     */
class S extends M implements ArrayAccess {
	/**
     * Implementing the ArrayAcces offsetGet () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed The contents of the current passed key as parameter;
     * @param mixed $offsetKey Needs an existing key, so it won't output an error;
     */
	public function offsetGet ($offsetKey) {
		return $this->varContainer[$offsetKey];
	}

	/**
     * Implementing the ArrayAcces offsetSet () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed The contents of the current passed key as parameter;
     * @param mixed $offsetKey Needs an existing key, so it won't output an error;
     * @param mixed $offsetVar What to change at the offset key ...;
     */
	public function offsetSet ($offsetKey, $offsetVar) {
		return $this->varContainer[$offsetKey] = $offsetVar;
	}

	/**
     * Implementing the ArrayAcces offsetExists () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed The contents of the current passed key as parameter;
     * @param mixed $offsetKey Needs an existing key, so it won't output an error;
     */
	public function offsetExists ($offsetKey) {
		return isset ($this->varContainer[$offsetKey]);
	}

	/**
     * Implementing the ArrayAcces offsetUnset () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed The contents of the current passed key as parameter;
     * @param mixed $offsetKey Needs an existing key, so it won't output an error;
     */
	public function offsetUnset ($offsetKey) {
		return $this->varContainer[$offsetKey] = NULL;
	}

    /**
     * Takes an argument at construction time, uses the specific DataType method to check that passed parameter is a string;
     *
     * The constructor, as in any other cases, takes the argument passed to it and saves in the DataType, checking it to be an
     * actual string in this case. If it doesn't it will echo an error at least. It will return the current DataType instance,
     * so the instance can be assigned to a variable.
     *
     * @return object The current object instance;
     * @param string $passedArgument Expecting a string argument;
     */
    public function & __construct ($passedArgument = _NONE) {
        return $this->setString ($passedArgument);
    }

    /**
     * Redefinition of 'M' __toString, but re-implemented;
     *
     * We redefine the 'M' __toString method, because we already know that the contents of the DataType is a string, so we just
     * might return the content, without any othr casting or checks, which, of course, is a bit faster, not by much, but even that
     * performance gain is of great importance to us;
     *
     * @return string The contents of the DataType;
     */
    public function & __toString () {
        return $this->varContainer;
    }

    /**
     * Explicit calling of the string method, for some edge-cases where this is needed;
     *
     * There are some edge-cases (and you can search for them by using a file search in Eclipse for example) where we don't have
     * a PHP string context, and we are actually forced to call the specific ->toString () method of the DataType. Although, these
     * cases are rare, it's a good thing to have a way to manually call the 'toString' method, and get the DataType contents. And,
     * yes, I know, we could have called __toString from our code, but that's ugly programming and we want to avoid it;
     *
     * @return string The contents of the DataType;
     */
    public function & toString () {
        return $this->varContainer;
    }

    /**
     * Setting the strinc content of the DataType;
     *
     * Expects a string parameter, or uses the default one, whichever comes first, this method, as any other setter methods in
     * the context of our DataTypes, will set the container to the string parameter we pass to it; Besides PHP strings, we also
     * accept instaces of S, from which we copy the information;
     *
     * @return object The current instance of the DataType;
     * @param string $passedArgument Expects a string or an instance of a string;
     */
    public function & setString ($passedArgument = _NONE) {
        if (is_string ($passedArgument)) {
            $this->varContainer = $passedArgument;
        } else if ($passedArgument instanceof S) {
            $this->varContainer = $passedArgument->toString ();
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_STRING),
                new S (VAR_NOT_STRING_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_STRING);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * Appends a string to our own string;
     *
     * The name says it all. As we're unable to do concatenation, we must provide methods for it. I know it's easier to just
     * concatenate using the PHP syntax, but where would life be without a little more horror?
     *
     * @return object The current instance of the DataType;
     * @param string $passedArgument A string argument;
     */
    public function & appendString ($passedArgument) {
        // Check if NULL, and return ...
        if ($passedArgument == NULL) {
            // Return to chain;
            return $this->returnToChain ();
        }

        if ($passedArgument instanceof S) {
            $this->varContainer .= $passedArgument->toString ();
        } else if (is_string ($passedArgument)) {
            $this->varContainer .= $passedArgument;
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_STRING),
                new S (VAR_NOT_STRING_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_STRING);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     * Prepend a string to our own string;
     *
     * The inverse operation of appendString. People are still wondering why do we go to such enormous lengths for operations
     * that could be done quite easy without methods that do appending/prepending. Well, it's simple: having methods and doing
     * very deep checking alows us developers to add features with little care about bugs or developer errors;
     *
     * @return object The current object instance;
     * @param string $passedArgument Expecting a string argument;
     */
    public function & prependString ($passedArgument) {
        // Check if NULL, and return ...
        if ($passedArgument == NULL) {
            // Return to chain;
            return $this->returnToChain ();
        }

        if ($passedArgument instanceof S) {
            $this->varContainer = $passedArgument->toString () . $this->varContainer;
        } else if (is_string ($passedArgument)) {
            $this->varContainer = $passedArgument . $this->varContainer;
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_STRING),
                new S (VAR_NOT_STRING_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_STRING);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }

    /**
     *
     * This method, doToken, is a 'non-mapped method of the PHP str_replace function';
     *
     * If we were that lazy, we could have mapped 'str_replace' to 'doToken', in our DTs, but because we use doToken
     * quite extensivelly, for performance reasons, we chose to implement the actual code. From our perspective any mapped
     * PHP function to DataType methods, that are used extensivelly should have code implemented in the core of the framework.
     *
     * The above ideology allows us to have a limited set of operations on a DataType, while we would give great care only to that
     * set of methods that are used extensivelly. As time and need will force us to use specific methods, we are going to go to
     * that length to implement mapped methods as actual code, rather than relying on our mapper;
     *
     * @return object Will replace the given string token, with the given string replacement string ...
    */
    public function & doToken ($whatToReplace, $withWhatToReplace) {
        $this->varContainer = str_replace ($whatToReplace, $withWhatToReplace, $this->varContainer);
        return $this->returnToChain ();
    }
}

    /**
     * R: Resource DataType, just a container for any PHP returned resources;
     *
     * For the moment, the R DataType is a placeholder for common resource results. We will be able to develop this DataType,
     * according to specific returned resources, if PHP allows us to do that. For example, if R detects it contains a MySQL
     * resource, it could automatically transform the resource into a result, or it could save it for example to a file, for
     * swapping on it if it's a BIG resource.
     *
     * In short, God knows what we can do with a DataType like R, but because we must think ahead, we just can't live a door
     * placeholder here, and come back to put the actual door here after the walls have been finished, because that would mean
     * that we have to destroy the walls so we can insert the frame of the door in the door placeholder. I hope that with this
     * one little example you guys understood why we have a R DataType;
     *
     * @package RA::DataTypes
     */
class R extends M {
    /**
     * Set the content of the resource at construction time;
     *
     * Expecting a resource as an argument; the constructor will set the container to the specified resource. Different from other
     * DataType constructors, is that it MUST have an argument passed at construction time, answering the question: why do you
     * need an R DataType, without anything in it;
     *
     * @return object The current object instance;
     * @param resource $passedArgument Expecting a resource argument;
     */
    public function & __construct ($passedArgument) {
        return $this->setResource ($passedArgument);
    }

    /**
     * Get the resource back from its container;
     *
     * The method will return the resource from its container. For the moment, as we've said in the DataType description, we
     * don't allow for something specific to be done here, but the R DataType can be extended to be used as a MySQL Resource,
     * or any other kind of Resource, and the ->toResource method can be used to do specific-by-resource operations on it's
     * contents. Possibilities are limitless!
     *
     * @return resource The contained resource;
     */
    public function & toResource () {
        return $this->varContainer;
    }

    /**
     * Setting the resource container to the passed argument;
     *
     * As any other DataType setter method, it sets the internal container to the passed resource. It expects a resource or an R
     * DataType as argument, and it will return the current instance of the DataType, so we can use it further down the line;
     *
     * @return object The current instance of the DataType;
     * @param resource $passedArgument Expects a resource as a parameter;
     */
    public function & setResource ($passedArgument) {
        if ($passedArgument instanceof R) {
            if (is_resource ($passedArgument->toMix ())) {
                $this->varContainer = $passedArgument->toMix ();
            }
        } else if (is_resource ($passedArgument)) {
            $this->varContainer = $passedArgument;
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_RESOURCE),
                new S (VAR_NOT_RESOURCE_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . VAR_NOT_RESOURCE);
            }
        }
        // Return to chain;
        return $this->returnToChain ();
    }
}

    /**
     * Array implementation on our framework, based on the SPL, developed by Marcus Boerger;
     *
     * What we first did, before starting to implement any DataTypes, is that we started to implement true compatible SPL-type
     * arrays in our framework. We've gone from one-depath arrays which were sadly used quite extensivelly, to having our array
     * implementation be able to support multiple-depth arrays, based on the offsetSet/offsetGet from the SPL;
     *
     * @package RA::DataTypes
     */
class A extends M implements ArrayAccess, Iterator, RecursiveIterator, SeekableIterator, Countable {
    /**
     * The __constructor, gets the passed parameter and checks if it's an array, on which case it goes recursive for it, or
     * advances and properly sets the container to the passed parameter, or it will output an error in the last case;
     *
     * @return object The current object instance;
     * @param array $passedArgument Expecting an array argument;
     */
    public function __construct ($passedArgument = NULL) {
    	if ($passedArgument != NULL) {
	    	foreach ($passedArgument as $k => $v) {
	    		if (is_array ($v)) {
	    			$this->varContainer[$k] = new A ($v);
	    		} else {
	    			$this->varContainer[$k] = $v;
	    		}
	    	}
        } else {
            if ($passedArgument != NULL) {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . OFFSET_KEY_NOT_SET);
                }
            } else {
                # Make an empty array out of it;
                $this->varContainer = array ();
            }
        }
    }

    /**
     * Returning the array contained in our A DataType;
     *
     * Sadly, PHP doesn't support array context, which means that we're obligated that when we pass an array to a PHP method,
     * to be sure we use the ->toArray () method. There is a fix for this, in that we can implement any known PHP function as
     * a DataType method, which should make things a little bit speedy and easier to use;
     *
     * @return array The contained array;
     */
    public function & toArray () {
        return $this->varContainer;
    }

    /**
     * Return only the values of the stored array;
     *
     * We use this method to return only the values of the stored array, without modyfing the container. To modify the container
     * we need another inherent method that will do that, for exampe 'setToValues ()', or any other name you can think of;
     *
     * @return array Returned result of array_values () on the contained array;
     */
    public function toValues () {
        return new A (array_values ($this->varContainer));
    }

    /**
     * Return only the keys of the stored array;
     *
     * We use this method to return only the keys of the stored array, without modyfing the stored array. Again, we need another
     * method that will actually modify the stored array, if that is what we want;
     *
     * @return array The result of the array_keys on the contained array;
     */
    public function toKeys () {
        return new A (array_keys ($this->varContainer));
    }

    /**
     * Return the RecursiveIteratorIterator of the stored array;
     *
     * Method to return the RecursiveIteratorIterator (... Iterator :D ...) of the current contained array. This allows us to
     * do a recursive representation of an array with a foreach loop. See: SPL for more information;
     *
     * @return object The RecursiveIteratorIterator of the current array;
     */
    public function toRecursive () {
        return new RecursiveIteratorIterator ($this);
    }

    /**
     * Returns the current index of the array;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed Current array contents;
     */
    public function current () {
        return current ($this->varContainer);
    }

    /**
     * Returns the next index of the array, and advances the array;
     *
     * Imlementing the SPL next () method, to allow us to do the 'foreach' construct on our object. There's no need for too much
     * extensive documentation on these set of methods that implement the SPL. Check out the SPL for more details
     *
     * @return mixed Current array contents;
     */
    public function next () {
    	return (FALSE !== next ($this->varContainer));
    }

    /**
     * Return true/false, if the current is a valid key;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * return boolean TRUE/FALSE, depending on the result of the FALSE !== current ();
     */
    public function valid () {
        return (FALSE !== current ($this->varContainer));
    }

    /**
     * Return true/false, if the current is a valid key;
     *
     * Imlementing the SPL rewind () method, to allow us to do the 'foreach' construct on our object. There's no need for too much
     * extensive documentation on these set of methods that implement the SPL. Check out the SPL for more details
     *
     * return boolean TRUE/FALSE, depending on the result of the FALSE !== next ();
     */
    public function rewind () {
    	return (FALSE !== reset ($this->varContainer));
    }

    /**
     * Implement SPL key () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed Result of the key () function on the current stored array;
     */
    public function key () {
        return key ($this->varContainer);
    }

    /**
     * Implement the count () method of the SPL Countable interface;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return integer Size of the current stored array;
     */
    public function count () {
        return sizeof ($this->varContainer);
    }

    /**
     * Implement the SPL RecursiveIterator hasChildren () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return boolean TRUE/FALSE, depending if the current index is an instance of self;
     */
    public function hasChildren () {
        return ($this->current () instanceof self);
    }

    /**
     * Implement the SPL RecursiveIterator getChildren () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed The current index, because it has children;
     */
    public function getChildren () {
        return $this->current ();
    }

    /**
     * Implement the SPL Seekable seek () method from the interface;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return void Doesn't return anything. It just seeks to the position. We could return TRUE/FALSE, instead of exception;
     */
    public function seek ($objIndex) {
    	$this->rewind ();
    	$objPosition = 0;
    	while ($objPosition < $objIndex && $this->valid ()) {
    		$this->next ();
    		$objPosition++;
    	}
    	if (!$this->valid ()) {
    		throw new OutOfBoundsException ('Invalid seek position');
    	}
    }

    /**
     * Implement of the ArrayAccess offsetSet () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return boolean TRUE/FALSE, depending on code execution;
     * @param mixed $offsetKey The array key to be set;
     * @param mixed $offsetString Can be anything that can be array-stored;
     */
    public function offsetSet ($offsetKey, $offsetString) {
    	if (is_array ($offsetString)) {
    		$offsetString = new A ($offsetString);
    	}
        if (empty ($offsetKey)) {
            $this->varContainer[count ($this->varContainer)] = $offsetString;
            return TRUE;
        }
        if ($offsetKey instanceof S) {
            if ($this->varContainer[$offsetKey->toString ()] = $offsetString) {
                return $this->returnToChain ();
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        } else if ($offsetKey instanceof I) {
            if ($this->varContainer[$offsetKey->toInt ()] = $offsetString) {
                return $this->returnToChain ();
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        } else if (is_int ($offsetKey)) {
            $this->varContainer[$offsetKey] = $offsetString;
            if (isset ($this->varContainer[$offsetKey])) {
                return $this->returnToChain ();
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        } else if (is_string ($offsetKey)) {
            $this->varContainer[$offsetKey] = $offsetString;
            if (isset ($this->varContainer[$offsetKey])) {
                return $this->returnToChain ();
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        }
    }

    /**
     * Implementing the ArrayAcces offsetGet () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return mixed The contents of the current passed key as parameter;
     * @param mixed $offsetKey Needs an existing key, so it won't output an error;
     */
    public function offsetGet ($offsetKey) {
        if ($offsetKey instanceof S) {
            if (!isset ($this->varContainer[$offsetKey->toString ()])) {
                $this->varContainer[$offsetKey->toString ()] = new A;
            }
            if (isset ($this->varContainer[$offsetKey->toString ()])) {
                return $this->varContainer[$offsetKey->toString ()];
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        } else if ($offsetKey instanceof I) {
            if (!isset ($this->varContainer[$offsetKey->toInt ()])) {
                $this->varContainer[$offsetKey->toInt ()] = new A;
            }
            if (isset ($this->varContainer[$offsetKey->toInt ()])) {
                return $this->varContainer[$offsetKey->toInt ()];
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        } else if (is_int ($offsetKey)) {
            if (!isset ($this->varContainer[$offsetKey])) {
                $this->varContainer[$offsetKey] = new A;
            }
            if (isset ($this->varContainer[$offsetKey])) {
                return $this->varContainer[$offsetKey];
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        } else if (is_string ($offsetKey)) {
            if (!isset ($this->varContainer[$offsetKey])) {
                $this->varContainer[$offsetKey] = new A;
            }
            if (isset ($this->varContainer[$offsetKey])) {
                return $this->varContainer[$offsetKey];
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (OFFSET_KEY_NOT_SET),
                    new S (OFFSET_KEY_NOT_SET_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                }
            }
        }
    }

    /**
     * Implementing the ArrayAccess offsetUnset () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return boolean TRUE/FALSE depending on code execution;
     * @param mixed $offsetKey Passed key to be unset;
     */
    public function offsetUnset ($offsetKey) {
        if ($offsetKey instanceof S) {
            if (isset ($this->varContainer[$offsetKey->toString ()])) {
                unset ($this->varContainer[$offsetKey->toString ()]);
            } else {
                if (DEBUG_DATA_TYPES >= 1) {
                    if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (OFFSET_KEY_NOT_SET),
                        new S (OFFSET_KEY_NOT_SET_FIX));
                    } else {
                        throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                    }
                }
            }
        } else if ($offsetKey instanceof I) {
            if (isset ($this->varContainer[$offsetKey->toInt ()])) {
                unset ($this->varContainer[$offsetKey->toInt ()]);
            } else {
                if (DEBUG_DATA_TYPES >= 1) {
                    if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (OFFSET_KEY_NOT_SET),
                        new S (OFFSET_KEY_NOT_SET_FIX));
                    } else {
                        throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                    }
                }
            }
        } else {
            if (isset ($this->varContainer[$offsetKey])) {
                unset ($this->varContainer[$offsetKey]);
            } else {
                if (DEBUG_DATA_TYPES >= 1) {
                    if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                        self::renderScreenOfDeath (new S (__CLASS__),
                        new S (OFFSET_KEY_NOT_SET),
                        new S (OFFSET_KEY_NOT_SET_FIX));
                    } else {
                        throw new Exception (__CLASS__ . _DCSP . 'Array index key was not set!');
                    }
                }
            }
        }
    }

    /**
     * Implementing the ArrayAcces offsetExists () method;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return boolean TRUE/FALSE, depending on code execution;
     * @param mixed $offsetKey The passed key to be checked for existence;
     */
    public function offsetExists ($offsetKey) {
        if ($offsetKey instanceof S) {
            # Return with key string;
            return isset ($this->varContainer[$offsetKey->toString ()]);
        } else if ($offsetKey instanceof I) {
            # Return with key integer;
            return isset ($this->varContainer[$offsetKey->toInt ()]);
        } else {
            # Either string w/o integer;
            return isset ($this->varContainer[$offsetKey]);
        }
    }

    /**
     * PHP array_unshift, as DataType method, for performance reasons;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return object The current object instance;
     */
    public function & arrayUnShift () {
        $getFunctionArguments = func_get_args ();
        foreach ($getFunctionArguments as $k => $v) {
            array_unshift ($this->varContainer, $v);
        }
        # Return to chain ...
        return $this->returnToChain ();
    }

    public function & arrayReverse () {
        $this->varContainer = array_reverse ($this->varContainer);
        # Return to chain ...
        return $this->returnToChain ();
    }

    /**
     * PHP method, for count (), easier to use when you know we have an array;
     *
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * @return integer The count of the current Array ()
     */
    public function doCount () {
        return new I (count ($this->varContainer));
    }
}

    /**
     * FilePath: Implementing FilePath, as relative paths from the webroot. They return error if the path we're trying to work on
     * doesn't exist.
     *
     * We need some kind of verification mechanism for file path that exist. Also, we wanted something that can be rathare quickly
     * extended from SPL, and from our S (String) DataType, which can allow us to check the existence of files at any USE time of
     * our files in the framework. What do mean by this?!
     *
     * We mean, that to avoid useless checks of files that exists, we can implement some methods on this DataType, and we'll be
     * sure that we have the file, we lock it to avoid deletion/writing/reading from it, and for the time of the execution of the
     * script, our files will be safe for processing. This way, we can interact with them faster by using predefined methods and
     * algorithms that work on files (also, we could extend functionality from them) and we'll be sure of at least 90% of good old
     * data-integrity. The rest of 10% are just cases where scripts go FATAL;
     *
     * @package RA::DataTypes
     */
class FilePath extends S {
    /*
     * @var boolean $pathExists Container for the boolean attribute pathExists, which we cache and check each time we want to do
     * an operation on the filethat requires the proper existence of the file and the specified path;
     */
    protected $pathExists = NULL;

    /**
     * Setting the path upon construction time, while providing a way for non-existing paths;
     *
     * As you can see, when constructiong a FilePath object, we're actually eager to have the possibility to trick the error
     * mechanism if we pass a non-existent path. We do that, because of the fact that we're actually trying to create a file,
     * before we use it, and that means that upon creation, we should be able to trick the error mechanism until the file is
     * created.
     *
     * @return object The current object instance;
     * @param string $passedArgument Expecting a realistic file path, or a virtual one if the second parameter is FALSE;
     * @param boolean $outputErrorOnMissingFile Trick the system NOT TO OUTPUT AN ERROR for the path we give it;
     */
    public function & __construct ($passedArgument, $outputErrorOnMissingFile = TRUE) {
        return $this->setPath ($passedArgument, $outputErrorOnMissingFile);
    }

    /**
     * Return the existing path, no matter what changes have been done to it;
     *
     * This method will return the existing file path, no matter what changes have been done to it. This means that in the case
     * we have passed a non-existing path, it will return the string. It's probable that if we have an existing path, files will
     * be moved.
     *
     * @return string The current contained file path;
     */
    public function & toExistingPath () {
        return $this->varContainer;
    }

    /**
     * Get the relative path, from the place where the application was installed;
     *
     * This method will return the relative path from where the root of the application was instaleed. This means, that before
     * returning anything it will do a str_replace on the path, replacing any occurence of the document root, in the current
     * contained path.
     *
     * @return string Relative path of the file from where the application was installed;
     */
    public function toRelativePath () {
        return new S (str_replace (DOCUMENT_ROOT, _NONE, $this->varContainer));
    }

    /**
     * Get the absolute path, by prepending the relative path with our document root;
     *
     * At the moment of calling our ->to* methods, we don't know if we have a path that contains the document root or not,
     * which means, that in order to be bug-proof, we will first do a replacement on the path, to block-out any still existing
     * occurence of the document root in the stored file path, after which we will do a string prepending of the document root;
     *
     * We could avoid such operations by actually checking the passed parameters at insertion/update times, thing that we'll be
     * doing in the future versions of the framework, while providing backward compatibility;
     *
     * @return string The absolute path of the contained file path, after processing;
     */
    public function toAbsolutePath () {
        return new S (DOCUMENT_ROOT . str_replace (DOCUMENT_ROOT, _NONE, $this->varContainer));
    }

    /**
     * Check some properties of the stored file path;
     *
     * Once in a while we need to check some special attributes of our stored file path, like the readable, writeable and
     * executable attributes, which will tell us what possible actions can we execute on the file, without echoing an error
     * or something. This is what this method does, if you pass the proper parameter;
     *
     * @return boolean TRUE/FALSE, depending on the attribute of the file path;
     * @param string $whatToCheckFor Attribute to be checked against file path;
     */
    public function checkPathIs ($whatToCheckFor) {
        switch ($whatToCheckFor) {
            case 'readable':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (is_readable ($this->varContainer));
                } else { return $this->pathExists; }
            break;
            case 'writeable':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (is_writeable ($this->varContainer));
                } else { return $this->pathExists; }
            break;
            case 'executable':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (is_executable ($this->varContainer));
                } else { return $this->pathExists; }
            break;
            case 'file':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (is_file ($this->varContainer));
                } else { return $this->pathExists; }
            break;
            case 'symlink':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (is_link ($this->varContainer));
                } else { return $this->pathExists; }
            break;
            case 'uploaded':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (is_uploaded_file ($this->varContainer));
                } else { return $this->pathExists; }
            break;
        }
    }

    /**
     * Change the file onwership of the path;
     *
     * This method will allow a chmod/chgrp on the specified file. It states that the file belongs to the user in the Apache/IIS
     * environment, which means that it will not check if the file does belong or not. In case it can't change the permissions on
     * the file, it will output a pure error, without no fall-back;
     *
     * @return boolean TRUE/FALSE, depending on passed parameters and file attributes;
     * @param mixed Either chmod 0xxx or chgrp 'aString', as passed parameters;
     * @param string $whatKindOfAccess Specify what kind of action to take on the stored file path;
     */
    public function setFileOwnership ($modeAccess, $whatKindOfAccess) {
        switch ($whatKindOfAccess) {
            case 'group':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (chgrp ($this->varContainer, $modeAccess));
                } else { return $this->pathExists; }
            break;
            case 'chmod':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new B (chmod ($this->varContainer, $modeAccess));
                } else { return $this->pathExists; }
            break;
        }
    }

    /**
     * Return information about the current stored path;
     *
     * This method will return whatever info you desire from the current file path. It doesn't do any weird checking on the
     * path to see if it's a file or a directory (as it should by the way), but the good thing is that it will return the
     * proper requested information;
     *
     * @return mixed Depends on every parameter that you pass;
     * @param string $whatInfoToGet The passed parameter, what info to get from the file;
     */
    public function getPathInfo ($whatInfoToGet) {
        switch ($whatInfoToGet) {
            case 'ftype':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new S (filetype ($this->varContainer));
                } else { return new S (_NONE); }
            break;
            case 'rpath':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new S (realpath ($this->varContainer));
                } else { return new S (_NONE); }
            break;
            case 'bname':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new S (basename ($this->varContainer));
                } else { return new S (_NONE); }
            break;
            case 'dname':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new S (dirname ($this->varContainer));
                } else { return new S (_NONE); }
            break;
            case 'atime':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (fileatime ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'ctime':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (filectime ($this->varCotnainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'mtime':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (filemtime ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'group':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (filegroup ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'inode':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (fileinode ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'owner':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (fileowner ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'chmod':
               if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (fileperms ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'fsize':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new I (filesize ($this->varContainer));
                } else { return $this->pathExists->getAsInt (); }
            break;
            case 'extension':
                if ($this->pathExists->toBoolean () == TRUE) {
                    return new S (pathinfo ($this->varContainer, PATHINFO_EXTENSION));
                } else { return new S (_NONE); }
            break;
        }
    }

    /**
     * Check if the path exists, or echo an error;
     *
     * This method will check that the path exists, and it's a file, or if it isn't a file, it will try at least to check that
     * the file path is a directory. If both fail, we have a problem, and we echo an error, if the passed parameter is true;
     *
     * @return boolean TRUE/FASE if the file path exists;
     * @param boolean $outputErrorOnMissingFile Should we bypass the error output?!
     */
   public function checkPathExists ($outputErrorOnMissingFile = TRUE) {
        if ($this->pathExists == NULL) {
            # Just return the true/false;
            $this->pathExists = new B (file_exists ($this->varContainer) && is_file ($this->varContainer));
            if ($this->pathExists->toBoolean () == FALSE) {
                $this->pathExists = new B (is_dir ($this->varContainer));
            }
            if ($this->pathExists->toBoolean () == FALSE) {
            	if ($outputErrorOnMissingFile == TRUE) {
	                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
	                    self::renderScreenOfDeath (new S (__CLASS__),
	                    new S (FILE_DOESNT_EXIST),
	                    new S (FILE_DOESNT_EXIST_FIX));
	                } else {
	                    throw new Exception (__CLASS__ . _DCSP . 'File does not exist!');
	                }
            	}
            } else {
                # Return the current pathExists;
                return $this->pathExists;
            }
        } else {
            # Return the current pathExists;
            return $this->pathExists;
        }
    }

    /**
     * Read the file path, if it's a file;
     *
     * We allow this method to read the stored file path if it's a file, directly into the output buffer of our executed script.
     * If not, we do nothing, because this is a method specific for reading files to the buffer;
     *
     * @return integer The number of bytes that have been read;
     */
    public function readFilePath () {
        if ($this->pathExists->toBoolean () == TRUE) {
            # Return the number of read chars, and output the content;
            return new I (readfile ($this->varContainer));
        }
    }

    /**
     * Delete the file path, remove recursive if it's a directory;
     *
     * With this method we delete a file if the current path is a file, or we recursivelly delete a directory and all it's known
     * contents, if the current file path is a directory. In case of errors, we should implement an error mechanism to allow
     * us to inform the user what went wrong;
     *
     * @return object The current object instance;
     */
    public function & unLinkPath () {
        if ($this->pathExists->toBoolean () == TRUE) {
            if (unlink ($this->varContainer)) {
                # Return to chain ...
                return $this->returnToChain ();
            }
        }
    }

    /**
     * Touch the stored file path;
     *
     * In case we've constructed the object with a path that for the moment doesn't exists, we can touch the path, if the writing
     * permissions for the user that the web server is under, has permissions to do that. If not, an error will sure be caught,
     * upon trying to use this method;
     *
     * @return object The current object instance;
     */
    public function & touchPath () {
        if (touch ($this->varContainer)) {
            # Return to chain ...
            return $this->returnToChain ();
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CANNOT_TOUCH_FILE),
                new S (CANNOT_TOUCH_FILE_FIX));
            }
        }
    }

    /**
     * Rename the stored path;
     *
     * This method will actually rename (or move) the stored path to a new path. It's a quick way to move files around, while
     * checking that we can actually do that before trying to do it.
     *
     * @return object The current object instance;
     * @param string $newRenamedName The new path where the file should be moved/renamed to;
     */
    public function & renamePath ($newRenamedName) {
        if (($this->pathExists->toBoolean () == TRUE) && !(file_exists ($newRenamedName))) {
            if (rename ($this->varContainer, $newRenamedName)) {
                $this->varContainer = $newRenamedName;
                return $this->returnToChain ();
            } else {
               if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (RENAME_OPERATION_FAILED),
                    new S (RENAME_OPERATION_FAILED_FIX));
                } else {
                    return $this->pathExists;
                }
            }
        } else {
            return $this->pathExists;
        }
    }

    /**
     * Copy the file (or directory) at the stored path, to a new path;
     *
     * This method will copy the file path, to a new path we give it as a parameter. It makes a copy of the file or recursive
     * copy of the directory, to the new specified path;
     *
     * @return object The current object instance;
     * @param string $newCopyName The new path where to copy the current path;
     */
    public function & copyPath ($newCopyName) {
        if (($this->pathExists->toBoolean () == TRUE)) {
            if (copy ($this->varContainer, $newCopyName)) {
               return $this->returnToChain ();
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (COPY_OPERATION_FAILED),
                    new S (COPY_OPERATION_FAILED_FIX));
                } else {
                    return $this->pathExists;
                }
            }
        } else {
            return $this->pathExists;
        }
    }

    /**
     * Set the stored path, to this (non-)existing path;
     *
     * This method will set the stored path to the the one given by the passed parameter. We can pass a non-existent path, while
     * avoiding error mechanims, but that's a risky thing to do.
     *
     * @return object The current object instance;
     * @param string $passedArgument Expected path;
     * @param boolean $outputErrorOnMissingFile Should we echo an error screen;
     */
    public function & setPath ($passedArgument, $outputErrorOnMissingFile = TRUE) {
        if ($passedArgument instanceof S) {
            $this->varContainer = DOCUMENT_ROOT . $passedArgument->toString ();
        } else if (is_string ($passedArgument)) {
            $this->varContainer = DOCUMENT_ROOT . $passedArgument;
        } else {
            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (VAR_NOT_SET),
                new S (VAR_NOT_SET_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . 'File does not exist!');
            }
        }
        # Run to see if path exits;
        $this->checkPathExists ($outputErrorOnMissingFile);
        # Method-chain;
        return $this->returnToChain ();
    }


	/**
	 * Will set the given string contents to the file, using the big PHP function "file_put_contents" (the same as file_get_*) in
	 * order to make code a little bit easier on the client side. We're actually eager to have this kind of code as we can easily
	 * maintain it and debug it per-se;
	 *
	 * @return object The current oject instance;
	 * @param string $stringToWrite The passed string to write to file;
	 */
    public function & putToFile ($stringToWrite) {
    	if (file_put_contents ($this->varContainer, $stringToWrite)) {
    		return $this->returnToChain ();
    	} else {
    	   if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CANNOT_WRITE_FILE),
                new S (CANNOT_WRITE_FILE_FIX));
            } else {
                throw new Exception (__CLASS__ . _DCSP . 'File does not exist!');
            }
    	}
    }
}

    /**
     * File Directory: DataType, containing the contents of a directory, that can be extended from the SPL;
     *
     * This class is an interface to the DirectoryIterator used in the SPL. It's an easy way for us to do intelligent things,
     * with little code. For the moment, it relies upon it's own methods, but it's going to evolve as time and money allows us
     * to invest time in it;
     *
     * @package RA::DataTypes
     */
class FileDirectory extends FilePath {
    public function __construct ($passedArgument, $outputErrorOnMissingFile = FALSE) {
        $this->setPath ($passedArgument, $outputErrorOnMissingFile);
    }

    public function & toString ($returnObjectOrString = FALSE) {
        switch ($returnObjectOrString == TRUE) {
            case TRUE:
                # Return to chain ...
                return $this->returnToChain ();
            break;
            case FALSE:
                # Return the parrent toString;
                return parent::toString ();
            break;
        }
    }

    public function & checkPathExists ($outputErrorOnMissingFile = TRUE) {
       if ($this->pathExists == NULL) {
            # Just return the true/false;
            $this->pathExists = new B (is_dir ($this->varContainer));
            if (($this->pathExists->toBoolean () == FALSE) && ($outputErrorOnMissingFile == TRUE)) {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (FILE_DOESNT_EXIST),
                    new S (FILE_DOESNT_EXIST_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . 'File does not exist!');
                }
            } else {
                # Return the current pathExists;
                return $this->pathExists;
            }
        } else {
            # Return the current pathExists;
            return $this->pathExists;
        }
    }

    /**
     * Scan the directory, and return its contents;
     *
     * This method will return an A (Array) containing all the files found in this directory. It allows us to get an instant view
     * of the contents of the directory we need to process.
     *
     * @return array The array containing files found in the current directory;
     * @param integer $setCountTo Expecting an integer we can modify with the count of all found files;
     * @param integer $sortByWhat What kind of sorting should be done (see PHP sort ());
     */
    public function scanDirectory (& $setCountTo = NULL, $sortByWhat = SORT_STRING) {
        $temporaryScandirArray = array_reverse (scandir ($this->varContainer, $sortByWhat), FALSE);
        $temporaryCount = count ($temporaryScandirArray);
        $scanArrayFiltered = array ();
        $setCountTo = 0;
        for ($i = 0; $i < $temporaryCount; ++$i) {
            if ($temporaryScandirArray[$i][0] != '.') {
                $scanArrayFiltered[] = $temporaryScandirArray[$i];
                $setCountTo++;
            }
        }
        return new A ($scanArrayFiltered);
    }
}

    /**
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     * @access private
     */
class FileContent extends FilePath {
    private $pathContentAs = NULL;
    # On construction, assign variable;
    public function & __construct ($passedArgument, $outputErrorOnMissingFile = TRUE) {
        return $this->setPath ($passedArgument, $outputErrorOnMissingFile);
    }

    public function & setPath ($passedArgument, $outputErrorOnMissingFile = TRUE) {
        parent::setPath ($passedArgument, $outputErrorOnMissingFile);
        $this->checkPathExists ();
        if ($this->pathExists->toBoolean () == TRUE) {
            # Set the variableContainer to the file content;
            $this->varContainer = file_get_contents ($this->varContainer);
            $this->pathContentsAs = 1;
        }
        // Return to chain;
        return $this->returnToChain ();
    }
}

    /**
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     * @access private
     */
class Nothing extends M {
    // Define the nothing ...
    const THE_NOTHING_STUFF = NULL;

    // SET ...
    public function __SET ($objKey, $objVar) {
        // Do nothing ...
        return self::THE_NOTHING_STUFF;
    }

    // GET ...
    public function __GET ($objKey) {
        // Do nothing ...
        return self::THE_NOTHING_STUFF;
    }

    // ISSET ...
    public function __ISSET ($objKey) {
        // Do nothing ...
        return self::THE_NOTHING_STUFF;
    }

    // UNSET ...
    public function __UNSET ($objKey) {
        // Do nothing ...
        return TRUE;
    }

    // CALL ...
    public function __CALL ($objFunc, $objArgs) {
        // Do nothing ...
        return self::THE_NOTHING_STUFF;
    }

    // CALL Static ...
    public function __CALLSTATIC ($objFunc, $objArgs) {
        // Do nothing ...
        return self::THE_NOTHING_STUFF;
    }

    // STRING ...
    public function __toString () {
        // Do nothing ...
        return (string) self::THE_NOTHING_STUFF;
    }

    /* WHY: Cause we need a CLASS that does absolutelly nothing, just because many of our projects use or don't use specific
     * modules and treating each case with an "IF ..." is annoying. That's why in the abstract class for the modules we just set
     * that if DEBUG_NON_MODULES == 1 we error, else we return a 'Nothing' class ... that does ... nothing. This way, we can write
     * non-redundant code easily, making functions that don't do a thing. Usually, returning FALSE is good ...
     */
}
?>
