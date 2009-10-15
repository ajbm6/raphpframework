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

############# Motto: 'We're used to say: Think FAST!';
/**
 * Abstract CLASS providing basic benchmarking and execution methods. This CLASS will implement the IFaceEXE interface of methods that
 * you can use to either benchmark your code or to provide some shortcuts to PHP functions that affect the execution behaviour.
 *
 * @package RA-Benchmark-And-Execution
 * @category RA-Abstract-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class EXE extends A implements IFaceEXE {
    /**
     * @var $objIdentificationString The object unique (__CLASS__) identification string.
     * @staticvar $objName An internal identifier to the object name, usually __CLASS__ :: RA PHP Framework;
     * @staticvar $objExecutionTime An array consisting of key/var pairs, set by setExeTime;
     * @staticvar $objErrorCallback Silently NULL, but used forward to see if the ERR class was loaded ...
    */
    protected static $objName                   = 'EXE :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    protected static $objExecutionTime          = NULL;

    /**
     * Sets a time identifier, used to benchmark code between to executions of code. The time difference between two time identifiers
	 * can be calculated using the getExeTime () method by passing the needed string identifiers to calculate the difference from.
	 * Internally, the time is stored as a PHP: microtime (TRUE) which returns a 'float' type integer. The method is used in the error
	 * handling mechanism to allow for a difference of time between the script start time and the script end time.
	 * <code>
	 * <?php
	 *		// Set some time identifiers;
	 * 		TPL::setExeTime ($objA = new S ('TimeSlice_A'));
	 *		TPL::setExeTime ($objB = new S ('TimeSlice_B'));
	 *		TPL::setExeTime ($objC = new S ('TimeSlice_C'));
	 *
	 *		// Now, get the difference and echo;
	 *		echo TPL::getExeTime ($objC, $objB);
	 *		echo TPL::getExeTime ($objB, $objA);
	 * ?>
	 * </code>
     *
     * @param S $timeSlice A timestamp identifier
     * @return B Was the timestamp set or not
     * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @see EXE::getExeTime()
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
     * @access public
	 * @static
     * @final
    */
    public static final function setExeTime (S $timeSlice) {
        // Set the current timeSlice, from microtime;
        if (self::$objExecutionTime[$timeSlice] = microtime (TRUE)) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Calculates the time difference between to given microcode (TRUE) timestamps. The timestamps are the string identifiers that have
	 * been previously set by the setExeTime method. The return of this function is a native PHP 'float' (aka double) for the moment,
	 * due to a little bug in outputting native RA DataType in our error handling mechanism;
	 * <code>
	 * <?php
	 *		// Get the time difference, as float ...
	 *		echo TPL::getExeTime ($objTimeSliceA, $objTimeSliceB);
	 * ?>
	 * </code>
     *
     * @param S $timeSliceA First time slice
     * @param S $timeSliceB Second time slice
     * @return F Difference between B - A
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see EXE::setExeTime()
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function getExeTime (S $timeSliceA, S $timeSliceB) {
        // Checks if the requested times have been set;
        if (isset (self::$objExecutionTime[$timeSliceA]) &&
            isset (self::$objExecutionTime[$timeSliceB])) {
			// Return the difference between the timestamps ...
            return new F (self::$objExecutionTime[$timeSliceB] - self::$objExecutionTime[$timeSliceA]);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Returns the string representation of the current object, be it parent or child. From any object that is a child of the current
	 * class, we have the advantaje that if we want to serialize the object and send it by wire (through http, smtp, etc.) to another
	 * place where it should keep its properties, this method automagically those this for us. Thus, we can use our objects, no matter
	 * what the contain as serialized strings that can be than used in _GET, emailed, saved to a file and more ...
	 * <code>
	 * <?php
	 *		// For ex. we ECHO the object in a buffer ...
	 *		echo $this; # Will serialize the string, using the __toString () method;
	 * ?>
	 * </code>
     *
     * @return S Object that has been serialized and URL-encoded
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see S::__toString()
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @final
    */
    public final function __toString () {
        // Do return ...
        return new S (urlencode (serialize ($this)));
    }

    /**
     * Returns the object, after unserializing and decoding it. Usually, a class will receive the serialized string by one of the
	 * wire protocols you've sent it through. When that happens you will certainly use this function to first decode the URL encoded
	 * string, after which, to unserialize the string and return the proper object. We place that object in an 'O' object which you
	 * can than retrieve it by ->toMix ();
	 * <code>
	 * <?php
	 * 		// Get a string from a source ...
	 *		$objUnSerialized = $this->__toObject ($objSerializedString)->toMix ()
	 * ?>
	 * </code>
     *
     * @param S $serializedString Passed serialized and encoded string to be made an object
     * @return O Contained unserialized object
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @see O::toMix()
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @final
    */
    protected final function __toObject (S $serializedString) {
        // Do return ...
        return new O (unserialize (urldecode ($serializedString)));
    }

    /**
     * Returns the CLASS name of the given object. This can be used in situations where you want the CLASS name of a given object, and
	 * in case you don't give it a parameter, the method will return the CLASS name of the current (this) object. The returned value
	 * is an S containing the name of the class the object was instantiated from;
	 * <code>
	 * <?php
	 *		// Get the CLASS
	 *		echo $this->getObjectCLASS (new S); # Will return S;
	 *		echo $this->getObjectCLASS (); # Will return EXE;
	 * ?>
	 * </code>
     *
     * @param M $checkedObject The object to determine its CLASS
     * @return S The name of the CLASS the object is an instance of
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/get_class
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @final
    */
    public final function getObjectCLASS (M $checkedObject = NULL) {
        if ($checkedObject == NULL) {
            // Get the current CLASS;
            return new S (get_class ($this));
        } else {
            // Do return the name of the object CLASS;
            return new S (get_class ($checkedObject));
        }
    }

    /**
     * Checks to see if whether a CLASS has been defined or not. We usually use this method to pre-check if a CLASS has been defined. As
	 * an example, this method is used in our DataTypes to check that the ERR (error) CLASS has been defined so we can use the proper
	 * method: renderScreenOfDeath - to output an error screen in case that the reporting DataType returns an error;
	 * <code>
	 * <?php
	 * 		// Execute the if;
	 *		if (EXE::checkClassExistence (new S ('EXE'))) {
	 *			// cause we're here, do something;
	 *		} else {
	 *			// cause we're here, do something else;
	 *		}
	 * ?>
	 * </code>
     *
     * @param S $classNameString The checked CLASS to see if it exists
     * @return B Will return true if the class exists
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function checkClassExistence (S $classNameString) {
        // Just check the CLASS existence, nothing more;
        return new B (class_exists ($classNameString, FALSE));
    }

    /**
     * Will return an unique code, based on a combination of sha1 and uniqid. First of ALL: do not relly on this method to return
	 * the same hash code again and again, as for a combination of sha1 (2^64) it will never return the same code twice upon a large
	 * field of possible situations. This method is rarelly used but has been promoted as a CORE method to generate a hased unique code
	 * for different purposes (weak encryption, random things, etc.);
	 * <code>
	 * <?php
	 *		// Get an unique code
	 *		echo EXE::getUniqueCode (); # Will return a sha1 (uniqid ());
	 * ?>
	 * </code>
     *
     * @param I $objLength The length of the string to get
     * @return S The code, stripped down to the desired length
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 2.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function getUniqueCode (I $objLength = NULL) {
        // Get the code, from an md5, of uniqid;
        $objCode = new S (sha1 (uniqid (rand (), TRUE)));

        // Either get it FULL, or just first N chars;
        if ($objLength == NULL) {
            // Return the code;
            return $objCode;
        } else {
            // Check something ...
            if ($objLength->toInt () > $objCode->toLength ()->toInt ()) {
                // Return the code;
                return $objCode;
            } else {
                // Trim it down to size ...
                return $objCode->doSubStr (0, $objLength->toInt());
            }
        }
    }

    /**
     * Will return an unique hash, based on the newly available hash_* functions in PHP, imported from PECL. It's a way to have
     * a secure mechanism for defining clear, unique hashing algorithms. For example, we recommend and soon started using the SHA-2
     * hashing mechanism for our own unique hashes. If the hash algorithm wasn't defined in PHP, the script will output a screen of
	 * death, mainly due to the fact that hashing, as a concept is quite important for encryption, uniqueness and content checks;
	 * <code>
	 * <?php
	 * 		// Return an unique HASH;
	 *		echo EXE::getUniqueHash (new S ('sha256'), new S ('Data as a string'));
	 * ?>
	 * </code>
     *
     * @param S $objHashAlgo The hash algorithm
     * @param S $objStringData The data to process
     * @return S The hash, as a string that can be further processed
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 3.0
	 * @access public
	 * @static
	 * @final
     */
    public static final function getUniqueHash (S $objHashAlgo, S $objStringData) {
        // Check to see if it's available ...
        if (_A (hash_algos ())->inArray ($objHashAlgo)) {
            // Do return ...
            return new S (hash ($objHashAlgo, $objStringData));
        } else {
			// Error!
			self::renderScreenOfDeath (new S (__CLASS__),
			new S (FATAL_ERROR),
			new S (FATAL_ERROR_CHECK_LOG),
			new S (HASH_ALGORITHM_DOES_NOT_EXIST));
		}
    }

    /**
     * Checks to see if whether a given method string is_callable, else outputs an error. In certain cases we are actually needed to
	 * check that a method is callable before we actually even try to call it. In the case of our framework, due to interfaces taht
	 * allow us to define a rigid architecture, this is going to rarelly be a case, but in the context of objects that are serialized
	 * back and forward and retrieved from different sources, such a check my be adequate;
	 * <code>
	 * <?php
	 * 		// Check a method ...
	 *		if (EXE::checkMethodIsCallable (new S ('getExeTime'))) {
	 *			// cause we're here, do something;
	 *		}
	 * ?>
	 * </code>
     *
     * @param S $methodName The name of the method to check for callability
     * @return B Will return true if method is callable
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function checkMethodIsCallable (S $methodName) {
        // Check if the method is callable;
        if (is_callable ($methodName->toString ())) {
            // Do return ...
            return new B (TRUE);
        } else {
			// Make an error ...
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (FATAL_ERROR),
            new S (FATAL_ERROR_CHECK_LOG),
            new S (METHOD_IS_NOT_CALLABLE));
        }
    }

    /**
     * Set here to have the 'ChainOfCommand' pattern function on current framework objects/modules ...This method, is a stand-in
	 * function that will have it's primary use in modules, where you can issue a ChainOfCommand on all registered modules, and expect
	 * them to work on that specific command. It's put here for backward commands, in the sense that if we want one of the framework
	 * object to catch a command, we'll just implement this method further down the inheritance tree;
	 * <code>
	 * <?php
	 *		// Make all commands EXECUTE;
	 *		$objSomething->executeCommand (new S ('doMAIL'), 
	 *		new A ('to' => 'somewhere@domain.tld'));
	 * ?>
	 * </code>
     *
     * @param S $objCommandName The name of the command the objects should execute
     * @param A $objPassedParameters What parameters we pass to the command
     * @return B Will return true until re-implemented, just so it can do something
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 02_EXE.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
    */
    public function executeCommand (S $objCommandName, A $objPassedParameters = NULL) {
        // Stop HERE, because we don't want command to be executed;
        return new B (TRUE);
    }
}
?>
