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

############# Motto: 'A love theraphy is all that you need!';
/**
 * Abstract CLASS implemention basic _SESSION management methods. Although not widely used it's the CENTRAL place to gather such
 * methods in a more organized manner. (for ex. what if you wanted to imlement DB sessions?!);
 *
 * @package RA-Session-Management
 * @category RA-Abstract-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class SES extends HDR {
    protected static $objName                   = 'SES :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /**
     * Will open the session, making the _SESSION global variable empty, if it's been previously set. The method will open the session,
	 * but will also set the 'session_set_cookie_params' to the SESSION_COOKIE_LIFETIME set in the the config. If something goes wrong
	 * when opening the _SESSION, the script will halt;
     *
     * @return B Will return true if by chance, the session was opened
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/session_start
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function openSession () {
        // Second check of 'in_session' to see if it's set!
        if (!(self::checkSessionVar (new S ('in_session'), new O (TRUE))->toBoolean ())) {
            // Set a 10 year session;
            session_set_cookie_params (SESSION_COOKIE_LIFETIME);
            // And now, start it;
            if (session_start ()) {
                self::setSessionCacheExpire ();
                // Do return ...
                
				return new B (TRUE);
            } else {
                // Error me proudly;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CANNOT_START_SESSION),
                new S (CANNOT_START_SESSION_FIX));
            }
        } else {
            // If 'in_session' is set, then we've already started the session;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (SESSION_ALREADY_STARTED),
            new S (SESSION_ALREADY_STARTED_FIX));
        }
    }

    /**
     * Will close the currently active _SESSION, or echo an error if we try to call this function invalid. This method will try to close
	 * an already opened session. If we try to call this method when a session hasn't already been started we will receive and error
	 * notifying us that we need to fix our code.
     *
     * @return B Will return true if by chance, the session was closed
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function closeSession () {
        // Check to see if we have a valid $_SESSION to destroy;
        if (self::checkSessionVar (new S ('in_session'), new O (TRUE))->toBoolean ()) {
            if (self::unsetSessionVar (new S ('in_session'))->toBoolean ()) {
                if (session_destroy ()) {
                    $_SESSION = array ();
                    // Do return ...
                    return new B (TRUE);
                } else {
                    // Error me proudly;
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (SESSION_ALREADY_STOP),
                    new S (SESSION_ALREADY_STOP_FIX));
                }
            } else {
                // Error me proudly;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (SESSION_ALREADY_STOP),
                new S (SESSION_ALREADY_STOP_FIX));
            }
        } else {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (SESSION_ALREADY_STOP),
            new S (SESSION_ALREADY_STOP_FIX));
        }
    }

    /**
     * Will encode the _SESSION so it can be transmited across the wire. This function is the same as we would have serialized the
	 * stored _SESSION data and sent it over wire, but we use the PHP specific @session_encode function to do that for us in a more
	 * compatible way for the novice PHP developer;
     *
     * @return S Will return the encoded session as a string
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/session_encode
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function encodeSession () {
        // Do return ...
        return new S (session_encode ());
    }

    /**
     * Will accept a string as a parameter which has the _SESSION information encoded inside. After encoding the _SESSION and sending it
	 * over the wire, you'd need a decoding method for that information. We use the PHP specific @session_decode function to actually
	 * decode the _SESSION information for us (and put it back in the _SESSION) - to be as close to the native PHP as we can;
     *
     * @param S $sessionString Will decoded the passed session string
     * @return B Will return true if decoding was OK
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/session_decode
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function decodeSession (S $sessionString) {
        // Decode _SESSION, and return;
        if (session_decode ($sessionString->toString ())) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (COULD_NOT_DECODE_SESSION),
            new S (COULD_NOT_DECODE_SESSION_FIX));
        }
    }

    /**
     * Will set the time before the session_cache_expire will set session files as invalid. The PHP garbage collection mechanism will
	 * take the session_cache_expire time interval that you have set before declaring _SESSION files as invalid which will cause
	 * logged in users without a proper _SESSION file will be automatically logged out;
     *
     * @return B Will return true if the session cache expire was set OK
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/session_cache_expire
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setSessionCacheExpire () {
        if (SESSIONCACHEEXPIRE != _NONE) {
            // Set the _SESSION expire cache;
            if (session_cache_expire (SESSIONCACHEEXPIRE)) {
                // Do return ...
                return new B (TRUE);
            } else {
                // Error me proudly;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (CANNOT_MODIFY_SESSION_CACHE),
                new S (CANNOT_MODIFY_SESSION_CACHE_FIX));
            }
        } else {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (CANNOT_MODIFY_SESSION_CACHE),
            new S (CANNOT_MODIFY_SESSION_CACHE_FIX));
        }
    }

    /**
     * Will return the current session cache expire set by session_cache_expire PHP function. If you want to know what the session expire
	 * time will be before the PHP garbage collector is triggered. You could use this method to automatically adjust the cache expire
	 * depending on your specific project needs;
     *
     * @return I Will return the current session cache expire time, as number in seconds
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/session_cache_expire
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function getSessionCacheExpire () {
        // One-step conditional to get(the)SessionCacheExpire ();
        return new I (session_cache_expire ());
    }

    /**
     * Will check a session var to see if it's set. Same as isset (_SESSION) but a little bit complicated. Although we prefer to access
	 * the _SESSION through the superglobal, we provide this method to check for example that a session variable was set or not. You
	 * could use this if you want to keep code as OOP'ed as possible but it's a little overkill in comparison to the native PHP checks;
     *
     * @param S $theKey The key to be set in the _SESSION
     * @param M $theValue The value, any kind that can be serialized to be stored in _SESSION
     * @return B Will return true if the key/var pair are set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function checkSessionVar (S $theKey, O $theValue) {
        // Searching $_SESSION for theKey with theValue, and if the correspond;
        if (isset ($_SESSION[PROJECT_NAME . _U . $theKey->toString ()])) {
            if ($_SESSION[PROJECT_NAME . _U . $theKey
            ->toString ()] == $theValue->toMix ()) {
                // Do return ...
                return new B (TRUE);
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
     * Will set a given string _SESSION key to a given object value. Usually, you would do that by _SESSION['key'] = $var, but to keep
	 * it as OOP as possible, we provide such a method to set the session variable you want. For the purpose of developing this code
	 * in this manner we provide this session variable setting mechanism;
     *
     * @param S $theKey The key to be set in the _SESSION
     * @param M $theValue The value, any kind that can be serialized to be stored in _SESSION
	 * @return B Will return true if the key/var pair are set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function setSessionVar (S $theKey, O $theValue) {
        if ($_SESSION[PROJECT_NAME . _U . $theKey
            ->toString ()] = $theValue->toMix ()) {
            return new B (TRUE);
        } else {
            return new B (FALSE);
        }
    }

    /**
     * Will return a session key that has been previously set, or error if not. This is the same as getting the variable directly from
	 * the _SESSION superglobal, but as we provide an OOP way of handling the _SESSION variable, we provide such a method for getting
	 * stored data back from the _SESSION in a manner that is controllable;
     *
     * @param S $theKey The key to get
     * @return mixed Depends on what was actually set there
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
     */
    protected static final function getSessionVar (S $theKey) {
        if (isset ($_SESSION[PROJECT_NAME . _U . $theKey->toString ()])) {
            return $_SESSION[PROJECT_NAME . _U . $theKey->toString ()];
        } else {
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (VAR_NOT_SET),
            new S (VAR_NOT_SET_FIX));
        }
    }

    /**
     * Will modify an already set _SESSION variable. You need to first set the key to have this method work, else it will not modify
	 * the _SESSION variable. This is contrary to how the _SESSION superglobal works, but provides a method to be more precise when
	 * working with the _SESSION superglobal when actually adding/modifying variables inside it;
     *
     * @param S $theKey The key to be set in the _SESSION
     * @param M $theValue The value, any kind that can be serialized to be stored in _SESSION
     * @return B Will return true if the key/var pair are set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function modSessionVar (S $theKey, O $theValue) {
        if (isset ($_SESSION[PROJECT_NAME . _U . $theKey->toString ()])) {
            $_SESSION[PROJECT_NAME . _U . $theKey
            ->toString ()] = $theValue->toMix ();
            return new B (TRUE);
        } else {
            return new B (FALSE);
        }
    }

    /**
     * Will unset a _SESSION variable, checking that it has been set first. To actually unset a _SESSION variable you need to make sure
	 * that the variable was set at first, or this function would just execute and return FALSE. We provide it as an OOP way to manage
	 * the _SESSION superglobal in a way that gives the developer more control over the data stored in the _SESSION;
     *
     * @param S $theKey The key to be set in the _SESSION
     * @return B Will return true if the key/var pair are set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 06_SES.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
	 * @final
    */
    protected static final function unsetSessionVar (S $theKey) {
        if (isset ($_SESSION[PROJECT_NAME . _U . $theKey->toString ()])) {
            $_SESSION[PROJECT_NAME . _U . $theKey->toString ()] = _NONE;
            unset ($_SESSION[PROJECT_NAME . _U . $theKey->toString ()]);
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }
}
?>
