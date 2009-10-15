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

############# Motto: 'Omlet du fromage! Access denied ...';
class Authentication extends ICommonExtension implements IFaceAuthentication {
    /* OBJECT: Identity */
	protected static $objName                   = 'Authentication :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* MPTT for groups */
    protected static $objMPTT                   = NULL;
    protected static $objMPTTForZones           = NULL;

    /* TABLE: Users */
    public static $objAuthUsersTable            = NULL;
    public static $objAuthUsersTableFId         = NULL;
    public static $objAuthUsersTableFUName      = NULL;
    public static $objAuthUsersTableFUPass      = NULL;
    public static $objAuthUsersTableFEML        = NULL;
    public static $objAuthUsersTableFPhone      = NULL;
    public static $objAuthUsersTableFFName      = NULL;
    public static $objAuthUsersTableFLName      = NULL;
    public static $objAuthUsersTableFUGId       = NULL;
    public static $objAuthUsersTableFRegOn      = NULL;
    public static $objAuthUsersTableFLastLog    = NULL;
    public static $objAuthUsersTableFHash		= NULL;
    public static $objAuthUsersTableFActivated  = NULL;
    public static $objAuthUsersTableFCountry    = NULL;
    public static $objAuthUsersTableFSignature  = NULL;
    public static $objAuthUsersTableFDesc       = NULL;
    public static $objAuthUsersTableFYM         = NULL;
    public static $objAuthUsersTableFMSN        = NULL;
    public static $objAuthUsersTableFICQ        = NULL;
    public static $objAuthUsersTableFAOL        = NULL;
    public static $objAuthUsersTableFCity       = NULL;
    public static $objAuthUsersTableFAvatar     = NULL;
    public static $objAuthUsersTableFIp         = NULL;

    /* TABLE: Groups */
    public static $objAuthGroupTable            = NULL;
    public static $objAuthGroupTableFId         = NULL;
    public static $objAuthGroupTableFName       = NULL;
    public static $objAuthGroupTableFSEO        = NULL;

    /* TABLE: Zones */
    public static $objAuthZonesTable            = NULL;
    public static $objAuthZonesTableFId         = NULL;
    public static $objAuthZonesTableFName       = NULL;
    public static $objAuthZonesTableFDesc       = NULL;
    public static $objAuthZonesTableFPrice      = NULL;

    /* TABLE: Zone mappings */
    public static $objAuthZoneMTable            = NULL;
    public static $objAuthZoneMTableFId         = NULL;
    public static $objAuthZoneMTableFZId        = NULL;
    public static $objAuthZoneMTableFUGId       = NULL;
    public static $objAuthZoneMTableFIUG        = NULL;
    public static $objAuthZoneMTableFAorD       = NULL;
    public static $objAuthZoneMTableFErase      = NULL;

    /* TABLE: Configuration */
    public static $objAuthDefaultGroup          = NULL;
    public static $objAuthDefaultUsername       = NULL;
    public static $objAuthDefaultPassword       = NULL;

    /* REGEXPses */
    const REGEXP_PHP_CHECK_EMAIL				= '/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    const PHONE_IS_REQUIRED						= 0;

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent, parse the configuration while doing that;
        parent::__construct ();

        // Tie in common configuration data;
        $this->tieInCommonConfiguration ();

        // Set the proper configuration options for this object;
        self::$objAuthUsersTable                = $this->getConfigKey (new S ('authentication_users_table'));
        self::$objAuthUsersTableFId             = $this->getConfigKey (new S ('authentication_users_table_field_id'));
        self::$objAuthUsersTableFUName          = $this->getConfigKey (new S ('authentication_users_table_field_username'));
        self::$objAuthUsersTableFUPass          = $this->getConfigKey (new S ('authentication_users_table_field_password'));
        self::$objAuthUsersTableFEML            = $this->getConfigKey (new S ('authentication_users_table_field_email'));
        self::$objAuthUsersTableFPhone          = $this->getConfigKey (new S ('authentication_users_table_field_phone'));
        self::$objAuthUsersTableFFName          = $this->getConfigKey (new S ('authentication_users_table_field_first_name'));
        self::$objAuthUsersTableFLName          = $this->getConfigKey (new S ('authentication_users_table_field_last_name'));
        self::$objAuthUsersTableFUGId           = $this->getConfigKey (new S ('authentication_users_table_field_group_id'));
        self::$objAuthUsersTableFRegOn          = $this->getConfigKey (new S ('authentication_users_table_field_registered_on'));
        self::$objAuthUsersTableFLastLog        = $this->getConfigKey (new S ('authentication_users_table_field_visited_on'));
        self::$objAuthUsersTableFHash			= $this->getConfigKey (new S ('authentication_users_table_field_hash'));
        self::$objAuthUsersTableFActivated      = $this->getConfigKey (new S ('authentication_users_table_field_activated'));
        self::$objAuthUsersTableFCountry        = $this->getConfigKey (new S ('authentication_users_table_field_country'));
        self::$objAuthUsersTableFSignature      = $this->getConfigKey (new S ('authentication_users_table_field_signature'));
        self::$objAuthUsersTableFDesc           = $this->getConfigKey (new S ('authentication_users_table_field_description'));
        self::$objAuthUsersTableFYM             = $this->getConfigKey (new S ('authentication_users_table_field_ym'));
        self::$objAuthUsersTableFMSN            = $this->getConfigKey (new S ('authentication_users_table_field_msn'));
        self::$objAuthUsersTableFICQ            = $this->getConfigKey (new S ('authentication_users_table_field_icq'));
        self::$objAuthUsersTableFAOL            = $this->getConfigKey (new S ('authentication_users_table_field_aol'));
        self::$objAuthUsersTableFCity           = $this->getConfigKey (new S ('authentication_users_table_field_city'));
        self::$objAuthUsersTableFAvatar         = $this->getConfigKey (new S ('authentication_users_table_field_avatar'));
        self::$objAuthUsersTableFIp             = $this->getConfigKey (new S ('authentication_users_table_field_ip'));

        // Groups ...
        self::$objAuthGroupTable                = $this->getConfigKey (new S ('authentication_group_table'));
        self::$objAuthGroupTableFId             = $this->getConfigKey (new S ('authentication_group_table_field_id'));
        self::$objAuthGroupTableFName           = $this->getConfigKey (new S ('authentication_group_table_field_name'));
        self::$objAuthGroupTableFSEO            = $this->getConfigKey (new S ('authentication_group_table_field_seo'));

        // Zones ...
        self::$objAuthZonesTable                = $this->getConfigKey (new S ('authentication_zones_table'));
        self::$objAuthZonesTableFId             = $this->getConfigKey (new S ('authentication_zones_table_field_id'));
        self::$objAuthZonesTableFName           = $this->getConfigKey (new S ('authentication_zones_table_field_name'));
        self::$objAuthZonesTableFDesc           = $this->getConfigKey (new S ('authentication_zones_table_field_description'));
        self::$objAuthZonesTableFPrice          = $this->getConfigKey (new S ('authentication_zones_table_field_price'));

        // Zone mappings ...
        self::$objAuthZoneMTable                = $this->getConfigKey (new S ('authentication_gtozm_table'));
        self::$objAuthZoneMTableFId             = $this->getConfigKey (new S ('authentication_gtozm_table_field_id'));
        self::$objAuthZoneMTableFZId            = $this->getConfigKey (new S ('authentication_gtozm_table_field_zone_id'));
        self::$objAuthZoneMTableFUGId           = $this->getConfigKey (new S ('authentication_gtozm_table_field_ug_id'));
        self::$objAuthZoneMTableFIUG            = $this->getConfigKey (new S ('authentication_gtozm_table_field_is_group'));
        self::$objAuthZoneMTableFAorD           = $this->getConfigKey (new S ('authentication_gtozm_table_field_deny_or_allow'));
        self::$objAuthZoneMTableFErase          = $this->getConfigKey (new S ('authentication_gtozm_table_field_eraseable'));

        // Configuration ...
        self::$objAuthDefaultGroup              = $this->getConfigKey (new S ('authentication_default_group'));
        self::$objAuthDefaultUsername           = $this->getConfigKey (new S ('authentication_default_admin_username'));
        self::$objAuthDefaultPassword           = $this->getConfigKey (new S ('authentication_default_admin_password'));

        // DB: Auto-CREATE:
        $objQueryDB = new FileContent ($this->getPathToModule ()->toRelativePath () .
        _S . CFG_DIR . _S .  __CLASS__ . SCH_EXTENSION);

        // Make a FOREACH on each ...
        foreach (_S ($objQueryDB->toString ())
        ->fromStringToArray (RA_SCHEMA_HASH_TAG) as $k => $v) {
            // Make'em ...
            $this->_Q (_S ($v));
        }

        // Check non-modified user data, to prevent hackers;
        if ($this->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) { $this->checkIfUserDataIsOK (); }

        // Get an MPTT Object, build the ROOT, make sure the table is OK;
        self::$objMPTT = new MPTT (self::$objAuthGroupTable, self::$objAuthDefaultGroup);
        self::$objMPTTForZones = new MPTT (self::$objAuthZonesTable, $this->getObjectCLASS ());

        // Load'em defaults ... ATH, STG and others ...
        $this->STG = MOD::activateModule (new FilePath ('mod/settings'), new B (TRUE));

        // Check that the default administrator user exists, or create it;
        $this->setDefaultAdministratorSettings ();

        // Check to see if this zone exists, and if not, add it;
        if ($this->checkZoneByName (new S (__CLASS__))->toBoolean () == FALSE)
        $this->doMakeZone (new S (__CLASS__), self::$objAuthZonesTableFName);
        if ($this->checkAdministratorIsMappedToZone ($this->getObjectCLASS ())->toBoolean () == FALSE)
        $this->doMapAdministratorToZone ($this->getObjectCLASS ());

        // Do the tie, with myself, just for sub-zones;
        $this->tieInWithAuthentication ($this);
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
        $objTokens = new A (Array (
        'objAuthDefaultGroup',
        'objAuthDefaultUsername',
        'objAuthDefaultPassword',
        'objAuthUsersTable',
        'objAuthUsersTableFId',
        'objAuthUsersTableFUName',
        'objAuthUsersTableFUPass',
        'objAuthUsersTableFEML',
        'objAuthUsersTableFPhone',
        'objAuthUsersTableFFName',
        'objAuthUsersTableFLName',
        'objAuthUsersTableFUGId',
        'objAuthUsersTableFRegOn',
        'objAuthUsersTableFLastLog',
        'objAuthUsersTableFHash',
        'objAuthUsersTableFActivated',
        'objAuthGroupTable',
        'objAuthGroupTableFId',
        'objAuthGroupTableFName',
        'objAuthZonesTable',
        'objAuthZonesTableFId',
        'objAuthZonesTableFName',
        'objAuthZonesTableFDesc',
        'objAuthZonesTableFPrice',
        'objAuthZoneMTable',
        'objAuthZoneMTableFId',
        'objAuthZoneMTableFZId',
        'objAuthZoneMTableFUGId',
        'objAuthZoneMTableFIUG',
        'objAuthZoneMTableFAorD',
        'objAuthZoneMTableFErase',
        'objAuthUsersTableFCountry',
        'objAuthUsersTableFSignature',
        'objAuthUsersTableFDesc',
        'objAuthUsersTableFYM',
        'objAuthUsersTableFMSN',
        'objAuthUsersTableFICQ',
        'objAuthUsersTableFAOL',
        'objAuthUsersTableFCity',
        'objAuthUsersTableFAvatar',
        'objAuthUsersTableFIp',
        'objAuthGroupTableFSEO'));

        // Set the replacements;
        $objReplac = new A (Array (
        self::$objAuthDefaultGroup,
        self::$objAuthDefaultUsername,
        self::$objAuthDefaultPassword,
        self::$objAuthUsersTable,
        self::$objAuthUsersTableFId,
        self::$objAuthUsersTableFUName,
        self::$objAuthUsersTableFUPass,
        self::$objAuthUsersTableFEML,
        self::$objAuthUsersTableFPhone,
        self::$objAuthUsersTableFFName,
        self::$objAuthUsersTableFLName,
        self::$objAuthUsersTableFUGId,
        self::$objAuthUsersTableFRegOn,
        self::$objAuthUsersTableFLastLog,
        self::$objAuthUsersTableFHash,
        self::$objAuthUsersTableFActivated,
        self::$objAuthGroupTable,
        self::$objAuthGroupTableFId,
        self::$objAuthGroupTableFName,
        self::$objAuthZonesTable,
        self::$objAuthZonesTableFId,
        self::$objAuthZonesTableFName,
        self::$objAuthZonesTableFDesc,
        self::$objAuthZonesTableFPrice,
        self::$objAuthZoneMTable,
        self::$objAuthZoneMTableFId,
        self::$objAuthZoneMTableFZId,
        self::$objAuthZoneMTableFUGId,
        self::$objAuthZoneMTableFIUG,
        self::$objAuthZoneMTableFAorD,
        self::$objAuthZoneMTableFErase,
        self::$objAuthUsersTableFCountry,
        self::$objAuthUsersTableFSignature,
        self::$objAuthUsersTableFDesc,
        self::$objAuthUsersTableFYM,
        self::$objAuthUsersTableFMSN,
        self::$objAuthUsersTableFICQ,
        self::$objAuthUsersTableFAOL,
        self::$objAuthUsersTableFCity,
        self::$objAuthUsersTableFAvatar,
        self::$objAuthUsersTableFIp,
        self::$objAuthGroupTableFSEO));

        // Do a CALL to the parent;
        return parent::doModuleTokens ($objTokens, $objReplac, $objSQLParam);
    }

    /**
     * Will tie the current object with administration;
     *
     * This method will tie in the current object with the administration object. It will set the proper menu links and sublinks,
     * needed for the administration module, and will store the administration object for further use later. We do this just to
     * avoid adding subsequent PHP code for a manual tie later;
     *
     * @param IFaceAdministration $objAdminMechanism The administration object;
     * @return void Doesn't return anything, probably giving an error if it encounters one;
     */
    public function tieInWithAdministration (IFaceAdministration $objAdminMechanism) {
        // Do a parent::CALL, for some predefined defaults; Save the object after;
        parent::tieInWithAdministration ($objAdminMechanism);

        // Do the administration menu;
        $objWP = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
        $this->getConfigKey (new S ('authentication_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (AUTHENTICATION_MANAGE_USERS), $objWP,
        $this->getHELP (new S (AUTHENTICATION_MANAGE_USERS)));

        $objACL = new A;
        $objACL[] = new S ('Authentication.Users.Do.View');
        $objACL[] = new S ('Authentication.Groups.Do.View');
        $objACL[] = new S ('Authentication.Zones.Do.View');
        $objACL[] = new S ('Authentication.ACLsForGroups.Do.View');
        $objACL[] = new S ('Authentication.ACLsForUsers.Do.View');
        $objACL[] = new S ('Authentication.Do.Configuration');

        // ONLY: Authentication.Users.Do.View
        if ($this->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMU = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('authentication_file_manage_users')));
            self::$objAdministration->setSubMLink (new S (AUTHENTICATION_MANAGE_USERS),
            $objMU, $this->getHELP (new S (AUTHENTICATION_MANAGE_USERS)));
        }

        // ONLY: Authentication.Groups.Do.View
        if ($this->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMG = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('authentication_file_manage_groups')));
            self::$objAdministration->setSubMLink (new S (AUTHENTICATION_MANAGE_GROUPS),
            $objMG, $this->getHELP (new S (AUTHENTICATION_MANAGE_GROUPS)));
        }

        // ONLY: Authentication.Zones.Do.View
        if ($this->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMZ = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('authentication_file_manage_zones')));
            self::$objAdministration->setSubMLink (new S (AUTHENTICATION_MANAGE_ZONES),
            $objMZ, $this->getHELP (new S (AUTHENTICATION_MANAGE_ZONES)));
        }

        // ONLY: Authentication.ACLForGroups.Do.View
        if ($this->checkCurrentUserZoneACL ($objACL[3])->toBoolean () == TRUE) {
            $objMM = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('authentication_file_manage_zones_mapping')));
            self::$objAdministration->setSubMLink (new S (AUTHENTICATION_MANAGE_ZONES_MAPPING),
            $objMM, $this->getHELP (new S (AUTHENTICATION_MANAGE_ZONES_MAPPING)));
        }

        // ONLY: Authentication.ACLForUsers.Do.View
        if ($this->checkCurrentUserZoneACL ($objACL[4])->toBoolean () == TRUE) {
            $objMU = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('authentication_file_manage_zones_mapping_fusr')));
            self::$objAdministration->setSubMLink (new S (AUTHENTICATION_MANAGE_ZONES_MAPPING_FOR_USERS),
            $objMU, $this->getHELP (new S (AUTHENTICATION_MANAGE_ZONES_MAPPING)));
        }

        // ONLY: Authentication.Do.Configuration
        if ($this->checkCurrentUserZoneACL ($objACL[5])->toBoolean () == TRUE) {
            $objMC = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('authentication_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (AUTHENTICATION_MANAGE_CONFIGURATION),
            $objMC, $this->getHELP (new S (AUTHENTICATION_MANAGE_CONFIGURATION)));
        }

        // WIDGET: Statistics for users ...
        self::$objAdministration->setWidget ($this
        ->getHELP (new S ('adminStatistics'))
        ->doToken ('%u', $this->getUserCount ())
        ->doToken ('%g', $this->getGroupCount ())
        ->doToken ('%z', $this->getZoneCount ()));

        // WIDGET: Latest 10 users ... no status query ...
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
        // Do a CALL to the parent;
        parent::tieInWithAuthentication ($objAuthenticationMech);

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Authentication.Users.Do.View');
        $objACL[] = new S ('Authentication.Groups.Do.View');
        $objACL[] = new S ('Authentication.Zones.Do.View');
        $objACL[] = new S ('Authentication.ACLsForGroups.Do.View');
        $objACL[] = new S ('Authentication.ACLsForUsers.Do.View');
        $objACL[] = new S ('Authentication.Do.Configuration');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if ($this->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            $this->doMakeZone ($objACL[$k], $this->getObjectCLASS ());

            if ($this->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            $this->doMapAdministratorToZone ($objACL[$k]);
        }
    }

    /**
     * Will check if the passed user already exists in the database;
     *
     * This method will check if the passed user already exists in the database, thus avoiding errors in the system due to
     * double identical usernames. (as the code only expects one user to exist in the table of users). This method must be invoked
     * in any 'new user register' or user edit form.
     *
     * @param S $objUserName The user name to check for existence;
     * @return boolean Will return true if the current user exists;
     */
    public function checkUserNameExists (S $objUserName) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', self::$objAuthUsersTableFUName)->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFUName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objUserName))->doCount ()->toInt () != 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check if the curren email exists in the database;
     *
     * This method will check if the passed email already exists in the database, thus avoiding errors in the system due to
     * double identical emails. (as the code only expects one email to exist in the table of users). This method must be invoked
     * where e-mail check is needed;
     *
     * @param S $objUserMail The passed email to check for existence;
     * @return boolean Will return true if the current email exists;
     */
    public function checkUserMailExists (S $objUserMail) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', self::$objAuthUsersTableFEML)->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFEML = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objUserMail))->doCount ()->toInt () != 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check the current user in the databse table, by the given hash. The given hash can be anything, from SHA1 hash, to
     * MD5 and can contain any hash string - as long as the received hash string retains the same mecanism.
     *
     * @param S $objHash The has to activate the user of ...
     * @return boolean Will return OK if everything went OK ...
     */
    public function checkHashExists (S $objHASH) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFHash = "%hId" LIMIT 1'))
        ->doToken ('%hId', $objHASH))->doCount ()->toInt () != 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set the default administrator privileges;
     *
     * This method will get the required configuration options and set the default administrator permissions, if by chance the
     * tables are empty or something has become broken in the system. Usually, overwriting quickly is the best way to fix errors
     * that can be seen by users, errors that you can fix at a later time, from a backup;
     *
     * @return boolean Will return true if the new settings have been inserted;
     */
    public function setDefaultAdministratorSettings () {
        // Check that there are no previous users ...
        if ($this->getUserCount ()->toInt () == 0) {
            return $this->_Q (_QS ('doINSERT')
            ->doToken ('%table', self::$objAuthUsersTable)->doToken ('%condition', new S ('%objAuthUsersTableFUName = "%u",
            %objAuthUsersTableFUPass = "%p", %objAuthUsersTableFUGId = 1'))->doToken ('%u', self::$objAuthDefaultUsername)
            ->doToken ('%p', self::$objAuthDefaultPassword->encryptIt (sha1 (self::$objAuthDefaultPassword))));
        }
    }

    /**
     * Will check if the current visitor is a logged in user or not;
     *
     * This method will check the cookie object registered with this module to see if the current visitor is a registered user,
     * or a non-authenticated user, thus restricting access to some sensitive specific zones;
     *
     * @return boolean Will return true if the current users is logged in;
     */
    public function checkIfUserIsLoggedIn () {
        // Do return ...
        return new B ($this->objCookie->checkKey (self::$objAuthUsersTableFId)->toBoolean () == TRUE &&
        $this->objCookie->checkKey (self::$objAuthUsersTableFUName)->toBoolean () == TRUE &&
        $this->objCookie->checkKey (self::$objAuthUsersTableFUPass)->toBoolean () == TRUE);
    }

    /**
     * Will check to see that the users hasn't modified his cookie information to gain a higher access;
     *
     * This method will check that the data stored in the cookie on the users computer, if for example we do cookie authentication
     * hasn't been modified lately, meaning that the user can be a potential hacker trying to get a higher access by modifying
     * sensitive information. If something ain't coherent with the database, we automatically log the user out;
     *
     * @return boolean Will return true if the data is ok;
     */
    public function checkIfUserDataIsOK () {
        // Do return ...
        return new B ($this->objCookie->checkKey (self::$objAuthUsersTableFId)->toBoolean () == TRUE) &&
        $this->_Q (_QS ('doSELECT')->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFUName = "%uId" AND %objAuthUsersTableFUPass = "%pId"
        AND %objAuthUsersTableFActivated = "Y" LIMIT 1'))->doToken ('%uId', $this
        ->objCookie->getKey (self::$objAuthUsersTableFUName))
        ->doToken ('%pId', $this->objCookie->getKey (self::$objAuthUsersTableFUPass)))
        ->doCount ()->toInt () == 0 ? $this->doLogOut () : FALSE;
    }


    /**
     * Will authenticate the user, by passing the necessary user-name and password to it ...
     *
     * This method will set the necessary cookie information to authenticate the current user. The authentication is done, only
     * if the crypt'ed password and user name combination are found in the database query. If not, then it means that there is no
     * such user registered with our project, and thus, we don't allow the user to get authenticated, and get access to sensitive
     * zones in our project, those protected by our authentication mechanism;
     *
     * @param S $objUsername The passed user name;
     * @param S $objPassword The passed user password;
     * @param B $objRememberMe If true, the current information will be memorized beyond the closing of the browser window ...
     * @return boolean Will return true if the user has been authenticated with the system ...
     */
    public function doLogIn (S $objUsername, S $objPassword, B $objDoRememberMe = NULL) {
    	// Check if we're permanent or not ...
    	$objBt = $objDoRememberMe == NULL ? new B (FALSE) : $objDoRememberMe;

        // Make a query container, for the sake of the method-chain;
        $objSQLQuery = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFUName = "%uId" AND %objAuthUsersTableFUPass = "%pId"
        AND %objAuthUsersTableFActivated = "Y" LIMIT 1'))->doToken ('%uId', $objUsername)
        ->doToken ('%pId', $objPassword->encryptIt (sha1 ($objPassword))));

        // See if we can authenticate or not ...
        if ($objSQLQuery->doCount ()->toInt () > 0) {
        	// Set some requirements ...
	        $objId = $objSQLQuery->offsetGet (0)->offsetGet (self::$objAuthUsersTableFId);
	        $objUr = $objSQLQuery->offsetGet (0)->offsetGet (self::$objAuthUsersTableFUName);
	        $objPw = $objSQLQuery->offsetGet (0)->offsetGet (self::$objAuthUsersTableFUPass);

        	// Set required COOKIE/_SESSION params;
            $this->objCookie->setKey (self::$objAuthUsersTableFId, $objId, $objBt);
            $this->objCookie->setKey (self::$objAuthUsersTableFUName, $objUr, $objBt);
            $this->objCookie->setKey (self::$objAuthUsersTableFUPass, $objPw, $objBt);
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will clear the cookie information that has currently authenticated the user;
     *
     * This method will check the existence of a cookie for the current authenticated user. By default, it won't let any
     * action execute if the current user is not authenticated, but if he is, it will clear the cookie information on that user,
     * thus logging the user out from the system;
     *
     * @return boolean Will return true if it was able to log the user out ...
     */
    public function doLogOut () {
        // Check to see if there's something wrong ...
        if ($this->objCookie->checkKey (self::$objAuthUsersTableFId)->toBoolean () == TRUE) {
            $this->objCookie->unSetKey (self::$objAuthUsersTableFId);
            $this->objCookie->unSetKey (self::$objAuthUsersTableFUName);
            $this->objCookie->unSetKey (self::$objAuthUsersTableFUPass);
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will activate the current user in the database table, thus confirming the email;
     *
     * This method will update the activated field of the passed user thus allowing him to use the services provided by our
     * application. The concept of activation is just to test that no automated users are registered with the system.
     *
     * @param S $objUserName The passed user to activate;
     * @return boolean Will return true if the user is going to get activated;
     */
    public function doActivateByUserName (S $objUserName) {
        // Do return ...
        return $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', self::$objAuthUsersTable)->doToken ('%condition', new S ('%objAuthUsersTableFActivated = "Y"
        WHERE %objAuthUsersTableFUName = "%uId" LIMIT 1'))
        ->doToken ('%uId', $objUserName));
    }

    /**
     * Will activate the current user in the databse table, by the given hash. The given hash can be anything, from SHA1 hash, to
     * MD5 and can contain any hash string - as long as the received hash string retains the same mecanism.
     *
     * @param S $objHash The has to activate the user of ...
     * @return boolean Will return OK if everything went OK ...
     */
    public function doActivateByHash (S $objHASH) {
    	// Do return ...
    	return $this->_Q (_QS ('doUPDATE')
    	->doToken ('%table', self::$objAuthUsersTable)->doToken ('%condition', new S ('%objAuthUsersTableFActivated = "Y",
    	%objAuthUsersTableFHash = "" WHERE %objAuthUsersTableFHash = "%hId" LIMIT 1'))->doToken ('%hId', $objHASH));
    }

    /**
     * Will return information about the user group of the passed user id;
     *
     * This method will query the database to retrieve group information about the passed user id. This will allow to avoid
     * doing queries outside of the class, which just litter code. Also it's an easy way to get information about to tightly binded
     * entities: the user and the group it belongs to ...
     *
     * @param S $objUserId The passed user id;
     * @param S $objFieldToGet The field of the group to query for;
     * @return mixed Depends on what was requested;
     */
    public function getGroupInfoByUserId (S $objUserId, S $objFieldToGet) {
        // Do return ...
        return $this->getGroupInfoById ($this->getUserInfoById ($objUserId,
        self::$objAuthUsersTableFUGId), $objFieldToGet);
    }

    /**
     * Will return information about the user group of the passed user name;
     *
     * This method will query the database to retrieve group information about the passed user name. As you guessed it, it first
     * does a query to find the user id and uses that user id to query for group related information; It uses the method based on
     * the user id to do its queries;
     *
     * @param S $objUserName The passed user name;
     * @param S $objFieldToGet The passed field to get;
     * @return mixed Depends on what was requested;
     */
    public function getGroupInfoByUserName (S $objUserName, S $objFieldToGet) {
        // Do return ...
        return $this->getGroupInfoByUserId ($this->getUserInfoByName ($objUserName,
        self::$objAuthUsersTableFId), $objFieldToGet);
    }

    /**
     * Will return information for the current user;
     *
     * This method will get the current user id from cookie and see if what information can we retrieve for the current
     * authenticated user. We do check upon script execution that the data stored in the cookie or in the session is consistent
     * to what we have in the database, to avoid hacking mechanisms like changing cookie information. This method uses a CALL to
     * the getUserInfoById method we already coded;
     *
     * @param S $objFieldToGet The field to get for the current user;
     * @return mixed Depends on what was requested;
     */
    public function getCurrentUserInfoById (S $objFieldToGet) {
        // Do return ...
        return ($this->objCookie->checkKey (self::$objAuthUsersTableFId)->toBoolean () == TRUE) ?
        $this->getUserInfoById ($this->objCookie->getKey (self::$objAuthUsersTableFId),
        $objFieldToGet) : new B (FALSE);
    }

    /**
     * Will return information for the group of the current user;
     *
     * This method will get information about the group of the current user. It ueses the already coded method getGroupInfoById,
     * to query for group information, doing a CALL to that method, using information stored in the cookie or in the session,
     * depending on authentication mechanism, to get that requested information;
     *
     * @param S $objFieldToGet The field to get for the current user;
     * @return mixed Depends on what was requested;
     */
    public function getGroupInfoForCurrentUser (S $objFieldToGet) {
        // Do return ...
        return ($this->objCookie->checkKey (self::$objAuthUsersTableFId)->toBoolean () == TRUE) ?
        $this->getGroupInfoByUserId ($this->objCookie->getKey (self::$objAuthUsersTableFId),
        $objFieldToGet) : new B (FALSE);
    }

    /**
     * Will return the group path for the current user;
     *
     * This method will return the group path for the current user. We actually don't need to get the group path for other
     * users, because we actually won't determine the ACL for a user, except for the current one, which we authenticate. Thus,
     * this method doesn't use any indirect CALL, but a direct one to the MPTT object registered with our system;
     *
     * @return array Will return an array of the group hierarchy for the current user;
     */
    public function getGroupPathForCurrentUser () {
        // Do an MPTT search, on the group of the current logged in user; Simple, no?!
        return self::$objMPTT->mpttGetSinglePath ($this
        ->getGroupInfoForCurrentUser (self::$objAuthGroupTableFName));
    }

    /**
     * Will check ACL for current authenticated user;
     *
     * This method will check current access type of the curren authenticated user, agains the specific zone identifier (or zone
     * name). If everything i OK it will return a boolean true/or false depending on the type of access the user has. If the zone
     * name doesn't exist or something like that, it will default to a boolean false;
     *
     * @param S $objZoneName The zone identifier (a.k.a. name) to check access for;
     * @return boolean Will return true if the current user has permission to access the zone;
     */
    public function checkCurrentUserZoneACL (S $objZoneName) {
    	// Do return ...
        return $this->objCookie->checkKey (self::$objAuthUsersTableFId)->toBoolean () == TRUE ?
        $this->checkZoneACL ($this->getCurrentUserInfoById (self::$objAuthUsersTableFUName), $objZoneName) : new B (FALSE);
    }

    /**
     * Will return the count of users in the table that meet the SQL condition;
     *
     * This method will return the count of users that meet the passed SQL parameter. This method is used mainly to determine if
     * the current page where it is invoked needs some kind of pagination or not. Also, it can have some kind of statistic use,
     * for example, when generating reports on how many users meet a certain criteria;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer The count of existing users;
     */
    public function getUserCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objAuthUsersTableFId) AS count'))->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of groups in the table that meet the SQL condition;
     *
     * This method will return the count of groups that meet the passed SQL parameter. This method is used mainly to determine if
     * the current page where it is invoked needs some kind of pagination or not. Also, it can have some kind of statistic use,
     * for example, when generating reports on how many groups meet a certain criteria;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer The count of existing groups;
     */
    public function getGroupCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objAuthGroupTableFId) AS count'))->doToken ('%table', self::$objAuthGroupTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of zones in the table that meet the SQL condition;
     *
     * This method will return the count of zones that meet the passed SQL parameter. This method is used mainly to determine if
     * the current page where it is invoked needs some kind of pagination or not. Also, it can have some kind of statistic use,
     * for example, when generating reports on how many groups meet a certain criteria;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer The count of existing zones;
     */
    public function getZoneCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objAuthZonesTableFId) AS count'))->doToken ('%table', self::$objAuthZonesTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of mappings of groups to zones, or users to zones in the table that meet the SQL condition;
     *
     * This method will return the count of mappings between zones and groups or zones and users that meet the passed SQL
     * condition. The only thinkable use of such a method is behind the scenes, in the administrator back-end of the website,
     * where some pagination is needed for the mapping list. The mapping list is also known as ACL (access control list) ...
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer The count of existing mappings;
     */
    public function getMappingCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objAuthZoneMTableFId) AS count'))->doToken ('%table', self::$objAuthZoneMTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the current users that meet the passed SQL criteria;
     *
     * This method will return an array of all the users that meet the current criteria. The concept of an user will not be
     * matched against the current defined group, which means, that after getting this array of users, for each group retrieved
     * from the array, you need to get its name using the proper object methods for that ...
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return array The array of returned users;
     */
    public function getUsers (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return an user field, by passing an id to it, and the corresponding field;
     *
     * This method will return a field description of the user, by passing the id of the user, and the corresponding field to
     * return. If that is done, than it will return that information from the database, so you can use it ...
     *
     * @param S $objUserId The passed user id;
     * @param S $objFieldToGet The field to get;
     * @return mixed Depends on what was returned from the database;
     */
    public function getUserInfoById (S $objUserId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objUserId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return an user field, by passing the user name and the corresponding field;
     *
     * This method will return a field description of the user, by passing the name of the user. We use such a function for SEO
     * purposes only, for example, we want to show the Profile/[User] page and for SEO purposes we need the name of the user to be
     * visible in the URL. That's why we need such a method ...
     *
     * @param S $objUserName The passed user name;
     * @param S $objFieldToGet The field to get;
     * @return mixed Depends on what was returned from the database;
     */
    public function getUserInfoByName (S $objUserName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAuthUsersTable)
        ->doToken ('%condition', new S ('WHERE %objAuthUsersTableFUName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objUserName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the array of groups, using the MPTT object;
     *
     * This method will make a call to the MPTT object, to manage the hierarchical data. We use a generic MPTT object in all
     * hierarchical data structures, like the ACL system, or the article table. You can pass a subcategory to start from,
     * and an SQL condition to be met ...
     *
     * @param S $objSubCategory The passed subcategory, NULL if not passed;
     * @param S $objSQLCondition The passed SQL condition;
     * @return array The array of categories, organized by depth ...
     */
    public function getGroups (S $objSQLCondition = NULL,
    S $objSubCategory = NULL) {
        // Make a CALL to the MPTT object;
        return self::$objMPTT->mpttGetTree ($objSubCategory,
        $objSQLCondition);
    }

	/**
	 * Will return a group field, by passing the group id and the corresponding field;
	 *
	 * This method will return a field description of the group, by passing the id of the group and the corresponding field to
	 * return. If that is done, then it will return that information from the database, so you can use it ...
	 *
	 * @param S $objGroupId The passed group id to query for;
	 * @param S $objFieldToGet The field to retrieve;
	 * @return mixed Depends on what was returned from the database;
	 */
    public function getGroupInfoById (S $objGroupId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAuthGroupTable)
        ->doToken ('%condition', new S ('WHERE %objAuthGroupTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objGroupId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return a field description of the group, by passing the group name and field;
     *
     * This method will return a field description of the group, by passing the group name and the field to query for. We use such
     * functionality for example in SEO like URLs, where we want to show the name of the group in the address. Thus, we enable
     * the use of simple functions for such kind of retrieves, without to much work;
     *
     * @param S $objGroupName The group name to query for;
     * @param S $objFieldToGet The field to get after querying;
     * @return mixed Depends on what was returned from the database;
     */
    public function getGroupInfoByName (S $objGroupName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAuthGroupTable)
        ->doToken ('%condition', new S ('WHERE %objAuthGroupTableFName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objGroupName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return the array of zones that meet the SQL criteria;
     *
     * This method will return the array of zones that meet the SQL criteria, that you can use however you want. You may be
     * already used to the way we treat this functionality: while WHERE and ORDER are conditions for use, the table, and condition
     * tokens are standard for all ... So is here;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return array The array of returned zones;
     */
    public function getZones (S $objSQLCondition = NULL,
    S $objSubCategory = NULL) {
        // Do return ...
        return self::$objMPTTForZones->mpttGetTree ($objSubCategory,
        $objSQLCondition);
    }

    /**
     * Will return the zones by a given SQL condition;
     *
     * This method is used to return an array of defined zones that meet a specific SQL criteria. The other ->getZones method
     * is used just to return the zones in their hierarchical order. But that is now always the case we're interested in, that's
     * why we provide this alternative method;
     *
     * @param $objSQLCondition The passed SQL condition;
     * @return arrat The array of returned zones;
     */
    public function getZonesBy (S $objSQLCondition = NULL) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthZonesTable)
        ->doToken ('%condition', $objSQLCondition));
    }

	/**
	 * Will return a zone field, by passing the zone id and the corresponding field;
	 *
	 * This method will return a field description of the zone, by passing the id of the zone and the corresponding field to
	 * return. If that is done, then it will return that information from the database, so you can use it ...
	 *
	 * @param S $objZoneId The passed zone id ...;
	 * @param S $objFieldToGet The field to retrieve;
	 * @return mixed Depends on what was returned from the database;
	 */
    public function getZoneInfoById (S $objZoneId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAuthZonesTable)
        ->doToken ('%condition', new S ('WHERE %objAuthZonesTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objZoneId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

	/**
	 * Will return a zone field, by passing the proper zone name and field to get;
	 *
	 * This method will return a field description, by querying for the passed zone name. Although in other cases we would say that
	 * this is used for SEO, here this kind of functionality is used to have string identifiers through-out the project. For ex.
	 * if we have the 'Authentication' zone, it's easier for us to remember the string of the zone to query for, besides knowing
	 * the id, which may change, thus meaning name-based string queryies for zones are the best choice for our ACL system;
	 *
	 * @param S $objZoneName The zone name to query for;
	 * @param S $objFieldToGet The field to query for;
	 * @return mixed Depends on what was returned from the database;
	 */
    public function getZoneInfoByName (S $objZoneName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objAuthZonesTable)
        ->doToken ('%condition', new S ('WHERE %objAuthZonesTableFName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objZoneName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will check the zone info, if it is set;
     *
     * This method will check that a specific zone information is set. This is to determine if that current zone exists, and it's
     * mainly used by our automated administration code, to map access to the the administrator group and user; It can also be
     * used to check other zone information, like price or any other kind of information that's associated to an access zone;
     *
     * @param S $objZoneName What to check;
     * @param S $objFieldToGet What field is it on;
     * @return boolean Will return true if something was found in the database;
     */
    public function checkZoneByName (S $objZoneName) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', self::$objAuthZonesTableFName)->doToken ('%table', self::$objAuthZonesTable)
        ->doToken ('%condition', new S ('WHERE %key = "%var" LIMIT 1'))->doToken ('%key', self::$objAuthZonesTableFName)
        ->doToken ('%var', $objZoneName))->doCount ()->toInt () != 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set the zone information needed;
     *
     * This method will set the needed zone information. This function is used to automatically add new zones to requested zones
     * if they do not exist in the database, to make sure that we have a consistent mapping through-out the whole system;
     *
     * @param S $objZoneName What to set on the specified field;
     * @param S $objFieldToGet The field to set;
     * @return boolean Will return true if everything was ok;
     */
    public function doMakeZone (S $objZoneName, S $objZonePName = NULL) {
        // Memorize if it's a kid;
        $objAddAsKid = new B (TRUE);

        // This means, it's not a kid;
        if ($objZonePName == NULL) {
            $objZonePName = $this->getObjectCLASS ();
            $objAddAsKid->switchType ();
        }

        // Switch ...
        switch ($objAddAsKid->toBoolean ()) {
            case TRUE:
                // Do return ...
                return self::$objMPTTForZones->mpttAddnode ($objZoneName,
                $objZonePName, new S ((string) MPTT::LAST_CHILD));
                break;

            case FALSE:
                // Do return ...
                return self::$objMPTTForZones->mpttAddnode ($objZoneName,
                $objZonePName, new S ((string) MPTT::PREVIOUS_BROTHER));
                break;
        }
    }

    /**
     * Will return an array of all mapped group/users to zones ...
     *
     * This method will return an array with all the mappings in the database. It will return if the mapping is a group or user
     * mapping type and get the required information needed to display these mappings (eg. most important: username or group name),
     * which should help clean back-end code a lot ...
     *
     * @param S $objSQLCondition The passed SQL condition, if passed;
     * @return array An array of all the mappings in the database;
     */
    public function getZoneMappings (S $objSQLCondition = NULL) {
        // Do a query, set the conditions, return the array;
        foreach ($objReturnedZones = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthZoneMTable)
        ->doToken ('%condition', $objSQLCondition)) as $k => $v) {
            // Get the zone name, for display ...
            $v['zone_name'] = $this->getZoneInfoById ($v[self::$objAuthZoneMTableFZId],
            self::$objAuthZonesTableFName);

            // Get either group or the user's name ...
            if ($v[self::$objAuthZoneMTableFIUG] == 'Y') {
                $v['user_or_group_name'] = $this
                ->getGroupInfoById ($v[self::$objAuthZoneMTableFUGId],
                self::$objAuthGroupTableFName);
            } else {
                $v['user_or_group_name'] = $this
                ->getUserInfoById ($v[self::$objAuthZoneMTableFUGId],
                self::$objAuthUsersTableFUName);
            }
        }
        // Do return ...
        return $objReturnedZones;
    }

    /**
     * Will check if the administrator account is mapped to the specified zone;
     *
     * This method will check if the first administrator account, the one we INSTALL by default is mapped to an ACL zone. This is
     * usually used to set some default privileges for the administrator account and to set the default conditions for the
     * framework to work on;
     *
     * @param S $objZoneName The passed zone name to check for mapping;
     * @return boolean Will return true if the administrator is mapped to that zone;
     */
    public function checkAdministratorIsMappedtoZone (S $objZoneName) {
        // Do return ...
        return $this->checkFixedACL (self::$objAuthDefaultGroup,
        $objZoneName, new S ('A'));
    }

    /**
     * Will map the administrator account to the specified zone;
     *
     * This method will map the default administrator account to the specified zone. This is used to set the default access for the
     * provided zones, thus making an usefull INSTALL environment for the framework;
     *
     * @param S $objZoneName The zone name to map the administrator account to;
     * @return boolean Will return true if it was able to map the administrator account to the passed zone;
     */
    public function doMapAdministratorToZone (S $objZoneName) {
        // Do return ...
        return $this->setFixedACL (self::$objAuthDefaultGroup,
        $objZoneName, new S ('A'));
    }

    /**
     * Will check that the group is mapped to the given zone;
     *
     * This method will check if the passed group is mapped to the passed zone. If that's so, it will return true, thus allowing
     * the proper code to be executed. It doesn't check the type of access to the zone, just the 1 - 1 mapping in this case;
     *
     * @param S $objGroup The passed group to check;
     * @param S $objZone The passed zone to check group mapping to;
     * @return boolean Will return true if the group is mapped to the specified zone;
     */
    private function checkFixedACL (S $objGroup, S $objZone) {
        // Get some requirements ...
        $objZoneId  = $this->getZoneInfoByName ($objZone, self::$objAuthZonesTableFId);
        $objGroupId = $this->getGroupInfoByName ($objGroup, self::$objAuthGroupTableFId);

        // Do the query, make it happen ...
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthZoneMTable)
        ->doToken ('%condition', new S ('WHERE %objAuthZoneMTableFZId = "%z" AND %objAuthZoneMTableFUGId = "%g"
        AND %objAuthZoneMTableFIUG = "Y" LIMIT 1'))->doToken ('%g', $objGroupId)->doToken ('%z', $objZoneId))
        ->doCount ()->toInt () != 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will set fixed ACL for the passed group name;
     *
     * This method will set a fixed ACL for the passed group anme and zone combination. This kind of fixed ACL is not eraseable,
     * which means that not even the administrator is not able to delete these mappings, mainly due to the fact that they are
     * system dependent. This means that if they remove the mappings, the administrator won't be able to login, ever ...
     *
     * @param S $objGroupName The passed group to set ACL for;
     * @param S $objZoneName The zone to map to;
     * @param S $objTypeOfAccess What kind of access to set;
     * @return boolean Will return true if the mapping has been set;
     */
    private function setFixedACL (S $objGroupName, S $objZoneName, S $objTypeOfAccess) {
        switch ($objTypeOfAccess) {
            case 'D':
            case 'A':
                // Get some requirements;
                $objZoneId  = $this->getZoneInfoByName  ($objZoneName, self::$objAuthZonesTableFId);
                $objGroupId = $this->getGroupInfoByName ($objGroupName, self::$objAuthGroupTableFId);

                // Do return ...
                return $this->_Q (_QS ('doINSERT')
                ->doToken ('%table', self::$objAuthZoneMTable)
                ->doToken ('%condition', new S ('%objAuthZoneMTableFIUG = "Y", %objAuthZoneMTableFErase = "N",
                %objAuthZoneMTableFZId  = "%z", %objAuthZoneMTableFAorD = "%t", %objAuthZoneMTableFUGId = "%g"'))
                ->doToken ('%z', $objZoneId)->doToken ('%t', $objTypeOfAccess)->doToken ('%g', $objGroupId));
                break;

            default:
                // Render a screen of death ...
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (INCOMPATIBLE_ZONE_ACCESS),
                new S (INCOMPATIBLE_ZONE_ACCESS_FIX));
                break;
        }
    }

    /**
     * Will check ACL for the specific user and zone;
     *
     * This method will query the database, user, group, zone and zone mapping tables to see if the current user that is mapped
     * to a group or is in a hierarchy of groups has access to the specified zone. Access is giving by mapping permissions in
     * a zone like manner, with either allow or deny type of access;
     *
     * @param S $objUserName The user to check ACL for;
     * @param S $objZoneName The zone to check ACL for;
     * @return boolean Will return true if the current user has access to zone;
     */
    private function checkZoneACL (S $objUserName, S $objZoneName) {
        // Get some requirements;
        $objUserId = $this->getUserInfoByName ($objUserName, self::$objAuthUsersTableFId);
        $objZoneId = $this->getZoneInfoByName ($objZoneName, self::$objAuthZonesTableFId);

        // Make a new query container;
        $objQ = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', self::$objAuthZoneMTableFAorD)->doToken ('%table', self::$objAuthZoneMTable)
        ->doToken ('%condition', new S ('WHERE %objAuthZoneMTableFZId = "%z" AND %objAuthZoneMTableFUGId = "%u"
        AND %objAuthZoneMTableFIUG = "N" LIMIT 1'))->doToken ('%z', $objZoneId)->doToken ('%u', $objUserId));

        // Get the zone access for user groups/subgroup;
        if ($objQ->doCount ()->toInt () != 0) {
            // Determine if specific zone access have been enabled;
            switch ($objQ->offsetGet (0)->offsetGet (self::$objAuthZoneMTableFAorD)) {
                case 'A':
                    return new B (TRUE);
                    break;

                default:
                    return new B (FALSE);
                    break;
            }
        } else {
            // Determine the path from the users sub-group, to the top root group;
            $objCurrentUserHierarchy = self::$objMPTT->mpttGetSinglePath ($this->getGroupInfoByUserId
            ($this->getUserInfoByName ($objUserName, self::$objAuthUsersTableFId), self::$objAuthGroupTableFName));

            // Foreach LVL, get respective zone-mappings;
            foreach ($objCurrentUserHierarchy as $k => $v) {
                $objGroupId = $this->getGroupInfoByName ($v[self::$objAuthGroupTableFName], self::$objAuthGroupTableFId);
                $objQueryACL[] = $this->_Q (_QS ('doSELECT')
                ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthZoneMTable)
                ->doToken ('%condition', new S ('WHERE %objAuthZoneMTableFZId = "%z" AND %objAuthZoneMTableFUGId = "%g"
                AND %objAuthZoneMTableFIUG = "Y"'))->doToken ('%z', $objZoneId)->doToken ('%g', $objGroupId))->offsetGet (0);
            }

            // Go down the tree, pass through every mapped group/subgroup and memorize, the LAST Access/Denied flag;
            $objACLMemorized = new S ('D');
            foreach ($objQueryACL as $k => $v) {
                if ($v->doCount ()->toInt () != 0) {
                    $objACLMemorized->setString ($v[self::$objAuthZoneMTableFAorD]);
                }
            }

            // Switch between A/D/OR ELSE;
            switch ($objACLMemorized) {
                case 'A':
                    return new B (TRUE);
                    break;

                case 'D':
                    return new B (FALSE);
                    break;

                default:
                    return new B (FALSE);
                    break;
            }
        }
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
    public function renderWidget (S $objWidgetToRender, A $objWA = NULL) {
        // Make an empty array if NULL ...
        if ($objWA == NULL) $objWA = new A;

        // Do a switch ...
        switch ($objWidgetToRender) {
            case 'userLogIn':
                // Check if the we're logged in ...
                if ($this->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                    // Don't show the form ...
                    $objShowFrm = new B (FALSE);
                } else {
                    // Show it ...
                    $objShowFrm = new B (TRUE);
                }

                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objWidgetToRender . TPL_EXTENSION);
                TPL::tpSet ($objShowFrm, new S ('objShowForm'), $tpF);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($this, new S ('ATH'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'userSignUp':
                if (isset ($_GET[AUTHENTICATION_STATUS_URL])) {
                    // Don't show the form ...
                    $objShowFrm = new B (FALSE);
                } else {
                    // Show it ...
                    $objShowFrm = new B (TRUE);
                }

                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objWidgetToRender . TPL_EXTENSION);
                TPL::tpSet ($objShowFrm, new S ('objShowForm'), $tpF);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($this, new S ('ATH'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'userProfile':
                if (isset ($_GET[AUTHENTICATION_STATUS_URL])) {
                    // Get me there ...
                    $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_SECTION_URL)),
                    new A (Array (FRONTEND_HOME))), new S ('Location'));
                } else {
                    if ($this->checkIfUserIsLoggedIn ()->toBoolean () == TRUE) {
                        // Make'em form ...
                        $this->renderform (new S ('userProfile'), $objWA);
                    } else {
                        // Get me there ...
                        $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_SECTION_URL)),
                        new A (Array (FRONTEND_HOME))), new S ('Location'));
                    }
                }
                // BK;
                break;

            case 'widgetUserProfileBox':
                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objWidgetToRender . TPL_EXTENSION);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($this, new S ('ATH'), $tpF);
                TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'userActivateByHash':
                if (isset ($_GET[AUTHENTICATION_HASH_URL])) {
                    if ($this->checkHashExists ($_GET[AUTHENTICATION_HASH_URL])
                    ->toBoolean () == TRUE) {
                        // Everything is OK, activate me ...
                        if ($this->doActivateByHash ($_GET[AUTHENTICATION_HASH_URL])
                        ->toBoolean () == TRUE) {
                            // Make the content ...
                            $objContent = $this->getConfigKey (new S ('authentication_page_activation_ok_message'));
                        }
                    } else {
                        // Make the content ...
                        $objContent = $this->getConfigKey (new S ('authentication_page_activation_not_ok_message'));
                    }

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objWidgetToRender . TPL_EXTENSION);
                    TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                    TPL::tpSet ($objContent, new S ('objContent'), $tpF);
                    TPL::tpSet ($this, new S ('ATH'), $tpF);
                    TPL::tpExe ($tpF);
                } else {
                    // Hacking attempt ... go back dude ...
                    $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_SECTION_URL)),
                    new A (Array (FRONTEND_HOME))), new S ('Location'));
                }
                // BK;
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
            case 'welcomePage':
                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'welcomePage.tp');
                TPL::tpSet (self::$objAdministration->getWidget (NULL), new S ('objWidgets'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'manageUsers':
                // Do specific actions, based on _GET parameters;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Switch ...
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('userEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('userErase'));
                            break;
                    }
                } else {
                    // Show them ordered by DESC;
                    if (!isset ($_GET[ADMIN_ACTION_SORT]))
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SORT)),
                    new A (Array ('DescByRegistered'))), new S ('Location'));

					// Set some requirements;
                    $objGetCondition = new S;

                    if (isset ($_GET[ADMIN_ACTION_BY])) {
                    	// Do a switch ...
                        switch ($_GET[ADMIN_ACTION_BY]) {
                            case AUTHENTICATION_PROFILE_USERNAME:
                                $objGetCondition->appendString ('WHERE %objAuthUsersTableFUName');
                                break;

                            case AUTHENTICATION_PROFILE_EMAIL:
                                $objGetCondition->appendString ('WHERE %objAuthUsersTableFEML');
                                break;

                            case AUTHENTICATION_PROFILE_GROUP:
                                $objGetCondition->appendString ('AS t1 LEFT JOIN %objAuthGroupTable
                                AS t2 ON t1.%objAuthUsersTableFUGId = t2.%objAuthGroupTableFId
                                WHERE t2.%objAuthGroupTableFName');
                                break;
                        }

                        // Add LIKE searching ...
                        $objGetCondition->appendString (_SP)
                        ->appendString ('LIKE "%%Search%"')
                        ->doToken ('%Search', $_GET[ADMIN_ACTION_SEARCH]);
                    }

                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByUsername':
                            case 'DescByUsername':
                                // Set the order ...
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAuthUsersTableFUName');
                                // Switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByUsername':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByUsername':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByEMail':
                            case 'DescByEMail':
                                // Set the order ...
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAuthUsersTableFEML');
                                // Switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByEMail':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByEMail':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByGroup':
                            case 'DescByGroup':
                                // Set the order ...
                                if (isset ($_GET[ADMIN_ACTION_BY])) {
                                    $objGetCondition->appendString (_SP)
                                    ->prependString ('AS t1 LEFT JOIN %objAuthGroupTable
                                    AS t2 ON t1.%objAuthUsersTableFUGId = t2.%objAuthGroupTableFId ');
                                } else {
                                    $objGetCondition->appendString (_SP)
                                    ->appendString ('AS t1 LEFT JOIN %objAuthGroupTable
                                    AS t2 ON t1.%objAuthUsersTableFUGId = t2.%objAuthGroupTableFId ');
                                }

                                // Add order by ...
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY t2.%objAuthGroupTableFName');
                                // Switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByGroup':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByGroup':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByRegistered':
                            case 'DescByRegistered':
                                // Set the order ...
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objAuthUsersTableFRegOn');
                                // Switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByRegistered':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByRegistered':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;
                        }
                    }

                    // Make the unordered condition;
                    $objGetCondition = $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Get the users ...
                    $objUsersTable = $this->getUsers ($objGetCondition);
                    if (isset ($_GET[ADMIN_ACTION_BY])) { $objUsersTableCount = $objUsersTable->doCount (); }
                    else { $objUsersTableCount = $this->getUserCount (); }

                    // Get each group for each user ...
                    foreach ($objUsersTable as $k => $v) {
                        $v['group_name'] = $this->getGroupInfoById ($v[Authentication::$objAuthUsersTableFUGId],
                        Authentication::$objAuthGroupTableFName);
                    }

                    // Fix pagination when count is LESS than 10;
                    if (isset ($_GET[ADMIN_ACTION_BY]) && isset ($_GET[ADMIN_PAGINATION])) {
                        if ($objUsersTableCount->toInt () < 10) {
                            // Remove paging ... & redirect to proper ...
                            TPL::setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGINATION))), new S ('Location'));
                        } else {
                            if (CEIL ($objUsersTableCount->toInt () / 10) < (int) $_GET[ADMIN_PAGINATION]->toString ()) {
                                // Redirect to proper ...
                                TPL::setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGINATION)),
                                new A (Array (CEIL ($objUsersTableCount->toInt () / 10)))), new S ('Location'));
                            }
                        }
                    }

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageUsers.tp');
                    TPL::tpSet ($objUsersTable, new S ('usersTable'), $tpF);
                    TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do them pagination ...
                    if ($objUsersTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objUsersTableCount);

                    // Set a search form;
                    $this->renderForm (new S ('userSearch'));
                    $this->renderForm (new S ('userCreate'));
                }
                break;

            case 'manageGroups':
                // Add some requirements;
                TPL::manageJSS (new FilePath ($this->getPathToSkinJSS ()
                ->toRelativePath () . 'manageGroups.js'), new S ('manageCategories'));

                // Do specific actions, based on _GET parameters;
                if (isset ($_GET[ADMIN_ACTION])) {
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('groupEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('groupErase'));
                            break;

                        case ADMIN_ACTION_MOVE:
                            $this->renderForm (new S ('groupMove'));
                            break;
                    }
                } else {
                    // Do an empty SQL string;
                    $objGetCondition = new S;
                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'DescByGroup':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('DESC');
                                break;

                            case 'AscByGroup':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ASC');
                                break;
                        }
                    }

                    // Add some LIMITs;
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit) ->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objGroupTable = $this->getGroups (isset ($_GET[ADMIN_SHOW_ALL]) ? new S : $objGetCondition);
                    $objGroupTableCount = $this->getGroupCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageGroups.tp');
                    TPL::tpSet ($objGroupTable, new S ('groupTree'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do them paginations ...
                    if ($objGroupTableCount->toInt () > 10 && !isset ($_GET[ADMIN_SHOW_ALL]))
                    self::$objAdministration->setPagination ($objGroupTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('groupCreate'));
                }
                break;

            case 'manageZones':
                // Do specific actions, based on _GET parameters;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Switch ...
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('zoneEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('zoneErase'));
                            break;
                    }
                } else {
                    // Do a sorting beforehand;
                    $objGetCondition = new S;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByZone':
                            case 'DescByZone':
                                // Switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByZone':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString (new S ('ASC'));
                                        break;

                                    case 'DescByZone':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString (new S ('DESC'));
                                        break;
                                }
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objZonesTable = $this->getZones ($objGetCondition);
                    $objZonesTableCount = $this->getZoneCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageZones.tp');
                    TPL::tpSet ($objZonesTable, new S ('zonesTable'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do them paginations ...
                    if ($objZonesTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objZonesTableCount);
                }
                break;

            case 'manageMappings':
                // Do specific actions, based on _GET parameters;
                if (isset ($_GET[ADMIN_ACTION])) {
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('zoneMappingEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('zoneMappingErase'));
                            break;
                    }
                } else {
                    // Do a sorting beforehand;
                    $objGetCondition = new S;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByGroup':
                            case 'DescByGroup':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('AS t1 LEFT JOIN %objAuthGroupTable
                                AS t2 ON t1.%objAuthZoneMTableFUGId = t2.%objAuthGroupTableFId
                                ORDER BY t2.%objAuthGroupTableFName');
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByGroup':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByGroup':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByZoneForGroup':
                            case 'DescByZoneForGroup':
                                // Set some requirements;
                                $objGetCondition = new S ('AS t1 LEFT JOIN %objAuthZonesTable
                                AS t2 ON t1.%objAuthZoneMTableFZId = t2.%objAuthGroupTableFId
                                ORDER BY t2.%objAuthZonesTableFName');
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByZoneForGroup':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByZoneForGroup':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;
                        }
                    }

                    // LIMIT only TO GROUP;
                    if ($objGetCondition->toLength ()->toInt () != 0) {
                    $objGetCondition->doToken ('ORDER', new S ('WHERE %objAuthZoneMTableFIUG = "Y" ORDER')); }
                    else { $objGetCondition = new S ('WHERE %objAuthZoneMTableFIUG = "Y"'); }

                    // Add SOME LIMITs;
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements;
                    $objZoneMappings = $this->getZoneMappings ($objGetCondition);
                    $objMappingsTableCount = $this->getMappingCount (new S ('WHERE %objAuthZoneMTableFIUG = "Y"'));

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageMappings.tp');
                    TPL::tpSet ($objZoneMappings, new S ('zonesMappingsTableForGroup'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination;
                    if ($objMappingsTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objMappingsTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('zoneMappingCreateForGroups'));
                }
                break;

            case 'manageMappingsForUsers':
                // Do specific actions, based on _GET parameters;
                if (isset ($_GET[ADMIN_ACTION])) {
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('zoneMappingEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('zoneMappingErase'));
                            break;
                    }
                } else {
                    // Do a sorting beforehand;
                    $objGetUCondition = new S;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByZone':
                            case 'DescByZone':
                                // Set some requirements;
                                $objGetUCondition = new S ('AS t1 LEFT JOIN %objAuthZonesTable
                                AS t2 ON t1.%objAuthZoneMTableFZId = t2.%objAuthUsersTableFId
                                ORDER BY t2.%objAuthZonesTableFName');
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByZone':
                                        $objGetUCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByZone':
                                        $objGetUCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;

                            case 'AscByUsername':
                            case 'DescByUsername':
                                // Set some requirements;
                                $objGetUCondition = new S ('AS t1 LEFT JOIN %objAuthUsersTable
                                AS t2 ON t1.%objAuthZoneMTableFUGId = t2.%objAuthUsersTableFId
                                ORDER BY t2.%objAuthUsersTableFUName');
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByUsername':
                                        $objGetUCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByUsername':
                                        $objGetUCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                break;
                        }
                    }

                    // LIMIT ONLY to USERS;
                    if ($objGetUCondition->toLength ()->toInt () != 0) {
                    $objGetUCondition->doToken ('ORDER', new S ('WHERE %objAuthZoneMTableFIUG = "N" ORDER')); }
                    else { $objGetUCondition = new S ('WHERE %objAuthZoneMTableFIUG = "N"'); }

                    // Set some requirements;
                    $objMappingsTableCount = $this->getMappingCount (new S ('WHERE %objAuthZoneMTableFIUG = "N"'));
                    $objZoneMappings = $this->getZoneMappings ($objGetUCondition);

                    // Set the template file;
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageMappingsForUsers.tp');
                    TPL::tpSet ($objZoneMappings, new S ('zonesMappingsTableForUsers'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination;
                    if ($objMappingsTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objMappingsTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('zoneMappingCreateForUsers'));
                }
                // Break out;
                break;

            case 'manageConfiguration':
                if (isset ($_GET[ADMIN_ACTION])) { $this->renderForm ($_GET[ADMIN_ACTION]); }
                else { $this->renderForm (new S ('configurationEdit')); }
                break;
        }
    }

    /**
     * Will render a specified form, the name of the form given by the first parameter;
     *
     * This method will render one of the forms for our object, invoked by giving the proper form identifier to the current form.
     * have chosen this method of invoking forms, because we just had too many this->renderSomethingMethod (), which really had
     * an impact on code massiveness. Also, having code organized in switch/case statements leads us to be able to share common
     * settings between different forms, as we've done with the methods defined in the __CALL method above;
     *
     * For example, if we wanted to share some common configuration between a create and an edit form, we could have introduced
     * two switches in this method, one that would have set the common options, and the second, would have just passed through
     * again, and get the already set configuration options, using them. This means that if we needed to change behavior of
     * some interconnected forms, that would mean modifying the needed code one place only, which is a big advantage over
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality best;
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderForm (S $objFormToRender, A $objFA = NULL) {
        // Make them defaults ...
        if ($objFA == NULL) $objFA = new A;

        // Do a switch ...
        switch ($objFormToRender) {
            case 'adminLoginScreen':
                // Do some URL mangling, redirect back to a pure index;
                URL::doCleanURLPath ();

                // Add a <title> tag, saying we need to log in;
                $this->manageTTL (new S (AUTHENTICATION_LOGIN_FORM));

                // Get the personalized CSS;
                $this->manageCSS (new FilePath ($this->getPathToSkin()->toRelativePath () .
                SKIN_CSS_DIR . _S . 'renderAdminLoginScreen.css'), new S (__FUNCTION__));

                // Do the authentication mechanism, check the _POST;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    if ($this->doLogIn ($this->getPOST (self::$objAuthUsersTableFUName),
                    $this->getPOST (self::$objAuthUsersTableFUPass))->toBoolean () == TRUE) {
                        // OK;
                        $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_PAGE)),
                        new A (Array (ADMIN_DASHBOARD))), new S ('Location'));
                    } else {
                        // Do an error, and retain the user;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S (AUTHENTICATION_ACCESS_DENIED));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setName ($objFormToRender)
                ->setFieldset (new S (AUTHENTICATION_LOGIN_FORM))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_LOGIN_GO))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFUName)
                ->setLabel (new S (AUTHENTICATION_USERNAME))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .-]'))
                ->setTooltip ($this->getHELP (new S ('adminLoginScreenU')))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (self::$objAuthUsersTableFUPass)
                ->setLabel (new S (AUTHENTICATION_PASSWORD))
                ->setTooltip ($this->getHELP (new S ('adminLoginScreenP')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userLogIn':
                // Do the authentication mechanism, check the _POST;
                if ($this->checkPOST (new S ('submit_log_in'))->toBoolean () == TRUE) {
                    // ELSE ... go authenticate us ...
                    if ($this->doLogIn ($this->getPOST (new S ('user_username')),
                    $this->getPOST (new S ('user_password')))->toBoolean () == TRUE) {
                        // Set the proper POST ...
                        $this->setPOST (self::$objAuthUsersTableFIp, new S ($_SERVER['REMOTE_ADDR']));

                        // Make and auto-executing form ...
                        $this->setMethod (new S ('POST'))
                        ->setSQLAction (new S ('update'))
                        ->setTableName (self::$objAuthUsersTable)
                        ->setUpdateId ($this->getUserInfoByName ($this
                        ->getPOST (new S ('user_username')), self::$objAuthUsersTableFId))
                        ->setUpdateField (self::$objAuthUsersTableFId)
                        ->setName ($objFormToRender)
                        ->setInputType (new S ('text'))
                        ->setName (self::$objAuthUsersTableFIp)
                        ->setContainerDiv (new B (TRUE))
                        ->setFormEndAndExecute (new B (TRUE));

                        // Ok ... do nothin' ...
                        $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_SECTION_URL)),
                        new A (Array (FRONTEND_HOME))), new S ('Location'));
                    } else {
                        // Do an error, and retain the user;
                        $this->setErrorOnInput (new S ('user_username'),
                        new S ($objFA['log_in_failed']));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setName ($objFormToRender)
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit_log_in'))
                ->setValue (new S ($objFA['log_in']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (new S ('user_username'))
                ->setLabel (new S ($objFA['log_in_username']))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .-]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('user_password'))
                ->setLabel (new S ($objFA['log_in_password']))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userSignUp':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (AUTHENTICATION_STATUS_URL)),
                new A (Array (AUTHENTICATION_STATUS_OK_URL)));

                // Check if both password fields have been entered correctly;
                if ($this->checkPOST (new S ('submit_signup'))->toBoolean () == TRUE) {
                    // Check username ...
                    if ($this->getPOST (self::$objAuthUsersTableFUName)
                    ->toLength ()->toInt () == 0) {
                        //Check non-empty username, and error if the username is empty;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S ($objFA['error_username_mandatory']));
                    } else {
                        // Check USERNAME;
                        if ($this->checkUserNameExists ($this->getPOST (self::$objAuthUsersTableFUName))
                        ->toBoolean () == TRUE)
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S ($objFA['error_username_exists']));

                        // Check EMAIL;
                        if ($this->checkUserMailExists ($this->getPOST (self::$objAuthUsersTableFEML))
                        ->toBoolean () == TRUE)
                        $this->setErrorOnInput (self::$objAuthUsersTableFEML,
                        new S ($objFA['error_email_exists']));
                    }

                    // Check password;
                    if ($this->getPOST (self::$objAuthUsersTableFUPass) != $this
                    ->getPOST (new S ('confirmation_password'))) {
                        // Check password mismatch, and error on it if so;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUPass,
                        new S ($objFA['error_passwords_do_not_match']));
                    }

                    // Check phone number has 10 chars, only if it is required;
                    if (self::PHONE_IS_REQUIRED == 1) {
                        if ($this->getPOST (self::$objAuthUsersTableFPhone)
                        ->toLength ()->toInt () != 10) {
                            $this->setErrorOnInput (self::$objAuthUsersTableFPhone,
                            new S ($objFA['error_phone_ten_chars']));
                        }
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S ($objFA['form_signup']))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthUsersTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAuthUsersTableFId)
                ->setExtraUpdateData (self::$objAuthUsersTableFRegOn, new S ((string) $_SERVER['REQUEST_TIME']))
                ->setExtraUpdateData (self::$objAuthUsersTableFUGId, $objFA['default_group_id'])
                ->setExtraUpdateData (self::$objAuthUsersTableFActivated, new S ('N'))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit_signup'))
                ->setValue (new S ($objFA['form_submit']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFUName)
                ->setLabel (new S ($objFA['form_username']))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .-]'))
                ->setInputType (new S ('password'))
                ->setName (self::$objAuthUsersTableFUPass)
                ->setLabel (new S ($objFA['form_password']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('confirmation_password'))
                ->setLabel (new S ($objFA['form_password_confirm']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFEML)
                ->setRegExpType (new S ('preg'))
                ->setRegExpErrMsg (new S ($objFA['form_regexp_invalid_email']))
                ->setPHPRegExpCheck (new S (self::REGEXP_PHP_CHECK_EMAIL))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9.@_-]'))
                ->setLabel (new S ($objFA['form_email']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFPhone)
                ->setLabel (new S ($objFA['form_phone']))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFLName)
                ->setLabel (new S ($objFA['form_last_name']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFFName)
                ->setLabel (new S ($objFA['form_first_name']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthUsersTableFCountry)
                ->setLabel (new S ($objFA['form_country']));

                // Get the COUNTRIES;
                foreach ($this->STG->getCountries () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v[Settings::$objSettingsCountryTableFIso])
                    ->setValue ($v[Settings::$objSettingsCountryTableFIso])
                    ->setLabel ($v[Settings::$objSettingsCountryTableFPrnt]);
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFCity)
                ->setLabel (new S ($objFA['form_city']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFYM)
                ->setLabel (new S ($objFA['form_ym']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFMSN)
                ->setLabel (new S ($objFA['form_msn']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFICQ)
                ->setLabel (new S ($objFA['form_icq']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFAOL)
                ->setLabel (new S ($objFA['form_aol']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFDesc)
                ->setLabel (new S ($objFA['form_description']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFSignature)
                ->setLabel (new S ($objFA['form_signature']))
                ->setContainerDiv (new B (TRUE));

                // Encrypt the data;
                if ($this->checkFormHasErrors ()->toBoolean () == FALSE) {
                    // Make them passwords OK ...
                    if ($this->checkPOST ()->toBoolean () == TRUE) {
                        $this->setPOST (self::$objAuthUsersTableFUPass, $this->getPOST (self::$objAuthUsersTableFUPass)
                        ->encryptIt (sha1 ($this->getPOST (self::$objAuthUsersTableFUPass))));
                        $this->setPOST (new S ('confirmation_password'), $this->getPOST (self::$objAuthUsersTableFUPass));
                    }

                    $this->setExtraUpdateData (self::$objAuthUsersTableFHash,
                    new S (sha1 (md5 ($this->getPOST (self::$objAuthUsersTableFEML)))));

                    // Send the activation EML ...
                    $objEML = new MAIL;
                    $objEML->doMAIL ($this->getPOST (self::$objAuthUsersTableFEML), $objFA['form_activation_subject'],
                    $this->getHELP (new S ('activationEMLMessage'))->doToken ('%u', $this
                    ->getPOST (self::$objAuthUsersTableFUName))->doToken ('%a', URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
                    AUTHENTICATION_ACTION_URL, AUTHENTICATION_HASH_URL)), new A (Array (FRONTEND_AUTHENTICATION_URL,
                    AUTHENTICATION_ACTIVATE_URL, sha1 (md5 ($this->getPOST (self::$objAuthUsersTableFEML))))))));
                }

                // Continue;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userProfile':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (AUTHENTICATION_STATUS_URL)),
                new A (Array (AUTHENTICATION_STATUS_OK_URL)));

                // Check if both password fields have been entered correctly;
                if ($this->checkPOST (new S ('submit_signup'))->toBoolean () == TRUE) {
                    // Check username ...
                    if ($this->getPOST (self::$objAuthUsersTableFUName)
                    ->toLength ()->toInt () == 0) {
                        //Check non-empty username, and error if the username is empty;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S ($objFA['error_username_mandatory']));
                    } else {
                        if ($this->getPOST (self::$objAuthUsersTableFUName) !=
                        $this->getCurrentUserInfoById (self::$objAuthUsersTableFUName)) {
                            // Check USERNAME;
                            if ($this->checkUserNameExists ($this->getPOST (self::$objAuthUsersTableFUName))
                            ->toBoolean () == TRUE) {
                                $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                                new S ($objFA['error_username_exists']));
                            }
                        }

                        if ($this->getPOST (self::$objAuthUsersTableFEML) !=
                        $this->getCurrentUserInfoById (self::$objAuthUsersTableFEML)) {
                            // Check EMAIL;
                            if ($this->checkUserMailExists ($this->getPOST (self::$objAuthUsersTableFEML))
                            ->toBoolean () == TRUE) {
                                $this->setErrorOnInput (self::$objAuthUsersTableFEML,
                                new S ($objFA['error_email_exists']));
                            }
                        }
                    }

                    // Check password;
                    if ($this->getPOST (self::$objAuthUsersTableFUPass) != $this
                    ->getPOST (new S ('confirmation_password'))) {
                        // Check password mismatch, and error on it if so;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUPass,
                        new S ($objFA['error_passwords_do_not_match']));
                    }

                    // Check phone number has 10 chars, only if it is required;
                    if (self::PHONE_IS_REQUIRED == 1) {
                        if ($this->getPOST (self::$objAuthUsersTableFPhone)
                        ->toLength ()->toInt () != 10) {
                            $this->setErrorOnInput (self::$objAuthUsersTableFPhone,
                            new S ($objFA['error_phone_ten_chars']));
                        }
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S ($objFA['form_signup']))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthUsersTable)
                ->setUpdateId (new S ($this->getCurrentUserInfoById (self::$objAuthUsersTableFId)))
                ->setUpdateField (self::$objAuthUsersTableFId)
                ->setUploadDirectory (new S ('users/avatars'))
                ->setUploadImageResize (new A (Array (128 => 128)))
                ->setExtraUpdateData (self::$objAuthUsersTableFActivated, new S ('N'))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit_signup'))
                ->setValue (new S ($objFA['form_submit']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFUName)
                ->setLabel (new S ($objFA['form_username']))
                ->setReadOnly (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .-]'))
                ->setInputType (new S ('password'))
                ->setName (self::$objAuthUsersTableFUPass)
                ->setLabel (new S ($objFA['form_password']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('confirmation_password'))
                ->setValue ($this->getCurrentUserInfoById (self::$objAuthUsersTableFUPass))
                ->setLabel (new S ($objFA['form_password_confirm']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFEML)
                ->setRegExpType (new S ('preg'))
                ->setRegExpErrMsg (new S ($objFA['form_regexp_invalid_email']))
                ->setPHPRegExpCheck (new S (self::REGEXP_PHP_CHECK_EMAIL))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9.@_-]'))
                ->setLabel (new S ($objFA['form_email']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFPhone)
                ->setLabel (new S ($objFA['form_phone']))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFLName)
                ->setLabel (new S ($objFA['form_last_name']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFFName)
                ->setLabel (new S ($objFA['form_first_name']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthUsersTableFCountry)
                ->setLabel (new S ($objFA['form_country']));

                // Get the COUNTRIES;
                foreach ($this->STG->getCountries () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v[Settings::$objSettingsCountryTableFIso])
                    ->setValue ($v[Settings::$objSettingsCountryTableFIso])
                    ->setLabel ($v[Settings::$objSettingsCountryTableFPrnt]);
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFCity)
                ->setLabel (new S ($objFA['form_city']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFYM)
                ->setLabel (new S ($objFA['form_ym']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFMSN)
                ->setLabel (new S ($objFA['form_msn']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFICQ)
                ->setLabel (new S ($objFA['form_icq']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFAOL)
                ->setLabel (new S ($objFA['form_aol']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setFileController (new B (TRUE))
                ->setName (self::$objAuthUsersTableFAvatar)
                ->setLabel (new S ($objFA['form_avatar']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFDesc)
                ->setLabel (new S ($objFA['form_description']))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFSignature)
                ->setLabel (new S ($objFA['form_signature']))
                ->setContainerDiv (new B (TRUE));

                // Encrypt the data;
                if ($this->checkFormHasErrors ()->toBoolean () == FALSE) {
                    // Make them passwords OK ...
                    if ($this->checkPOST (self::$objAuthUsersTableFUPass)->toBoolean () == TRUE) {
                        // If they differ ...
                        if ($this->getPOST (self::$objAuthUsersTableFUPass) !=
                        $this->getCurrentUserInfoById (self::$objAuthUsersTableFUPass)) {
                            $this->setPOST (self::$objAuthUsersTableFUPass, $this->getPOST (self::$objAuthUsersTableFUPass)
                            ->encryptIt (sha1 ($this->getPOST (self::$objAuthUsersTableFUPass))));
                            $this->setPOST (new S ('confirmation_password'), $this->getPOST (self::$objAuthUsersTableFUPass));
                        }
                    }

                    $this->setExtraUpdateData (self::$objAuthUsersTableFHash,
                    new S (sha1 (md5 ($this->getPOST (self::$objAuthUsersTableFEML)))));

                    // Send the activation EML ...
                    $objEML = new MAIL;
                    $objEML->doMAIL ($this->getPOST (self::$objAuthUsersTableFEML),
                    $objFA['form_activation_subject'],
                    $this->getHELP (new S ('activationEMLMessage'))
                    ->doToken ('%u', $this->getPOST (self::$objAuthUsersTableFUName))
                    ->doToken ('%a', URL::staticURL (new A (Array (FRONTEND_SECTION_URL,
                    AUTHENTICATION_ACTION_URL, AUTHENTICATION_HASH_URL)), new A (Array (FRONTEND_AUTHENTICATION_URL,
                    AUTHENTICATION_ACTIVATE_URL, sha1 (md5 ($this->getPOST (self::$objAuthUsersTableFEML))))))));
                }

                // Continue;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userCreate':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL ();

                // Check if both password fields have been entered correctly;
                if ($this->checkPOST (self::$objAuthUsersTableFUName)->toBoolean () == TRUE) {
                    // Check username ...
                    if ($this->getPOST (self::$objAuthUsersTableFUName)
                    ->toLength ()->toInt () == 0) {
                        //Check non-empty username, and error if the username is empty;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S (AUTHENTICATION_USERNAME_IS_MANDATORY));
                    } else {
                        // Check USERNAME;
                        if ($this->checkUserNameExists ($this->getPOST (self::$objAuthUsersTableFUName))
                        ->toBoolean () == TRUE)
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S (AUTHENTICATION_USERNAME_ALREADY_EXISTS));

                        // Check EMAIL;
                        if ($this->checkUserMailExists ($this->getPOST (self::$objAuthUsersTableFEML))
                        ->toBoolean () == TRUE)
                        $this->setErrorOnInput (self::$objAuthUsersTableFEML,
                        new S (AUTHENTICATION_EMAIL_ALREADY_EXISTS));
                    }

                    // Check password;
                    if ($this->getPOST (self::$objAuthUsersTableFUPass) != $this
                    ->getPOST (new S ('confirmation_password'))) {
                        // Check password mismatch, and error on it if so;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUPass,
                        new S (AUTHENTICATION_USER_PASSWORDS_DONT_MATCH));
                    }

                    // Check phone number has 10 chars, only if it is required;
                    if (self::PHONE_IS_REQUIRED == 1) {
	                    if ($this->getPOST (self::$objAuthUsersTableFPhone)
	                    ->toLength ()->toInt () != 10) {
	                        $this->setErrorOnInput (self::$objAuthUsersTableFPhone,
	                        new S (AUTHENTICATION_PHONE_TEN_CHARS));
	                    }
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_PROFILE_ADD))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthUsersTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAuthUsersTableFId)
                ->setUploadDirectory (new S ('users/avatars'))
                ->setUploadImageResize (new A (Array (128 => 128)))
                ->setExtraUpdateData (self::$objAuthUsersTableFRegOn, new S ((string) $_SERVER['REQUEST_TIME']));
                if ($this->checkPOST (self::$objAuthUsersTableFUName)->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_PROFILE_ADD))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFUName)
                ->setLabel (new S (AUTHENTICATION_PROFILE_USERNAME))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .-]'))
                ->setInputType (new S ('password'))
                ->setName (self::$objAuthUsersTableFUPass)
                ->setLabel (new S (AUTHENTICATION_PROFILE_PASSWORD))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('confirmation_password'))
                ->setLabel (new S (AUTHENTICATION_PROFILE_PASSWORD_CONFIRM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFEML)
                ->setRegExpType (new S ('preg'))
                ->setRegExpErrMsg (new S (AUTHENTICATION_INVALID_EMAIL))
                ->setPHPRegExpCheck (new S (self::REGEXP_PHP_CHECK_EMAIL))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9.@_-]'))
                ->setLabel (new S (AUTHENTICATION_PROFILE_EMAIL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFPhone)
                ->setLabel (new S (AUTHENTICATION_PROFILE_PHONE))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFLName)
                ->setLabel (new S (AUTHENTICATION_PROFILE_LAST_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFFName)
                ->setLabel (new S (AUTHENTICATION_PROFILE_FIRST_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthUsersTableFCountry)
                ->setLabel (new S (AUTHENTICATION_PROFILE_COUNTRY));

                // Get the COUNTRIES;
                foreach ($this->STG->getCountries () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v[Settings::$objSettingsCountryTableFIso])
                    ->setValue ($v[Settings::$objSettingsCountryTableFIso])
                    ->setLabel ($v[Settings::$objSettingsCountryTableFPrnt]);
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFCity)
                ->setLabel (new S (AUTHENTICATION_PROFILE_CITY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthUsersTableFUGId)
                ->setLabel (new S (AUTHENTICATION_PROFILE_GROUP));

                // Get the groups of the user;
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v['id'])->setValue ($v['id'])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) $v['depth']->toString ()) . $v['name']));
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFYM)
                ->setLabel (new S (AUTHENTICATION_PROFILE_YM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFMSN)
                ->setLabel (new S (AUTHENTICATION_PROFILE_MSN))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFICQ)
                ->setLabel (new S (AUTHENTICATION_PROFILE_ICQ))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFAOL)
                ->setLabel (new S (AUTHENTICATION_PROFILE_AOL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setFileController (new B (TRUE))
                ->setName (self::$objAuthUsersTableFAvatar)
                ->setLabel (new S (AUTHENTICATION_PROFILE_AVATAR))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFDesc)
                ->setLabel (new S (AUTHENTICATION_PROFILE_DESCRIPTION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFSignature)
                ->setLabel (new S (AUTHENTICATION_PROFILE_SIGNATURE))
                ->setContainerDiv (new B (TRUE));

                // Encrypt the data;
                if ($this->checkFormHasErrors ()->toBoolean () == FALSE) {
                    if ($this->checkPOST (self::$objAuthUsersTableFUPass)->toBoolean () == TRUE) {
                    	$this->setPOST (self::$objAuthUsersTableFUPass, $this->getPOST (self::$objAuthUsersTableFUPass)
                    	->encryptIt (sha1 ($this->getPOST (self::$objAuthUsersTableFUPass))));
                    	$this->setPOST (new S ('confirmation_password'), $this->getPOST (self::$objAuthUsersTableFUPass));
                    }
                }

                // Continue;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userEdit':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Check if both password fields have been entered correctly;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Check username ...
                    if ($this->getPOST (self::$objAuthUsersTableFUName)
                    ->toLength ()->toInt () == 0) {
                        //Check non-empty username, and error if the username is empty;
                        $this->setErrorOnInput (self::$objAuthUsersTableFUName,
                        new S (AUTHENTICATION_USERNAME_IS_MANDATORY));
                    }

                    // Check password mismatch;
                    if ($this->getPOST (self::$objAuthUsersTableFUPass) == $this
                    ->getUserInfoById ($_GET[ADMIN_ACTION_ID], self::$objAuthUsersTableFUPass) &&
                    $this->getPOST (new S ('confirmation_password')) == $this->getPOST (self::$objAuthUsersTableFUPass)) {
                        // Unset ...
                        $this->unsetPOST (self::$objAuthUsersTableFUPass);
                    } else {
                        if ($this->getPOST (self::$objAuthUsersTableFUPass) != $this
                        ->getPOST (new S ('confirmation_password'))) {
                            $this->setErrorOnInput (self::$objAuthUsersTableFUPass,
                            new S (AUTHENTICATION_USER_PASSWORDS_DONT_MATCH));
                        }
                    }

                    // Check phone number has 10 chars, only if it's required;
                    if (self::PHONE_IS_REQUIRED == 1) {
	                    if ($this->getPOST (self::$objAuthUsersTableFPhone)
	                    ->toLength ()->toInt () != 10) {
	                        $this->setErrorOnInput (self::$objAuthUsersTableFPhone,
	                        new S (AUTHENTICATION_PHONE_TEN_CHARS));
	                    }
                    }
                }

                // Get AJAX;
                $this->getAjaxErrors ();

                // Make the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_PROFILE_EDIT))
                ->setSQLAction (new S ('update'))
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUploadDirectory (new S ('users/avatars'))
                ->setUploadImageResize (new A (Array (128 => 128)))
                ->setTableName (self::$objAuthUsersTable)
                ->setUpdateField (self::$objAuthUsersTableFId)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_PROFILE_EDIT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE));

                // ONLY if we're NOT the BIG MAN, we cand de-activate;
                if ((int) $this->getUserInfoById ($_GET[ADMIN_ACTION_ID], self::$objAuthUsersTableFId)->toString () != 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objAuthUsersTableFActivated)
                    ->setContainerDiv (new B (TRUE))
                    ->setLabel (new S (AUTHENTICATION_PROFILE_ACTIVATED))
                    ->setInputType (new S ('option'))
                    ->setName (new S ('yes'))
                    ->setValue (new S ('Y'))
                    ->setLabel (new S (AUTHENTICATION_PROFILE_ACTIVATED_YES))
                    ->setInputType (new S ('option'))
                    ->setName (new S ('no'))
                    ->setValue (new S ('N'))
                    ->setLabel (new S (AUTHENTICATION_PROFILE_ACTIVATED_NO));
                }

                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFUName)
                ->setLabel (new S (AUTHENTICATION_PROFILE_USERNAME))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .-]'))
                ->setInputType (new S ('password'))
                ->setName (self::$objAuthUsersTableFUPass)
                ->setValue ($this->getUserInfoById ($_GET[ADMIN_ACTION_ID], self::$objAuthUsersTableFUPass))
                ->setLabel (new S (AUTHENTICATION_PROFILE_PASSWORD))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('confirmation_password'))
                ->setValue ($this->getUserInfoById ($_GET[ADMIN_ACTION_ID], self::$objAuthUsersTableFUPass))
                ->setLabel (new S (AUTHENTICATION_PROFILE_PASSWORD_CONFIRM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFEML)
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9.@_-]'))
                ->setLabel (new S (AUTHENTICATION_PROFILE_EMAIL))
                ->setContainerDiv (new B (TRUE))
                ->setRegExpType (new S ('preg'))
                ->setRegExpErrMsg (new S (AUTHENTICATION_INVALID_EMAIL))
                ->setPHPRegExpCheck (new S (self::REGEXP_PHP_CHECK_EMAIL))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFPhone)
                ->setLabel (new S (AUTHENTICATION_PROFILE_PHONE))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFLName)
                ->setLabel (new S (AUTHENTICATION_PROFILE_LAST_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFFName)
                ->setLabel (new S (AUTHENTICATION_PROFILE_FIRST_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthUsersTableFCountry)
                ->setLabel (new S (AUTHENTICATION_PROFILE_COUNTRY));

                // Countries ...
                foreach ($this->STG->getCountries () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v[Settings::$objSettingsCountryTableFIso])
                    ->setValue ($v[Settings::$objSettingsCountryTableFIso])
                    ->setLabel ($v[Settings::$objSettingsCountryTableFPrnt]);
                }

                // Continue ...
                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFCity)
                ->setLabel (new S (AUTHENTICATION_PROFILE_CITY))
                ->setContainerDiv (new B (TRUE));

                // ONLY if we're not the BIG MAN, can we change the group;
                if ((int) $this->getUserInfoById ($_GET[ADMIN_ACTION_ID], self::$objAuthUsersTableFId)->toString () != 1) {
                    $this->setInputType (new S ('select'))
                    ->setContainerDiv (new B (TRUE))
                    ->setName (self::$objAuthUsersTableFUGId)
                    ->setLabel (new S (AUTHENTICATION_PROFILE_GROUP));
                    foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                        $this->setInputType (new S ('option'))->setName ($v['id'])->setValue ($v['id'])
                        ->setLabel (new S (str_repeat ('--' . _SP, (int) $v['depth']->toString ()) . $v['name']));
                    }
                }

                // Execute the form, make it happen;
                $this->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFYM)
                ->setLabel (new S (AUTHENTICATION_PROFILE_YM))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFMSN)
                ->setLabel (new S (AUTHENTICATION_PROFILE_MSN))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFICQ)
                ->setLabel (new S (AUTHENTICATION_PROFILE_ICQ))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthUsersTableFAOL)
                ->setLabel (new S (AUTHENTICATION_PROFILE_AOL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setFileController (new B (TRUE))
                ->setName (self::$objAuthUsersTableFAvatar)
                ->setLabel (new S (AUTHENTICATION_PROFILE_AVATAR))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFDesc)
                ->setLabel (new S (AUTHENTICATION_PROFILE_DESCRIPTION))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthUsersTableFSignature)
                ->setLabel (new S (AUTHENTICATION_PROFILE_SIGNATURE))
                ->setContainerDiv (new B (TRUE));

                // Encrypt the data;
                if ($this->checkFormHasErrors ()->toBoolean () == FALSE) {
                    if ($this->checkPOST (self::$objAuthUsersTableFUPass)->toBoolean () == TRUE) {
                    	$this->setPOST (self::$objAuthUsersTableFUPass, $this->getPOST (self::$objAuthUsersTableFUPass)
                    	->encryptIt (sha1 ($this->getPOST (self::$objAuthUsersTableFUPass))));
                    	$this->setPOST (new S ('confirmation_password'), $this->getPOST (self::$objAuthUsersTableFUPass));
                    }
                }
                // Continue;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Check if it's administrator user, and don't delete it;
                if ((int) $_GET[ADMIN_ACTION_ID]->toString () == 1) {
                    // Do not permit the deletion of the administrator user;
                    self::$objAdministration->setErrorMessage (new S (CANNOT_DELETE_ADMINISTRATOR_USER), $objURLToGoBack);
                } else {
                    // Check to see if there are any zone mappings, for the current zone;
                    if ($this->_Q (_QS ('doSELECT')
                    ->doToken ('%what', new S ('*'))
                    ->doToken ('%table', self::$objAuthZoneMTable)
                    ->doToken ('%condition', new S ('WHERE %objAuthZoneMTableFUGId = "%Id" AND %objAuthZoneMTableFIUG = "N"'))
                    ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))->doCount ()->toInt () != 0) {
                        // Do not delete users that have specific mappings;
                        self::$objAdministration->setErrorMessage (new S (CANNOT_DELETE_MAPPED_USERS), $objURLToGoBack);
                    } else {
                    	// Go further ... and erase the user ...
                        $this->_Q (_QS ('doDELETE')
                        ->doToken ('%table', self::$objAuthUsersTable)
                        ->doToken ('%condition', new S ('%objAuthUsersTableFId = "%Id"'))
                        ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                        // Do a redirect, and get the user back where he belongs;
                        $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                    }
                }
                break;

            case 'userSearch':
                // Get some predefines;
                if (isset ($_GET[ADMIN_ACTION_SEARCH]))    { $objSearchWas = $_GET[ADMIN_ACTION_SEARCH]; }
                else { $objSearchWas = new S; }
                if (isset ($_GET[ADMIN_ACTION_BY]))        { $objSearchBy = $_GET[ADMIN_ACTION_BY]; }
                else { $objSearchBy = new S; }

                // Do some work;
                if ($this->checkPOST (new S ('search_submit'))->toBoolean () == TRUE) {
                    if ($this->getPOST (new S ('search_user_by'))->toLength ()->toInt () == 0) {
                        if (isset ($_GET[ADMIN_ACTION_SEARCH])) {
                            // Erase search terms ...
                            $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SEARCH,
                            ADMIN_ACTION_BY))), new S ('Location'));
                        }

                        // Set an error, notify me ...
                        $this->setErrorOnInput (new S ('search_user_by'),
                        new S (AUTHENTICATION_SEARCH_FIELD_IS_EMPTY));
                        // Unset the post ...
                        $this->unsetPOST ();
                    } else {
                        // Get what to search and where ...
                        $objWhatToSearch 	= $this->getPOST (new S ('search_user_by'));
                        $objWhereToSearch 	= $this->getPOST (new S ('search_user_field'));

                        // And go there ...
                        $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_ACTION_SEARCH, ADMIN_ACTION_BY)),
                        new A (Array ($objWhatToSearch, $objWhereToSearch))), new S ('Location'));
                    }
                }

                $objWasSelected = new A (Array (new B ($objSearchBy == AUTHENTICATION_PROFILE_USERNAME ? TRUE : FALSE),
                new B ($objSearchBy == AUTHENTICATION_PROFILE_EMAIL ? TRUE : FALSE),
                new B ($objSearchBy == AUTHENTICATION_PROFILE_GROUP ? TRUE : FALSE)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_SEARCH_USER_BY))
                ->setName ($objFormToRender)
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (new S ('search_user_by'))
                ->setvalue ($objSearchWas)
                ->setLabel (new S (AUTHENTICATION_SEARCH_USER_BY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('search_user_field'))
                ->setContainerDiv (new B (TRUE))
                ->setLabel (new S (AUTHENTICATION_SEARCH_USER_IN))
                ->setInputType (new S ('option'))
                ->setName (new S ('username_search'))
                ->setValue (new S (AUTHENTICATION_PROFILE_USERNAME))
                ->setLabel (new S (AUTHENTICATION_PROFILE_USERNAME))
                ->setSelected ($objWasSelected[0])
                ->setInputType (new S ('option'))
                ->setName (new S ('email_search'))
                ->setValue (new S (AUTHENTICATION_PROFILE_EMAIL))
                ->setLabel (new S (AUTHENTICATION_PROFILE_EMAIL))
                ->setSelected ($objWasSelected[1])
                ->setInputType (new S ('option'))
                ->setName (new S ('group_serach'))
                ->setValue (new S (AUTHENTICATION_PROFILE_GROUP))
                ->setLabel (new S (AUTHENTICATION_PROFILE_GROUP))
                ->setSelected ($objWasSelected[2])
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setValue (new S (AUTHENTICATION_SEARCH_USER_BY))
                ->setName (new S ('search_submit'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'groupCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work ...
                if ($this->checkPOST (new S ('categories_show_all'))->toBoolean () == TRUE) {
                    // Redirect to proper ...
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_SHOW_ALL)),
                    new A (Array ('1'))), new S ('Location'));
                }

                // Do some work;
                if ($this->checkPOST (new S ('submit_add_group'))->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    if ($this->getPOST (new S ('group'))->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it. We don't allow empty group names;
                        $this->setErrorOnInput (new S ('group'),
                        new S (GROUP_NAME_CANNOT_BE_EMPTY));

                        // Set to memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($this->getPOST (new S ('group')))->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (new S ('group'),
                            new S (GROUP_ALREADY_EXISTS));

                            // Set to memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;
                        // Switch ...
                        switch ($this->getPOST (new S ('group_as_what'))) {
                            case AUTHENTICATION_GROUP_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::FIRST_CHILD);
                                break;

                            case AUTHENTICATION_GROUP_LAST_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::LAST_CHILD);
                                break;

                            case AUTHENTICATION_GROUP_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::PREVIOUS_BROTHER);
                                break;

                            case AUTHENTICATION_GROUP_NEXT_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::NEXT_BROTHER);
                                break;
                        }

                        // Add the node;
                        self::$objMPTT->mpttAddNode ($this->getPOST (new S ('group')),
                        $this->getPOST (new S ('group_parent_or_bro')), $objAddNodeAS);

                        // Do a redirect back;
                        $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (ADD_NEW_GROUP_OF_USERS))
                ->setName (new S ($objFormToRender))
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (new S (AUTHENTICATION_SHOW_ALL_CATEGORIES))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('submit_add_group'))
                ->setValue (new S (AUTHENTICATION_ADD_GROUP))
                ->setInputType (new S ('text'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('group'))
                ->setLabel (new S (AUTHENTICATION_GROUP_NAME_LABEL))
                ->setRegExpType (new S ('ereg'))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('group_as_what'))
                ->setLabel (new S (AUTHENTICATION_AS_A))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_child'))
                ->setLabel (new S (AUTHENTICATION_GROUP_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_child'))
                ->setLabel (new S (AUTHENTICATION_GROUP_LAST_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_brother'))
                ->setLabel (new S (AUTHENTICATION_GROUP_BROTHER))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_brother'))
                ->setLabel (new S (AUTHENTICATION_GROUP_NEXT_BROTHER))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('group_parent_or_bro'))
                ->setLabel (new S (AUTHENTICATION_OF_GROUP));

                // Do a foreach on the already existing groups;
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v['name'])->setValue ($v['name'])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) $v['depth']->toString ()) . $v['name']));
                }

                // Execute the form;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'groupEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do validation and error on it if something goes wrong;
                if ($this->checkPOST (self::$objAuthGroupTableFName)->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    if ($this->getPOST (self::$objAuthGroupTableFName)->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it. We don't allow empty group names;
                        $this->setErrorOnInput (self::$objAuthGroupTableFName,
                        new S (GROUP_NAME_CANNOT_BE_EMPTY));

                        // Set to memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($this->getPOST (self::$objAuthGroupTableFName))
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (self::$objAuthGroupTableFName,
                            new S (GROUP_ALREADY_EXISTS));

                            // Set to memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();
                } else {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                }


                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthGroupTable)
                ->setUpdateField (self::$objAuthGroupTableFId)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID]);
                if ($this->checkPOST (self::$objAuthGroupTableFName)->toBoolean () == TRUE &&
                $objFormHappened->toBoolean () == FALSE) {
                    // Set the URL ...
                    $this->setExtraUpdateData (self::$objAuthGroupTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objAuthGroupTableFName)))
                    ->setRedirect ($objURLToGoBack);
                }

                // Continue ...
                $this->setFieldset (new S (AUTHENTICATION_EDIT_GROUP))
                ->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_EDIT_GROUP))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setName (self::$objAuthGroupTableFName)
                ->setLabel (new S (AUTHENTICATION_GROUP_NAME_LABEL))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'groupErase':
                // The URL to go back too;
                $objNodeHasKids = new B (FALSE);
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Check if it's administrator group;
                if ((int) $_GET[ADMIN_ACTION_ID]->toString () == 1) {
                	// Show the ERRORs;
                    self::$objAdministration->setErrorMessage (new S (CANNOT_ERASE_AUTHENTICATION_GROUP), $objURLToGoBack);
                } else {
                    // Check to see if there are any zone mappings;
                    if ($this->_Q (_QS ('doSELECT')
                    ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthZoneMTable)
                    ->doToken ('%condition', new S ('WHERE %objAuthZoneMTableFUGId = "%Id"
                    AND %objAuthZoneMTableFIUG = "Y"'))->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))
                    ->doCount ()->toInt () != 0) {
                        // Do not delete groups with users in them;
                        self::$objAdministration->setErrorMessage (new S (CANNOT_DELETE_MAPPED_GROUPS), $objURLToGoBack);
                    } else {
                        // Do erase the group node from the table;
                        self::$objMPTT->mpttRemoveNode ($this->getGroupInfoById ($_GET[ADMIN_ACTION_ID],
                        self::$objAuthGroupTableFName));

                        // Redirect back;
                        $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                    }
                }
                // BK;
                break;

            case 'groupMove':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_TO, ADMIN_ACTION_TYPE)));
                // Get names, as they are unique;
                $objThatIsMoved = $this->getGroupInfoById ($_GET[ADMIN_ACTION_ID], self::$objAuthGroupTableFName);
                $objWhereToMove = $this->getGroupInfoById ($_GET[ADMIN_ACTION_TO], self::$objAuthGroupTableFName);

                // Get the node subtree, that's move, make sure the node we move to ain't a child;
                $objMovedNodeSubTree = self::$objMPTT->mpttGetTree ($objThatIsMoved);

                // Memorize;
                $objIsChild = new B (FALSE);
                foreach ($objMovedNodeSubTree as $k => $v) {
                     if ($v[self::$objMPTT->objNameOfNode] == $objWhereToMove) {
                         $objIsChild->switchType ();
                     }
                }

                // Check if it's a child or not;
                if ($objIsChild->toBoolean () == TRUE) {
                    // Set an error message;
                    self::$objAdministration->setErrorMessage (new S (AUTHENTICATION_GROUP_MOVED_TO_CHILD),
                    $objURLToGoBack);
                } else {
                    // Move nodes;
                    self::$objMPTT->mpttMoveNode ($objThatIsMoved,
                    $objWhereToMove, $_GET[ADMIN_ACTION_TYPE]);
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                break;

            case 'zoneCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    # Check that the zone name is not empty!
                    if ($this->getPOST (self::$objAuthZonesTableFName)->toLength ()->toInt () == 0)
                    $this->setErrorOnInput (self::$objAuthZonesTableFName,
                    new S (AUTHENTICATION_ZONE_NAME_CANNOT_BE_EMPTY));

                    // Get AJAX;
                    $this->getAjaxErrors ();
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_ADD_ZONE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthZonesTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAuthZonesTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setValue (new S (AUTHENTICATION_ADD_ZONE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthZonesTableFName)
                ->setLabel (new S (AUTHENTICATION_ZONE_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 ]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthZonesTableFPrice)
                ->setLabel (new S (AUTHENTICATION_ZONE_PRICE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZonesTableFDesc)
                ->setLabel (new S (AUTHENTICATION_ZONE_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneEdit':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some validation, beforehand;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    if ($this->checkPOST (self::$objAuthZonesTableFName)->toBoolean () == TRUE) {
                        // Check that the zone name is not empty!
                        if ($this->getPOST (self::$objAuthZonesTableFName)->toLength ()->toInt () == 0)
                        $this->setErrorOnInput (self::$objAuthZonesTableFName,
                        new S (AUTHENTICATION_ZONE_NAME_CANNOT_BE_EMPTY));
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();
                }

                // Add some restrictions ...
                if ($this->_Q (_QS ('doSELECT')
                ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objAuthZoneMTable)
                ->doToken ('%condition', new S ('WHERE %objAuthZoneMTableFZId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))->doCount ()->toInt () != 0) {
                    // The name should not be changed, due to mapping;
                    $objNameChangeDisabled = new B (TRUE);
                } else {
                    // The name can be changed;
                    $objNameChangeDisabled = new B (FALSE);
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_EDIT_ZONE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthZonesTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objAuthZonesTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setContainerDiv (new B (TRUE))
                ->setValue (new S (AUTHENTICATION_EDIT_ZONE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthZonesTableFName)
                ->setLabel (new S (AUTHENTICATION_ZONE_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setReadOnly ($objNameChangeDisabled)
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 .]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objAuthZonesTableFPrice)
                ->setLabel (new S (AUTHENTICATION_ZONE_PRICE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objAuthZonesTableFDesc)
                ->setLabel (new S (AUTHENTICATION_ZONE_DESCRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Check to see if there are any zone mappings, for the current zone;
                $objSQLCondition = new S ('WHERE %s = %i');

                // Erase it;
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objAuthZonesTable)
                ->doToken ('%condition', new S ('%objAuthZonesTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Redirect the user back;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'zoneMappingCreateForGroups':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_ADD_ACL))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthZoneMTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAuthZoneMTableFId)
                ->setExtraUpdateData (self::$objAuthZoneMTableFIUG, new S ('Y'))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_ADD_ACL))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFUGId)
                ->setLabel (new S (AUTHENTICATION_ACL_ENTITY));

                // Get the groups;
                foreach (self::$objMPTT->mpttGetTree () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objAuthGroupTableFId])
                    ->setValue ($v[self::$objAuthGroupTableFId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) $v['depth']
                    ->toString ()) . $v[self::$objAuthGroupTableFName]));
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFZId)
                ->setLabel (new S (AUTHENTICATION_ZONE_NAME));

                // Get the zones;
                foreach ($this->getZones (NULL) as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objAuthZonesTableFId])
                    ->setValue ($v[self::$objAuthZonesTableFId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) $v['depth']
                    ->toString ()) . $v[self::$objAuthZonesTableFName]));
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFAorD)
                ->setLabel (new S (AUTHENTICATION_ACL_ACCESS_TYPE))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_allow'))
                ->setValue (new S ('A'))
                ->setLabel (new S (AUTHENTICATION_ACL_ALLOWED))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_deny'))
                ->setValue (new S ('D'))
                ->setLabel (new S (AUTHENTICATION_ACL_DENIED))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneMappingCreateForUsers':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_ADD_ACL))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthZoneMTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objAuthZoneMTableFId)
                ->setExtraUpdateData (self::$objAuthZoneMTableFIUG, new S ('N'))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setValue (new S (AUTHENTICATION_ADD_ACL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFUGId)
                ->setLabel (new S (AUTHENTICATION_ACL_ENTITY));

                // Get the users;
                foreach ($this->getUsers () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($v[self::$objAuthUsersTableFId])
                    ->setValue ($v[self::$objAuthUsersTableFId])
                    ->setLabel ($v[self::$objAuthUsersTableFUName]);
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFZId)
                ->setLabel (new S (AUTHENTICATION_ZONE_NAME));

                // Get zones;
                foreach ($this->getZones (NULL) as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objAuthZonesTableFId])
                    ->setValue ($v[self::$objAuthZonesTableFId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int)
                    $v['depth']->toString ()) . $v[self::$objAuthZonesTableFName]));
                }

                // Continue;
                $this->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFAorD)
                ->setLabel (new S (AUTHENTICATION_ACL_ACCESS_TYPE))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_allow'))
                ->setValue (new S ('A'))
                ->setLabel (new S (AUTHENTICATION_ACL_ALLOWED))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_deny'))
                ->setValue (new S ('D'))
                ->setLabel (new S (AUTHENTICATION_ACL_DENIED))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneMappingEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (AUTHENTICATION_EDIT_ACL))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objAuthZoneMTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objAuthZoneMTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_EDIT_ACL))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objAuthZoneMTableFAorD)
                ->setLabel (new S (AUTHENTICATION_ACL_ACCESS_TYPE))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_allow'))
                ->setValue (new S ('A'))
                ->setLabel (new S (AUTHENTICATION_ACL_ALLOWED))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_deny'))
                ->setValue (new S ('D'))
                ->setLabel (new S (AUTHENTICATION_ACL_DENIED))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneMappingErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Erase it;
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objAuthZoneMTable)
                ->doToken ('%condition', new S ('%objAuthZoneMTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'configurationEdit':
                // Set some requirements;
                $objURLToGoBack = URL::rewriteURL ();

                // Do form validation;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // The URL to go back too;
                    $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)),
                    new A (Array ($this->getPOST (new S ('what')))));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S ('Do'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S ('Choose'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-register_page_content'))
                ->setValue (new S ('configurationEdit-register_page_content'))
                ->setLabel (new S (AUTHENTICATION_REG_PAGE_FORM_CONTENT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-register_ok_page_content'))
                ->setValue (new S ('configurationEdit-register_ok_page_content'))
                ->setLabel (new S (AUTHENTICATION_REG_PAGE_FORM_SUCCESS_CONTENT))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-activation_page_content'))
                ->setValue (new S ('configurationEdit-activation_page_content'))
                ->setLabel (new S (AUTHENTICATION_REG_PAGE_FORM_ACTIVATE_CONTENT))
                ->setInputType (new S ('option'))
                ->setName (new S ('configurationEdit-activation_not_ok_page_content'))
                ->setValue (new S ('configurationEdit-activation_not_ok_page_content'))
                ->setLabel (new S (AUTHENTICATION_REG_PAGE_FORM_ACTIVATE_NOTOK))
                ->setFormEndAndExecute (new B (TRUE));
            	break;

            case 'configurationEdit-register_page_content':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('authentication_page_register_message'))
                ->setLabel (new S (AUTHENTICATION_CONFIG_DEFAULT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setValue ($this->getConfigKey (new S ('authentication_page_register_message')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-register_ok_page_content':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('authentication_page_register_ok_message'))
                ->setLabel (new S (AUTHENTICATION_CONFIG_DEFAULT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setValue ($this->getConfigKey (new S ('authentication_page_register_ok_message')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-activation_page_content':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('authentication_page_activation_ok_message'))
                ->setLabel (new S (AUTHENTICATION_CONFIG_DEFAULT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setValue ($this->getConfigKey (new S ('authentication_page_activation_ok_message')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit-activation_not_ok_page_content':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    foreach ($this->getPOST () as $k => $v) {
                        $this->setConfigKey (new S ($k), $v);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (AUTHENTICATION_MANAGE_CONFIGURATION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (new S ('authentication_page_activation_not_ok_message'))
                ->setLabel (new S (AUTHENTICATION_CONFIG_DEFAULT))
                ->setTinyMCETextarea (new B (TRUE))
                ->setValue ($this->getConfigKey (new S ('authentication_page_activation_not_ok_message')))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
