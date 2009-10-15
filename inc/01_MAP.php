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
############# DataType (DT) - PHP Functions Mapping - Because PHP doesn't support function overloading!;
    function _S ($objParam) {
        return new S ($objParam);
    }

    function & _QS ($objParam) {
        # Set some static variables ...
        static $objDoSELECTFromTable;
        $objDoSELECTFromTable = new S
        ('SELECT %what FROM %table %condition');

        static $objDoDELETEFromTable;
        $objDoDELETEFromTable = new S
        ('DELETE FROM %table WHERE %condition');

        static $objDoINSERTOnToTable;
        $objDoINSERTOnToTable = new S
        ('INSERT INTO %table SET %condition');

        static $objDoUPDATEOnToTable;
        $objDoUPDATEOnToTable = new S
        ('UPDATE %table SET %condition');

        # Do the switch ...
        switch ($objParam) {
            case 'doSELECT':
                return $objDoSELECTFromTable;
                break;

            case 'doDELETE':
                return $objDoDELETEFromTable;
                break;

            case 'doINSERT':
                return $objDoINSERTOnToTable;
                break;

            case 'doUPDATE':
                return $objDoUPDATEOnToTable;
                break;
        }
    }

    # Do a switch, determine what PHP function was called!
    function S (& $mappedObject, & $nameOfHook,
    & $nameOfFunction, & $argumentsOfHook) {
	    switch ($nameOfHook) {
	    	# Mapping's done here ...
	    	case 'escapeCString':
	    	case 'escapeString':
	    	case 'toHex':
	    	case 'toChunk':
	    	case 'encryptIt':
	    	case 'chrToASCII':
	    	case 'convertCYR':
	    	case 'uDecode':
	    	case 'uEncode':
	    	case 'countChar':
	    	case 'toCRC32':
	    	case 'toHebrew':
	    	case 'toNLHebrew':
	    	case 'entityDecode':
	    	case 'entityEncode':
	    	case 'charDecode':
	    	case 'charEncode':
	    	case 'trimLeft':
	    	case 'trimRight':
	    	case 'toMD5File':
	    	case 'toMD5':
	    	case 'toMetaphoneKey':
	    	case 'toMoneyFormat':
	    	case 'nL2BR':
	    	case 'ordToASCII':
	    	case 'qpDecode':
	    	case 'qpEncode':
	    	case 'toSHA1File':
	    	case 'toSHA1':
	    	case 'toSoundEx':
	    	case 'doCSV':
	    	case 'replaceIToken':
	    	case 'doPad':
	    	case 'doRepeat':
	    	case 'doShuffle':
	    	case 'toROT13':
	    	case 'doSplit':
	    	case 'toWordCount':
	    	case 'compareCaseTo':
	    	case 'compareNCaseTo':
	    	case 'compareTo':
	    	case 'compareNTo':
	    	case 'stripTags':
	    	case 'removeCStr':
	    	case 'removeStr':
	    	case 'findIPos':
	    	case 'findPos':
	    	case 'findILPos':
	    	case 'findLPos':
	    	case 'findIFirst':
	    	case 'findFirst':
	    	case 'findLast':
	    	case 'doReverse':
	    	case 'toLength':
	    	case 'natCaseCmp':
	    	case 'natCmp':
	    	case 'charSearch':
	    	case 'doTokenize':
	    	case 'toLower':
	    	case 'toUpper':
	    	case 'doTranslate':
	    	case 'doSubStr':
	    	case 'doSubCompare':
	    	case 'doSubCount':
	    	case 'doSubReplace':
	    	case 'doWrap':
	    	case 'doBZCompress':
	    	case 'doBZDecompress':
	    	case 'doBZOpen':
	    	case 'doLZFCompress':
	    	case 'doLZFDecompress':
	    	case 'changeDirectory':
	    	case 'scanDirectory':
	    	case 'getCWorkingDir':
	    	case 'stripSlashes':
	    	case 'fileGetContents':
	    	case 'filePutContents':
	    		array_unshift ($argumentsOfHook, $mappedObject);
	    	break;
	    	case 'fromStringToArray':
	    		if (isset ($argumentsOfHook[1])) {
	    			# Push the explode, third parameter up one;
	    			$argumentsOfHook[2] = $argumentsOfHook[1];
	    		}
	    		# Make the second parameter, this object;
	    		$argumentsOfHook[1] = $mappedObject;
	    	break;
	    	case 'fromArray':
    		break;
	    	case 'eregReplace':
	    	    $argumentsOfHook[2] = $mappedObject;
    	    break;
	        # ERROR:
	        default:
	            # Get the __CLASS__ as get_class ($this);
	            throw new Exception (METHOD_NAME_NOT_MAPPED);
	        break;
	    }

	    switch ($nameOfHook) {
	        case 'entityEncode':
	        case 'entityDecode':
	            $argumentsOfHook[] = 'UTF-8';
	            break;
	    }

	    # Return, based on TYPEs;
	    switch (gettype ($savedObjRETURN = call_user_func_array ($nameOfFunction, $argumentsOfHook))) {
	        default:
	        case 'string':
	            return $mappedObject->setString ($savedObjRETURN);
	            break;
	        case 'integer':
	            return new I ($savedObjRETURN);
	            break;
	        case 'double':
	            return new F ($savedObjRETURN);
	            break;
	        case 'array':
	            return new A ($savedObjRETURN);
	            break;
	        case 'boolean':
	            return new B ($savedObjRETURN);
	            break;
	        case 'resource':
	            return new R ($savedObjRETURN);
	            break;
	    }
    }

    function _A ($objParam) {
        return new A ($objParam);
    }

    # Do a switch, determine what PHP function was called!
    function A (& $mappedObject, & $nameOfHook, & $nameOfFunction, & $argumentsOfHook) {
        switch ($nameOfHook) {
            # Mapping's done here ...
            case 'changeKeyCase':
            case 'toChunk':
                array_unshift ($argumentsOfHook, $mappedObject->toArray ());
            break;
            case 'inArray':
                array_unshift ($argumentsOfHook, $mappedObject->toArray ());
                $argumentsOfHook = array_reverse ($argumentsOfHook);
            break;
            # ERROR:
            default:
                # Get the __CLASS__ as get_class ($this);
                throw new Exception (METHOD_NAME_NOT_MAPPED);
            break;
        }

        # Return, based on TYPEs;
        switch (gettype ($savedObjRETURN = call_user_func_array ($nameOfFunction, $argumentsOfHook))) {
            case 'array':
                return $mappedObject->setMix ($savedObjRETURN);
                break;
            case 'string':
                return new S ($savedObjRETURN);
                break;
            case 'integer':
                return new I ($savedObjRETURN);
                break;
            case 'double':
                return new F ($savedObjRETURN);
                break;
            case 'boolean':
                return new B ($savedObjRETURN);
                break;
            case 'resource':
                return new R ($savedObjRETURN);
                break;
        }
    }
?>
