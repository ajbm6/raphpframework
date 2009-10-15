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

############# Motto: "Shakespeare: 'Cry Havoc! and let loose the dogs of WAR!";
/**
 * Concrete CLASS providing the basics of our SQL querying mechanism. We, for the moment provide just the MySQL driver, but could
 * easily adapt to any SQL mechanism, by simply remaking this CLASS or the $this->_Q querying mechanism;
 *
 * @package RA-Structured-Query-Language
 * @category RA-Concrete-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access public
 */
class SQL extends TPL {
    /**
     * @staticvar $objSQLH Database host;
     * @staticvar $objSQLU Database user;
     * @staticvar $objSQLP Datapase password;
     * @staticvar $objSQLD Database db;
     * @staticvar $objSQLR Database resource;
    */
    protected static $objName                   = 'SQL :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;
    private static $objSQLH                     = NULL;
    private static $objSQLU                     = NULL;
    private static $objSQLP                     = NULL;
    private static $objSQLD                     = NULL;
    private static $objSQLR                     = NULL;

	// CONSTRUCT;
	public function __construct (S $databaseIndex = NULL) {
		parent::__construct ();
		// Do the rest ...
		if (!TheFactoryMethodOfSingleton::checkHasInstance (__CLASS__)) {
			// Set execution time for SQLHandler;
			self::setExeTime (new S (__CLASS__));
            // Make a default databaseIndex ...
            switch ($databaseIndex == NULL) {
                case TRUE:
                    // Yes, we set the default ...
                    $databaseIndex = new S ('db_0');
                break;
            }
            # Set ALL object properties to some defaults ...
			self::$objSQLH = new S ($GLOBALS['H'][$databaseIndex->toString ()]);
			self::$objSQLU = new S ($GLOBALS['U'][$databaseIndex->toString ()]);
			self::$objSQLP = new S ($GLOBALS['P'][$databaseIndex->toString ()]);
			self::$objSQLD = new S ($GLOBALS['D'][$databaseIndex->toString ()]);
			self::$objSQLR = new S ($GLOBALS['R'][$databaseIndex->toString ()]);

			// Use a switch, to clean the code;
	        if (SQL_PERSISTENT_CONNECTION == 1) {
	            // Do return ...
                return (mysql_pconnect
                (self::$objSQLH, self::$objSQLU, self::$objSQLP) or self::renderSQLScreenOfDeath ()) &&
                (mysql_select_db (self::$objSQLD) or self::renderSQLScreenOfDeath ());
	        } else {
	            // Do return ...
                return (mysql_connect
                (self::$objSQLH, self::$objSQLU, self::$objSQLP) or self::renderSQLScreenOfDeath ()) &&
                (mysql_select_db (self::$objSQLD) or self::renderSQLScreenOfDeath ());
	        }
		} else {
			// Return the instantiated object;
			return TheFactoryMethodOfSingleton::getInstance (__CLASS__);
		}
    }

    /**
     * Will return a MySQL Resource, containing the result of the passed SQL. This method will query the database and return the
	 * MySQL resource which will get processed and transformed into a specifc RA array (A) that you can then access for information
	 * you actually need;
     *
     * @param S $queryString The query string to be queried
     * @param S $queryField The field name (unique) to index by
     * @return A The returned query resource
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
    */
    public static function getQuery (S $queryString, S $queryFieldId = NULL) {
        // Get the MySQL result, or go an echo an error screen if it failed ...
        $queryResource = new O (mysql_query ($queryString->doToken (SQL_PREFIX, self::$objSQLR)));
        // Do another checking for FALSE, and return what's needed ...
        if ($queryResource->checkIs ('res')->toBoolean ()) {
            $queryResourceSet = new R ($queryResource->toMix ());
            $arrayResourceSet = new A;
            if ($queryFieldId == NULL) {
                // Set the indexer to minus 1, so we can ++i;
                $i = -1;
                // Do the looping ...
                while ($r = self::getQueryResultRow ($queryResourceSet)) {
                    // MySQL: Numeric ...
                    $arrayResourceSet[++$i] = $r;
                }
                // Just do it;
                return $arrayResourceSet;
            } else {
                // Do the looping ...
                while ($r = self::getQueryResultRow ($queryResourceSet)) {
                    // MySQL: Associative ...
                    $arrayResourceSet[$r[$queryFieldId->toString ()]] = $r;
                }
                // Just return, we know what it is;
                return $arrayResourceSet;
            }
        } else if ($queryResource->checkIs ('bln')->toBoolean ()) {
            if ($queryResource->toMix () == TRUE) {
                // Return TRUE, is not a resource ...
                return new B (TRUE);
            } else {
                // Return FALSE, output error screen;
                self::renderSQLScreenOfDeath ();
            }
        }
    }

    /**
     * Will return an array containing field names of the given string table name. This method is used to find out what fields a table
	 * has so we can use that information for example in the development of our auto-forms. Other uses can also be found easily when
	 * knowing or not knowing the table structure;
     *
     * @param S $queryTable The table to query for fields
     * @return A An array containing the fields from the table
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
    */
    protected static function getFieldsFromTable (S $queryTable) {
        $ex = new R (mysql_list_fields (self::$objSQLD, $queryTable->doToken (SQL_PREFIX, self::$objSQLR)));
        $tableFieldColumns = new I (mysql_num_fields ($ex->toResource ()));
        for ($i = 0, $tableFieldArray = new A; $i < $tableFieldColumns->toInt (); ++$i) {
            $tableFieldArray[$i] = new S (mysql_field_name ($ex->toResource (), $i));
        }
        // We already know what it is, return it;
        return $tableFieldArray;
    }

    /**
     * Will return an integer, measuring the number of affected rows from the last update. This method is used if you want to be sure
	 * that an UPDATE or INSERT has been processed corectly or how many items have been modified. Rarelly do you need such checking
	 * but we provide the method anyway;
     *
     * @return I Will return the number of affected rows
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
    */
    protected static function getQueryAffectedRows () {
        // Just return the number of affected rows
        // Useful when you want to find out how many changed ...
        return new I (mysql_affected_rows ());
    }

    /**
     * Will return an array (associative by default), containing a row of the MySQL result. This method will provide access to the
	 * MySQL result returned as an array that can then be processed;
     *
     * @param R $queryResourceSet The resource set to be fetched
     * @param I $queryFetchMode One of the mysql_fetch_array () fetch modes, as an integer
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access private
	 * @static
    */
    private static function getQueryResultRow (R $queryResourceSet, I $queryFetchMode = NULL) {
        if ($queryFetchMode == NULL) {
            $queryFetchMode = new I (MYSQL_ASSOC);
            $temporaryArray = mysql_fetch_array ($queryResourceSet->toResource (), $queryFetchMode->toInt ());
        } else {
            // Just return, taking the queryFetchMode, from the passed second parameter. We trust the developer!;
            $temporaryArray = mysql_fetch_array ($queryResourceSet->toResource (), $queryFetchMode->toInt ());
        }
        if ($temporaryArray != FALSE) {
            foreach ($temporaryArray as $k => $v) {
                ($v != NULL)                            ?
                ($temporaryArray[$k] = new S ($v))      :
                ($temporaryArray[$k] = new S (_NONE));
            }
            // Return the new A, passed from mysql_fetch_array;
            return new A ($temporaryArray);
        } else {
            // DO NOT MOVE. Set here to stop looping ...
            return FALSE;
        }
    }

    /**
     * Will set a quick function for querying. It's a mapping to the getQuery method, providing a shortcut method, easy to write that
	 * is also short. Accepts the same parameters as getQuery and returns whatever it returns;
     *
     * @param S $objSQL The query string
     * @param S $objQueryField The query field to sort by
     * @return S The parsed query as a resource
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
     */
    protected function _Q (S $objSQL, S $objQueryField = NULL) {
        // Do return ...
        return $this->getQuery ($this->doModuleToken
        ($objSQL), $objQueryField);
    }

    /**
     * Will replace module tokens (also named table fields) that can be used independent of the table structure. This provides the
	 * basics of our ORM (Object Relationship Mapping) as we can easily separate table fields from the SQL string and have an
	 * independent way to query the database freely of the structure or the table/field names;
     *
     * @param S $objSQLParam The SQL string to be processed
     * @return S Will return the current SQL string with modified tokens
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
     */
    protected function doModuleTokens (A $objTokens, A $objReplac, S $objSQLParam) {
        // Modify tokens;
        foreach ($objTokens as $k => $v) {
            // Replace %[theToken] with the corresponding field in the table;
			$objSQLParam->setString (preg_replace ('/%\b' . $v . '\b/i',
			$objReplac[$k], $objSQLParam));
        }

        // Do return ...
        return $objSQLParam->doToken ($objTokens->toArray (),
        $objReplac->toArray ());
    }

    /**
     * Will output an error screen, same as the one rendered by the error handler, with some defaults. This method, named as so is
	 * used if an 'mysql'_error () happens so that we can output a specific SQL error, including the SQL error string, the query string
	 * and the resulting error message;
     *
     * @return void Will not return a thing, but echo an error screen
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 11_SQL.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access protected
	 * @static
    */
    protected static function renderSQLScreenOfDeath () {
        parent::renderScreenOfDeath (new S (__CLASS__),
        new S (FATAL_ERROR), new S (FATAL_ERROR_CHECK_LOG), new S (mysql_error ()));
    }
}
?>
