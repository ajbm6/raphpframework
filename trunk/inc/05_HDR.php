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

############# Motto: 'My brain's HTTP ...';
/**
 * Abstract CLASS providing a way to handle setting of header ()'s in our system. Also, constants are provided to enable a better
 * way to have control over the executing environment;
 *
 * @package RA-Headers-Management-And-Processing
 * @category RA-Abstract-CORE
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @access protected
 * @abstract
 */
abstract class HDR extends OBS {
    protected static $objName                   = 'HDR :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* Contants HEADER defines ... used when redirecting the correct way ... */
    const HEADER_MOVED_PERMANENTLY				= 'HTTP/1.1 301 Moved Permanently';
    const HEADER_400_BAD_REQUEST                = 'HTTP/1.1 400 Bad Request';
    const HEADER_401_AUTHORIZATION_REQUIRED     = 'HTTP/1.1 401 Authorization Required';
    const HEADER_402_PAYMENT_REQUIRED           = 'HTTP/1.1 402 Payment Required';
    const HEADER_403_FORBIDDEN                  = 'HTTP/1.1 403 Forbidden';
    const HEADER_404_NOT_FOUND                  = 'HTTP/1.1 404 Not Found';
    const HEADER_405_METHOD_NOT_ALLOWED         = 'HTTP/1.1 405 Method Not Allowed';
    const HEADER_406_NOT_ACCEPTABLE             = 'HTTP/1.1 406 Not Acceptable';
    const HEADER_407_PROXY_AUTHENTICATION_REQ   = 'HTTP/1.1 407 Proxy Authentication Required';
    const HEADER_408_REQUEST_TIMED_OUT          = 'HTTP/1.1 408 Request Time-out';
    const HEADER_409_CONFLICT                   = 'HTTP/1.1 409 Conflict';
    const HEADER_410_GONE                       = 'HTTP/1.1 410 Gone';
    const HEADER_411_LENGTH_REQUIRED            = 'HTTP/1.1 411 Length Required';
    const HEADER_412_PRECONDITION_FAILED        = 'HTTP/1.1 412 Precondition Failed';
    const HEADER_413_REQUEST_ENTITY_TO_LARGE    = 'HTTP/1.1 413 Request Entity Too Large';
    const HEADER_414_REQUEST_URI_TOO_LARGE      = 'HTTP/1.1 414 Request-URI Too Large';
    const HEADER_415_UNSUPPOTED_MEDIA_TYPE      = 'HTTP/1.1 415 Unsupported Media Type';
    const HEADER_416_REQ_RANGE_NOT_SATISFIABLE  = 'HTTP/1.1 416 Requested Range Not Satisfiable';
    const HEADER_417_EXPECTATION_FAILED         = 'HTTP/1.1 417 Expectation Failed';
    const HEADER_422_UNPROCESSABLE_ENTITY       = 'HTTP/1.1 422 Unprocessable Entity';
    const HEADER_423_LOCKED                     = 'HTTP/1.1 423 Locked';
    const HEADER_424_FAILED_DEPENDENCY          = 'HTTP/1.1 424 Failed Dependency';
    const HEADER_425_NO_CODE                    = 'HTTP/1.1 425 No code';
    const HEADER_426_UPGRADE_REQUIRED           = 'HTTP/1.1 426 Upgrade Required';
    const HEADER_500_INTERNAL_SERVER_ERROR      = 'HTTP/1.1 500 Internal Server Error';
    const HEADER_501_METHOD_NOT_IMPLEMENTED     = 'HTTP/1.1 501 Method Not Implemented';
    const HEADER_502_BAD_GATEWAY                = 'HTTP/1.1 502 Bad Gateway';
    const HEADER_503_SERVICE_TEMP_UNAVAILABLE   = 'HTTP/1.1 503 Service Temporarily Unavailable';
    const HEADER_504_GATEWAY_TIMED_OUT          = 'HTTP/1.1 504 Gateway Time-out';
    const HEADER_505_HTTP_VERSION_NOT_SUPPORTED = 'HTTP/1.1 505 HTTP Version Not Supported';
    const HEADER_506_VARIANT_ALSO_NEGOTIATES    = 'HTTP/1.1 506 Variant Also Negotiates';
    const HEADER_507_INSUFFICIENT_STORAGE       = 'HTTP/1.1 507 Insufficient Storage';
    const HEADER_508_UNUSED                     = 'HTTP/1.1 508 unused';
    const HEADER_509_UNUSED                     = 'HTTP/1.1 509 unused';
    const HEADER_510_NOT_EXTENDED               = 'HTTP/1.1 510 Not Extended';

    /**
     * Will set a key/var pair, like 'gzip', 'Content-encoding'. We use such a method so we can have a single point where we can globally
	 * modify the way our headers are sent to the browser or the client, thus having control over what's done and in what circumstances,
	 * as the HTTP protocol works by sending and receiving headers, mechanism that is the SOUL of our Web-enabled applications;
	 * <code>
	 * <?php
	 *		// Ex: redirect the client ...
	 *		HDR::setHeaderKey (new S ('http://google.ro'), new S ('Location'));
	 * ?>
	 * </code>
     *
     * @param S $headerContent The content to be set for the header key
     * @param S $headerType The header key to be set
     * @return B Will return true if the headers have been set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @link http://php.net/header
	 * @version $Id: 05_HDR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function setHeaderKey (S $headerContent, S $headerType) {
        // Check if we've sent ANY headers;
        if (headers_sent ()) {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (HEADERS_ALREADY_SENT),
            new S (HEADERS_ALREADY_SENT_FIX));
        } else {
            // Add the HEADER, to the current page;
            switch ($headerType) {
                case 'Location':
                    // CALL the PHP function & exit ...
                    header ($headerType->toString () . _CL .
                    $headerContent->toString ());

                    // Empty the _SESSION
                    if (isset ($_SESSION['POST'])) {
                        unset ($_SESSION['POST']);
                    }

                    // Empty the __FILES
                    if (isset ($_SESSION['FILES'])) {
                        unset ($_SESSION['FILES']);
                    }

                    // Exit!;
                    exit (0);
                    // BK;
                    break;

                default:
                    // CALL the PHP function ...
                    header ($headerType->toString () . _CL .
                    $headerContent->toString ());
                    // BK;
                    break;
            }

            // Do return ...
            return new B (TRUE);
        }
    }

    /**
     * Will set a header string that you pass on to it. Useful for non-standard headers like 'refresh=5; url='. Although the HTTP works
	 * by sending and receiveing headers, now ALL of them are standard key/var pairs and some are something like th above. For such
	 * casses, we define this method to be able to set them in a more organized manner;
	 * <code>
	 * <?php
	 *		// Make a SEO friendly redirect ...
	 *		HDR::setHeaderStr (new S (HDR::HEADER_MOVED_PERMANENTLY));
	 *		HDR::setHeaderKey (new S ('http://newpage.com'), new S ('Location'));
	 * ?>
	 * </code>
     *
     * @param S $headerString Will set the specified non-key/var header
     * @return B Will return true if the header string was set
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License
	 * @link http://php.net/header
	 * @version $Id: 05_HDR.php 313 2009-10-09 13:27:52Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
	 * @static
	 * @final
    */
    public static final function setHeaderStr (S $headerString) {
        // Check if we've sent ANY headers;
        if (headers_sent ()) {
            // Error me proudly;
            self::renderScreenOfDeath (new S (__CLASS__),
            new S (HEADERS_ALREADY_SENT),
            new S (HEADERS_ALREADY_SENT_FIX));
        } else {
            // Add the HEADER, to the current page;
            header ($headerString);
            // Do return ...
            return new B (TRUE);
        }
    }
}
?>
