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

	#################################################
	This program is provided by S.C. KIT Software CAZ S.R.L., with it's
	HQ based in Romania. Any modules/extensions/plugins used by this here
	framework are properties of their respective owners.

	If you feel the need to see this framework get extended, and want to
	donate to its development please use the website: www.kitsoftware.ro
	for information on how to contact us. Any funds received from the
	community will be put in the development of the framework, and proof
	will be available for anyone interested in it.

	Along the official website of the company, we at KIT Software have put
	up a dedicated website for this framework at www.raphpframework.ro,
	where you can find information on getting the latest source-code and
	database schema for the current release (usually by SVN);

	If you ever need to consult the SVN trunk of the project, you can either
	use a SVN compatible editor (Eclipse) or any SVN command line or GUI
	client, by checking out: http://raphpframework.ro/svn/RA/trunk - where
	SVN commit access is only granted to the developers but anonymous read
	access is granted to ALL who wish to have it ...

    ###### RA PHP Framework :: CONFIGURATION ########
    @package RAPHPFramework::Configuration
	@version 0.3 (GPL enabled version of the platform) ...;
    @author C.Z.A. (Catalin Zamfir Alexandru) <office [at] kitsoftware.ro> && all contributors ...;
	@copyright S.C. KIT Software CAZ S.R.L. | Released under the GPL;
*/
    ################################ Framework SPECIFIC Configuration! AFFECTS ALL CODE! ###################################
    # Define some configuraiton data like: MAIL_FROM, SKIN, LANGUAGE, DATE_STRING, DATE_TIMEZONE which are just defaults;
    define ('MAIL_FROM', 'office@24up.ro');          		        # What should PHP MAIL () add to the 'From: ' header;
    define ('SKIN', 'default');                                     # What SKIN in SKIN_DIR_DIR to use (changeable default);
    define ('LANGUAGE', 'en_GB');                                   # What LANGUAGE in LANGUAGE_DIR to use (changeable default);
    define ('DATE_STRING', 'F j, Y, g:i a');                        # What date format is to be used (see PHP date ());
    define ('DATE_TIMEZONE', 'Europe/Bucharest');                   # What's the timezone of the server, for date processing;

    ################################ DO NOT EDIT BELOW THIS LINE, UNLESS YOU'RE A DEVELOPER ################################
    ################################ DO NOT EDIT BELOW THIS LINE, UNLESS YOU'RE A DEVELOPER ################################
    ################################ DO NOT EDIT BELOW THIS LINE, UNLESS YOU'RE A DEVELOPER ################################
    ################################ THANK YOU! ############################################################################
    ##### WARNING: That's all you need to edit unless you're a developer! #####
    ##### If you are a developer, give great attention to the configuration options below #####
    ##### RA PHP Framework :: Structure, so you can rename directories, but keep the same functionality ...
    define ('LANGUAGE_DIR', 'int');                                 # Framework: Includes directory for language files;
    define ('INCLUDE_DIR', 'inc');                                  # Framework: Includes directory for .php files;
    define ('IMAGE_DIR', 'img');                                    # Framework: Images directory, need by the platform;
    define ('UPLOAD_DIR', 'upd');                                   # Framework: Upload directory, for the form generator;
    define ('SKIN_DIR_DIR', 'skn');                                 # Framework: Skin directory, where to store .tp files;
    define ('SKIN_JSS_DIR', 'jss');                                 # Framework: Skin JSS directory, in skin, for jss files;,
    define ('SKIN_CSS_DIR', 'css');                                 # Framework: Skin CSS directory, in skin, for css files;
    define ('SKIN_IMG_DIR', 'img');                                 # Framework: Skin IMG directory, in skin, for img files;
    define ('JAVASCRIPT_DIR', 'jss');                               # Framework: Skin JSS directory, in skin, for jss files;
    define ('PLUGIN_DIR', 'pgn');                                   # Framework: Auto-loading plugins (GeSHi, Firebug, etc.);
    define ('CACHE_DIR', 'cch');                                    # Framework: Cache directory, where cache_* files are stored;
    define ('LOG_DIR', 'log');                                      # Framework: Log directory, errors that don't reach browser;
    define ('FORM_TP_DIR', 'frm');                                  # Framework: Forms directory, generic TPs to handle forms;
    define ('ADMIN_DIR', 'adm');                                    # Framework: Administrative backend; Project specific;
    define ('MOD_DIR', 'mod');                                      # Framework: Mod directory, for 'Independent Code';
    define ('ERR_DIR', 'err');                                      # Framework: Error directory, for some specific errors;
    define ('TEMP_DIR', 'tmp');                                     # Framework: File temporary directory;
    define ('CFG_DIR', 'cfg');                                      # Framework: Configuration directory;
    define ('DEVELOPER_DIR', 'dev');                                # Framework: Developer code, for platform hooks;
    define ('DEVELOPER_OTHERS', 'oth');                             # Framework: Developer OTHERS dir;
    define ('DEVELOPER_HEADER', 'hdr');                             # Framework: Developer HEADER dir;
    define ('DEVELOPER_FOOTER', 'ftr');                             # Framework: Developer FOOTER dir;
    define ('CSS_EXTENSION', '.css');                               # Framework: Extension for .css files;
    define ('JSS_EXTENSION', '.js');                                # Framework: Extension for .js files;
    define ('TPL_EXTENSION', '.tp');                                # Framework: Extension for .tp files; (templates);
    define ('HLP_EXTENSION', '.qst');                               # Framework: HELP files, for big texts in small places;
    define ('SCH_EXTENSION', '.schema');                            # Framework: Database SCHEMA file extension;
    define ('HTM_EXTENSION', '.html');                              # Framework: HTML extension, or mailed string attachments;
    define ('HTM_MIME_TYPE', 'text/html');                          # Framework: HTML mime type, for string attachments;

    ### -- ### Do Not Edit Below This Line ### --- ### Unless you know what you're doing ### --- ###
    define ('_S', '/');                                             # _S        = System Slash (PHP constant DIRECTORY_SEPARATOR);
    define ('_WS', '/');                                            # _WS       = Web Slash. It's always '/' no matter what;
    define ('_NONE', '');                                           # _NONE     = An empty string, used everywhere;
    define ('_S_WIN', '\\');                                        # _S_WIN    = Define the Windows (32) System Slash;

    ################# These are hidden, because they're usually configured by a developer! #################################
    ################# If you're going to alter these configuration options PLEASE PAY SPECIAL ATTENTION ... ################
    # Main DEBUG function. For simple use; I'm kidding, but really needed a var_dumper with <pre> tags, defined globally;
    function err ($r) {
        // Do some house-cleaning;
        if (isset ($_SESSION['POST'])) {
            // Do clean ...
            unset ($_SESSION['POST']);
        }

        if (isset ($_SESSION['FILES'])) {
            // Do clean ...
            unset ($_SESSION['FILES']);
        }

        // Echo & Exit;
        echo '<pre>', var_dump ($r), '</pre>';
        exit ();
    }

    ################################ Framework BEHAVIOUR Configuration! AFFECTS ALL CODE! ##################################
    ### RA PHP Framework :: Configuration, so we can control the PHP environment with PHP specific code;
    define ('REWRITE_ENGINE', TRUE);                                # If set TRUE: Rewrite Engine will be On (.htaccess);
    define ('ERROR_REPORTING_LEVEL', E_ALL);                        # What ERROR_REPORTING_LEVEL we need. E_ALL by default;
    define ('APACHE_SERVER_SIGNATURE', 'Off');                      # Should apache identify itself if accesing the framework;
    define ('APACHE_OPTIONS', '-Indexes +FollowSymLinks');          # Usually, won't need changing (provided default);
    define ('MEMORY_LIMIT', '1024M');                               # What's the maximum memory limit a script should consume;
    define ('UPLOAD_MAX_FILESIZE', '256M');                         # Set UPLOAD_MAX_FILESIZE to something big;
    define ('POST_MAX_SIZE', '256M');                               # How big the _POST array should be allowed;
    define ('REGISTER_LONG_GPC', 0);                                # FALSE: don't register HTTP_POST/HTTP_GET etc. Performance;
    define ('ASP_TAGS', 0);                                         # Do you actually want to use ASP_TAGS (provided default: 0);
    define ('ZEND_1_COMPATIBLE', 0);                                # Incredible performance gain by turning it off;
    define ('TIDY_CLEAN_OUTPUT', 0);                                # Let HTMLTidy clean your output; Disabled on GZIP/GD2;
    define ('MAX_INPUT_TIME', -1);                                  # The time a PHP script is allowed to parse _POST/_GET arrays;
    define ('IMPLICIT_FLUSH', 0);                                   # Should PHP flush every ouput block, for debugging purposes;
    define ('SCRIPT_TIME_LIMIT', 180);                              # What's the SCRIPT_TIME_LIMIT, after we die () the script on;
    define ('SESSIONCACHEEXPIRE', 60 * 60 * 24 * 30);               # Set SESSIONCACHEEXPIRE to 1 month;
    define ('SESSION_USE_TRANSPARENT_ID', 0);                       # Don't user transparent _SESSION Ids;
    define ('SESSION_COOKIE_LIFETIME', 31556926);                   # Set the cookie associated with a session to expire in 1year;
    define ('DEFAULT_CHARSET', 'utf-8');                            # You speak utf-8 english? No, je n'ais parle pas;
    define ('SHORT_OPEN_TAG', 0);                                   # Do you want to use short-open-tags;
    define ('DISPLAY_ERRORS', 1);                                   # Should always be TRUE. It's the soul of our debugger;
    define ('DISPLAY_STARTUP_ERRORS', 1);                           # Should PHP display startup errors (usually);
    define ('PHP_HTML_ERRORS', 0);                                  # Should PHP output HTML Errors (not really, buggy);
    define ('GESHI', TRUE);                                         # Should we enable GESHI for debug (but of course);
    define ('PHPIZE_JSS_FILES', FALSE);                             # Should we PHPize .jss files; (use checkIfIts in LDO when 1);
    define ('PHPIZE_CSS_FILES', FALSE);                             # Should we PHPize .css files; (use checkIfIts in LDO when 1);
    define ('META_DESCRIPTION_MAX', 250);                           # The default META-description cut-off size, for SEO purposes;

    ### RA PHP Framework :: Code Behaviour;
    # Never set with DEBUG_SESSION && DEBUG_CHECK, generates false-positives. Instead be sure to check your $_SESSION variable;
    # If DEBUG = 0, the debugger will not start. For some fancy debugging, use DEBUG = 1;
    define ('DEBUG', 1);                                            # What DEBUG_LEVEL to have throughout the framework;
    define ('DEBUG_UNDEFINED_MODULES', 0);                          # If to debug UNDEFINED modules at the time;
    define ('SYSTEM_LOAD_MAX', 90);                                 # What's the maximum safe LOAD_AVERAGE reporter by Linux;
    define ('SQL_PERSISTENT_CONNECTION', 0);                        # Should be set to true. Helps not reopening the socket;
    define ('SQL_PREFIX', '_T_');                                   # Prefix to use for parsing an SQL Query in the SQL object;
    define ('CLOSE_SESSION_ON_OBJECT_SCOPE', FALSE);                # Completly DESTROY _SESSION on object destruction (false);
    define ('CACHE_TIMEOUT', 3600);                                	# When to expire them cache_ files, by default 1 hour;
    define ('OB_GZIP', FALSE);                                      # Enable OutputBuffering GZip;
    define ('OB_GZIP_LEVEL', 6);                                    # OB_GZIP_LEVEL = 6 is a good place to start;
    define ('OB_GZIP_TYPE', 0);                                     # Define what kind of output, GZipped (0) or Deflated (1);

    ### RA PHP Framework :: Code Shortcuts & Other Definitions;
    # WTF (what the fu**): use to make code shorter, to stop using '' for some system characters and other specific uses;
    define ('_N_', PHP_EOL);                                        # Define NEW_LINE character (default old: "\n" or PHP_EOL);
    define ('_T_', "\t");                                           # Define TAB character;
    define ('_PIPE', '|');                                          # Define PIPE, used mainly when exploding ();
    define ('_U', '_');                                             # Define _, for simple inclusion;
    define ('_SP', ' ');                                            # Define _SP, as an empty space;
    define ('_CL', ': ');                                           # Define _CP, as a ': ';
    define ('_DC', '::');                                           # Define _DC, as a function call separator;
    define ('_DCSP', ' :: ');                                       # Define a separator (used mainly in <title>);
    define ('_ANY', '.*');                                          # Define a regex _ANY string;
    define ('_DOT',	'.');											# Define the DOT;
    define ('_QOT', '\'');                                          # Define the single quote;
    define ('_DTE', '...');                                         # Define the 3 dots extension (used with sub_str);
    define ('AUTO_PREPEND_FILE', '00_CFG.php');                     # What file needs to be auto-prepended;
    define ('AUTO_APPEND_FILE', '99_GBC.php');                      # What file needs to be auto-appended;
    define ('X_HTTP_APACHE', 'application/x-httpd-php');            # What APACHE worker to use for .css/.js files?!
    define ('ERBGR', 'err/error_background.jpg');                   # What file should be used for the error background;
    define ('ERPIX', 'err/error_pixel_code.png');                   # What file should be used for the error code;
    define ('ERPXL', 'err/error_pixel.png');                        # What file should be used for the error code pixels;
    define ('PHP_ERROR_LOG', 'ERROR_LOG_FROM_PHP');                 # What's the name of the PHP error_log;
    define ('DEFAULT_ERROR_CSS_CLASS', 'RA_err_msg_input');         # What's the default ERROR_CSS_CLASS;
    define ('RA_SCHEMA_HASH_TAG', '___RA_SCHEMA_HASH_TAG_');        # What's the schema _HASH_TAG_ used to separate SQLs;

    ################################ Google/BING/Yahoo Webmaster TOOLS ... #################################################
    define ('SECRET', 'f4e4a93a6318c17465419ad5ecfed854');          # What's your Yahoo Shared Secret, if you need one ...
    define ('APPID', 'M0_eEC3IkY0grMrVNuifywM1tzbSGZB_VPnHFw8-');   # What's your Yahoo Application Id if you need one ...

    // Google, Bing, Yahoo;
    define ('GOOGLE_WBM_KEY',                                       # Wbm KEY: G;
    'poLcIB03OWC0GgEso2d2DoE4g4zlaTcdSnRCPW7zLIA=');                # Wbm VAR: G;
    define ('BING___WBM_KEY',                                       # Wbm KEY: B;
    '996CC6701DDF34F6340F4B2214AA08CA');                            # Wbm VAR: B;
    define ('YAHOO__WBM_KEY',                                       # Wbm KEY: Y;
    'd6f44ed1b5bfa06d');                                            # Wbm VAR: Y;
    ################################ DO NOT MODIFY WHAT'S BELOW! IT'S AUTOMAGICALLY DONE ... ###############################
    # Fix a weird PHP bug, regarding the difference between _SERVER['DOCUMENT_ROOT'] on Windows vs. Linux;
    DIRECTORY_SEPARATOR == _S_WIN                                       ?
    $_SERVER['DOCUMENT_ROOT'] = rtrim ($_SERVER['DOCUMENT_ROOT'], '/')  :
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'];

    # Remember if we're on a Win 32 (R) (TM) ... platform or not.
    DIRECTORY_SEPARATOR == _S_WIN                                       ?
    define ('WIN', TRUE)                                                :
    define ('WIN', FALSE);

    # Define RELATIVE_PATH, by calculating the relative directory from the webroot;
    define ('RELATIVE_PATH', substr (str_replace ($_SERVER['DOCUMENT_ROOT'] . _WS, _NONE,
    str_replace (DIRECTORY_SEPARATOR, _WS, __FILE__)),  0, strrpos (str_replace ($_SERVER['DOCUMENT_ROOT'] . _WS, _NONE,
    str_replace (DIRECTORY_SEPARATOR, _WS, __FILE__)), _S . INCLUDE_DIR)));

    # Do a switch on RELATIVE_PATH, append it or not to
    # the DOCUMENT_ROOT, or DOCUMENT_HOST;
    switch (RELATIVE_PATH != _NONE) {
        case TRUE:
            define ('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']            .  _S   . RELATIVE_PATH . _S);
            define ('DOCUMENT_HOST', 'http://' . $_SERVER['HTTP_HOST']    . _WS   . RELATIVE_PATH . _WS);
        break;

        default:
            define ('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']            .  _S);
            define ('DOCUMENT_HOST', 'http://' . $_SERVER['HTTP_HOST']    . _WS);
        break;
    }

    # Fix the URL rewrite offset depending on the REWRITE_ENGINE;
    if (REWRITE_ENGINE == TRUE) {
        // Add an EXTRA +1;
        define ('URL_REWRITE_OFFFSET',                              # What's the URL offset to be used
        strpos ($_SERVER['REQUEST_URI'],                            # in URLs (see class URL) when making
        _WS . ADMIN_DIR . _WS) !== FALSE ? 0 : 1);                  # them as compatible to mod_rewrite;
    } else {
        // Add an EXTRA +0;
        define ('URL_REWRITE_OFFFSET', 0);                          # What's the URL offset to be used in URLs (see class URL);
    }

    ################################ AUTOMATICALLY define the PROJECT Name, as a mechanism ... #############################
    # Define an unique project name, so we can further extend the security mechanism, based on this unique name;
    # This unique project name will be used in separating sessions of different projects, and can also be used in other
    # circumstances. CHANGE IT FOR EVERY NEW PROJECT YOU DEVELOP WITH THE FRAMEWORK!
    define ('PROJECT_NAME', DOCUMENT_HOST);                         # A defined project name will further increase security;
    ################################ AUTOMATICALLY define the PROJECT Name, as a mechanism ... #############################

    ################################ REQUIRE THE APPLICATION DLL (DYNAMIC LOADING && LIBRARY OF CODE) ######################
    require_once DOCUMENT_ROOT . 'config.php';                      # Request the SQL authentication specific data ...
    require_once DOCUMENT_ROOT . INCLUDE_DIR . _S . '00_LDO.php';   # Request the PHP dynamic auto ...
    ################################ YEY, IF THE PARSER REACHES THIS, EVERYTHING WORKS #####################################
?>
