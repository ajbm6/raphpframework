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

/**
 * ------------------------------------------------------------------------------------------------------------------------
 * @class TheFactoryMethodOfSingleton
 * @description Used to maintain just ONE working copy of the base RA PHP Framework classes;
 * @return object Instance of the requested object;
 * ------------------------------------------------------------------------------------------------------------------------
*/
class TheFactoryMethodOfSingleton {
    private static $objInstanceArray = array ();

    public static function getInstance ($theCLSName) {
    	if (checkIfItsACSSFile () or checkIfItsAJSSFile ()) {
    		switch ($theCLSName) {
    			case 'ERR':
		    		if (isset (self::$objInstanceArray[$theCLSName])) {
		                # Return the saved instance;
		                return self::$objInstanceArray[$theCLSName];
		            } else {
		                # Return the first instance;
		                return self::$objInstanceArray[$theCLSName] = new $theCLSName;
		            }
	            break;
	            case 'TPL':
                    if (isset (self::$objInstanceArray[$theCLSName])) {
                        # Return the saved instance;
                        return self::$objInstanceArray[$theCLSName];
                    } else {
                        # Return the first instance;
                        return self::$objInstanceArray[$theCLSName] = new $theCLSName;
                    }
                break;
	            default:
	            	return NULL;
            	break;
    		}
    	} else {
	        if (isset (self::$objInstanceArray[$theCLSName])) {
	            # Return the saved instance;
	            return self::$objInstanceArray[$theCLSName];
	        } else {
	        	# Return the first instance;
	            return self::$objInstanceArray[$theCLSName] = new $theCLSName;
	        }
    	}
    }

    public static function checkHasInstance ($theCLSName) {
        return isset (self::$objInstanceArray[$theCLSName]);
    }
}

/**
 * ------------------------------------------------------------------------------------------------------------------------
 * @class OD: Object Delegator
 * @description Used to a common interface to a collection of objects.
 * @return object Instance of the requested object;
 * ------------------------------------------------------------------------------------------------------------------------
*/
class ObjectMethodDelegator {
    protected static $objRegisteredExecutors    = NULL;
    const ARG_NOT_OBJECT                        = 'Argument passed is not an object!';
    const MTH_CALL_NOTOK                        = 'Cannot CALL method!';
    const OBJ_IS_MISSING                        = 'Cannot find object class!';

    # Have a method that we can appendObjects with;
    public static function registerObject ($passedObjectParam) {
    	if (self::$objRegisteredExecutors == NULL) {
    		self::$objRegisteredExecutors = new A;
    	}
    	if ($passedObjectParam != NULL) {
	        if (is_object ($passedObjectParam)) {
	            self::$objRegisteredExecutors[] = $passedObjectParam;
	        } else {
	            if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
	                self::renderScreenOfDeath (new S (__CLASS__),
	                new S (VAR_NOT_OBJECT),
	                new S (VAR_NOT_OBJECT_FIX));
	            } else {
	                throw new Exception (__CLASS__ . _DCSP . self::ARG_NOT_OBJECT);
	            }
	        }
    	}
    }

    public function __CALL ($passedMethodString, $passedMethodArguments) {
        $methodCallerStatus = _NONE;
        foreach (self::$objRegisteredExecutors as $k => $v) {
            $objectClass = get_class ($v);
            if (is_array ($objectClassMethods = get_class_methods ($objectClass))) {
                if (in_array ($passedMethodString, $objectClassMethods)) {
                    # Just return everything;
                    return call_user_func_array (array ($v, $passedMethodString), $passedMethodArguments);
                } else {
                    # Set the methodCallerStatus;
                    $methodCallerStatus = 'dialed_method_not_found';
                }
            } else {
                # Set the methodCallerStatus;
                $methodCallerStatus = 'dialed_class_not_found';
            }
        }
        // Do some erroring. CANNOT do it in foreach, because of weird precision bug;
        if (($methodCallerStatus != _NONE) && ($methodCallerStatus == 'dialed_method_not_found')) {
            throw new Exception (__CLASS__ . _DCSP . self::MTH_CALL_NOTOK);
        } else if (($methodCallerStatus != _NONE) && ($methodCallerStatus == 'dialed_class_not_found')) {
        	throw new Exception (__CLASS__ . _DCSP . self::OBJ_IS_MISSING);
        }
    }
}

class ChainOfCommand {
	public static $objRegisteredExecutors    = NULL;

    // Have a method that we can appendObjects with;
    public static function registerExecutor ($passedObjectParam) {
        if (self::$objRegisteredExecutors == NULL) {
            self::$objRegisteredExecutors = new A;
        }
        if ($passedObjectParam != NULL) {
            if (is_object ($passedObjectParam)) {
                self::$objRegisteredExecutors[] = $passedObjectParam;
            } else {
                if (self::checkCanOutputErrorScreen ()->toBoolean ()) {
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (VAR_NOT_OBJECT),
                    new S (VAR_NOT_OBJECT_FIX));
                } else {
                    throw new Exception (__CLASS__ . _DCSP . self::ARG_NOT_OBJECT);
                }
            }
        }
    }

    /**
     * Will register the current object to the notifyCommand object LIST, and executes the command chain;
     *
     * This method, notifyCommand, will try to determine if the current object was registered. If it was not registered in the
     * LIST, it will try to register it, after which it will execute the chain of command. Calling 'notifyCommand' from
     * any object will cause a downward spiral to the first 'executeCommand' method, as long as the chain isn't interrupted!
     *
     * @param string $objCommandName Passed command name to be executed;
     * @param array $objPassedParameters Parameters to pass to the given command;
     * @return mixed Will not return a thing, but execute the command;
    */
    public static final function notifyCommand (S $objCommandName, A $objPassedParameters = NULL) {
        # Execute the command;
        foreach (self::$objRegisteredExecutors as $k => $v) {
            # Foreach registered object, execute current command;
            $v->executeCommand ($objCommandName, $objPassedParameters);
        }
    }
}
?>
