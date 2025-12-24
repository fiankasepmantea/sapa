<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');
define('DATE_FORMAT', '%Y-%m-%d %H:%i:%s');
define('OAUTH', 'Oauth/v1/auth');


define('ERROR_TOKEN_UNIDENTIFIED', 880);
define('ERROR_TOKEN_EXPIRED', 881);
define('ERROR_TOKEN_UNKNOWN', 882);
define('ERROR_NOT_FOUND', 404);
define('HTTP_CONTINUE', 100);
define('HTTP_SWITCHING_PROTOCOLS', 101);
define('HTTP_PROCESSING', 102);            // RFC2518

// Success

/**
 * The request has succeeded
 */
define('HTTP_OK', 200);

/**
 * The server successfully created a new resource
 */
define('HTTP_CREATED', 201);
define('HTTP_ACCEPTED', 202);
define('HTTP_NON_AUTHORITATIVE_INFORMATION', 203);

/**
 * The server successfully processed the request, though no content is returned
 */
define('HTTP_NO_CONTENT', 204);
define('HTTP_RESET_CONTENT', 205);
define('HTTP_PARTIAL_CONTENT', 206);
define('HTTP_MULTI_STATUS', 207);          // RFC4918
define('HTTP_ALREADY_REPORTED', 208);      // RFC5842
define('HTTP_IM_USED', 226);               // RFC3229

// Redirection

define('HTTP_MULTIPLE_CHOICES', 300);
define('HTTP_MOVED_PERMANENTLY', 301);
define('HTTP_FOUND', 302);
define('HTTP_SEE_OTHER', 303);

/**
 * The resource has not been modified since the last request
 */
define('HTTP_NOT_MODIFIED', 304);
define('HTTP_USE_PROXY', 305);
define('HTTP_RESERVED', 306);
define('HTTP_TEMPORARY_REDIRECT', 307);
define('HTTP_PERMANENTLY_REDIRECT', 308);  // RFC7238

// Client Error

/**
 * The request cannot be fulfilled due to multiple errors
 */
define('HTTP_BAD_REQUEST', 400);

/**
 * The user is unauthorized to access the requested resource
 */
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_PAYMENT_REQUIRED', 402);

/**
 * The requested resource is unavailable at this present time
 */
define('HTTP_FORBIDDEN', 403);

/**
 * The requested resource could not be found
 *
 * Note: This is sometimes used to mask if there was an UNAUTHORIZED (401) or
 * FORBIDDEN (403) error, for security reasons
 */
define('HTTP_NOT_FOUND', 404);

/**
 * The request method is not supported by the following resource
 */
define('HTTP_METHOD_NOT_ALLOWED', 405);

/**
 * The request was not acceptable
 */
define('HTTP_NOT_ACCEPTABLE', 406);
define('HTTP_PROXY_AUTHENTICATION_REQUIRED', 407);
define('HTTP_REQUEST_TIMEOUT', 408);

/**
 * The request could not be completed due to a conflict with the current state
 * of the resource
 */
define('HTTP_CONFLICT', 409);
define('HTTP_GONE', 410);
define('HTTP_LENGTH_REQUIRED', 411);
define('HTTP_PRECONDITION_FAILED', 412);
define('HTTP_REQUEST_ENTITY_TOO_LARGE', 413);
define('HTTP_REQUEST_URI_TOO_LONG', 414);
define('HTTP_UNSUPPORTED_MEDIA_TYPE', 415);
define('HTTP_REQUESTED_RANGE_NOT_SATISFIABLE', 416);
define('HTTP_EXPECTATION_FAILED', 417);
define('HTTP_I_AM_A_TEAPOT', 418);                                               // RFC2324
define('HTTP_UNPROCESSABLE_ENTITY', 422);                                        // RFC4918
define('HTTP_LOCKED', 423);                                                      // RFC4918
define('HTTP_FAILED_DEPENDENCY', 424);                                           // RFC4918
define('HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL', 425);   // RFC2817
define('HTTP_UPGRADE_REQUIRED', 426);                                            // RFC2817
define('HTTP_PRECONDITION_REQUIRED', 428);                                       // RFC6585
define('HTTP_TOO_MANY_REQUESTS', 429);                                           // RFC6585
define('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE', 431);                             // RFC6585

// Server Error

/**
 * The server encountered an unexpected error
 *
 * Note: This is a generic error message when no specific message
 * is suitable
 */
define('HTTP_INTERNAL_SERVER_ERROR', 500);

/**
 * The server does not recognise the request method
 */
define('HTTP_NOT_IMPLEMENTED', 501);
define('HTTP_BAD_GATEWAY', 502);
define('HTTP_SERVICE_UNAVAILABLE', 503);
define('HTTP_GATEWAY_TIMEOUT', 504);
define('HTTP_VERSION_NOT_SUPPORTED', 505);
define('HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL', 506);                        // RFC2295
define('HTTP_INSUFFICIENT_STORAGE', 507);                                        // RFC4918
define('HTTP_LOOP_DETECTED', 508);                                               // RFC5842
define('HTTP_NOT_EXTENDED', 510);                                                // RFC2774
define('HTTP_NETWORK_AUTHENTICATION_REQUIRED', 511);
/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_LOW instead.
 */
define('EVENT_PRIORITY_LOW', 200);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_NORMAL instead.
 */
define('EVENT_PRIORITY_NORMAL', 100);

/**
 * @deprecated Use \CodeIgniter\Events\Events::PRIORITY_HIGH instead.
 */
define('EVENT_PRIORITY_HIGH', 10);
/*
 |--------------------------------------------------------------------------
 | Dhiva JWT Timeout
 |--------------------------------------------------------------------------
 */
define("JWT_TIMEOUT", 180000*20000);
define("JWT_BY", "JAM");
/*
 |--------------------------------------------------------------------------
 | Dhiva Uploads Path
 |--------------------------------------------------------------------------
 */


define("BASE_APP", "https://dev.awh.co.id/mizu/app-version/show_by/version/");
define("PATH_MODEL", "App\Models\SqlModel");
define("FIREBASE_API", "AAAAtTcH0GQ:APA91bG0N7PqyXCFiDHQn4Jw8AO2dgFF-hp9jeCUff1pMwb25JrJWQUciUMhhfCwbVcabx-GGwzU-uktnbl3PmScYZI0weI_Fa1cCU_6HdJH0duX48tVZmh-QErIkXBeC0EPP7otC9Ip");

define("PATH_IMAGES", "uploads/images/");
define("PATH_IMAGES_SERVER", WRITEPATH . PATH_IMAGES);
define("PATH_IMAGES_CLIENT", APPPATH . PATH_IMAGES);

define("PATH_APK", "uploads/apk/");
define("PATH_APK_SERVER", WRITEPATH . PATH_APK);
define("PATH_APK_CLIENT", APPPATH . PATH_APK);

define("PATH_PDF", "uploads/pdf/");
define("PATH_PDF_SERVER", WRITEPATH . PATH_PDF);
define("PATH_PDF_CLIENT", APPPATH . PATH_PDF);
define("ProdevToken", "qeTAbqcqiZ6hooBgdtZ32ftcdney1SKGvDhLvS31A4g");

if (!function_exists('encodeloop')) {
	function encodeloop($source = '')
	{
		for ($i = 0; $i < 5; $i++) {
			$encodedString = base64_encode($source);
		}
		return $encodedString;
	}
}

if (!function_exists('shuffle_word')) {
	function shuffle_word($word = '', $x = 4)
	{
		$arrSplitString = str_split($word, $x);
		$countSplittedString = count($arrSplitString);

		$word = '';

		for ($i = 0; $i < ($countSplittedString); $i++) {
			if ($i == $countSplittedString - 1) {
				$word .= $arrSplitString[0];
			} else {
				$word .= $arrSplitString[$i + 1];
			}
		}

		return $x . $word;
	}
}

// ------------------------------------------------------------------------

if (!function_exists('mdate')) {
	function mdate($datestr = '', $time = '')
	{
		if ($datestr === '') {
			return '';
		} elseif (empty($time)) {
			$time = now();
		}

		$datestr = str_replace(
			'%\\',
			'',
			preg_replace('/([a-z]+?){1}/i', '\\\\\\1', $datestr)
		);

		return date($datestr, $time);
	}
}
if (!function_exists('decodeloop')) {
	function decodeloop($source = '', $x = 4)
	{
		for ($i = 0; $i < 5; $i++) {
			$decodeString = base64_decode($source);
		}

		return $decodeString;
	}
}

if (!function_exists('deshuffle_Word')) {
	function deshuffle_word($source = '', $x = 4)
	{
		$firstStr = substr($source, 0, 1);
		if ($firstStr != $x) {
			return false;
		}
		$source = substr($source, 1);
		$lengthSource = strlen($source);
		if (($lengthSource - 1) < $x) {
			return $source;
		}
		$shardString = substr($source, $lengthSource - $x);
		$source = substr($source, 0, $lengthSource - $x);
		$deshuffleString = $shardString . $source;
		return $deshuffleString;
	}
}
if (!function_exists("post")) {
	function post(string $key, $clean = true)
	{
		if ($clean) {
			if (isset($_POST[$key])) {
				$string = preg_match("/^[a-zA-Z0-9]+$/", $_POST[$key]);
				if ($string) {
					return $_POST[$key];
				}
			}
		}
		$response = [
			"success" => false,
			"code" => 9001,
			"message" => "Query Not Allowed",
		];
		header("Content-Type: application/json");
		echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		die;
	}
}
if (!function_exists("searchpost")) {
	function searchpost(): bool | array
	{
		if (isset($_POST["from"]) && isset($_POST["to"]) && isset($_POST["page"]) && isset($_POST["limit"])) {
			$data['from'] = $_POST["from"];
			$data['to'] = $_POST["to"];
			$data['page'] = $_POST["page"];
			$data['limit'] = $_POST["limit"];
			unset($_POST["from"]);
			unset($_POST["to"]);
			unset($_POST["page"]);
			unset($_POST["limit"]);
			return $data;
		}
		return false;
	}
}
if (!function_exists("postArray")) {
	function postArray($clean = true)
	{
		if ($clean) {
			foreach ($_POST as $a => $b) {
				$string = preg_match("/^[a-zA-Z0-9]+$/", $_POST[$a]);
				if (!$string) {
					$response = [
						"success" => false,
						"code" => 9001,
						"message" => "Query Not Allowed",
					];
					header("Content-Type: application/json");
					echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
					die;
				}
			}
		}
		return $_POST;
	}
}
