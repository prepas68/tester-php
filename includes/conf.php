<?php
// phpBB login integration
define("PHPBB", "0");
define("PHPBB_PATH", "/path/to/phpbb/");

// Handle sessions via IP address or cookies
define("IPSESSIONS", "0");

// Simple design settings
define("TITLE", "WebTester Online Testing");
define("BGCOLOR", "#FFFFFF");
define("LOGOW", "337");
define("LOGOH", "75");

// Global Test Settings
define("DISABLE_GRADE", "0");   // Disables showing grade chart on the final grade page
define("DISABLE_ANSWERS", "0"); // Disables showing answers on the final grade page
define("DISABLE_PRINT", "0");   // Disables the ability to print
define("EXPLAIN_ALL", "0");     // Forces explanation to be printed on all questions
define("VERIFY_EMAIL", "0");    // Requires the email address to be entered twice
define("CLOSE_WINDOW", "0");    // Closes window when 'Done' is pressed
define("RETRY", "0");           // Places Retry button on grade page
define("NODONE", "0");          // Removes Done button on grade page
define("ALLOW_OVERRIDE", "0");  // Disables strict session checking
define("SKIP_REVIEW", "1");     // Skip review page
define("SHUFFLEANSWERS", "1");  // Shuffle answers for each question

// LDAP Authentication Settings
define("LDAP_ENABLED", "0");
define("LDAP_SERVER", "sql24.dnsserver.eu");
define("LDAP_PREFIX", "cn=");
define("LDAP_USERNAME", "db203558xtestdb");
define("LDAP_PASSWORD", "FFdebfpeti39*,");
define("LDAP_SUFFIX", ",dc=company,dc=org");
define("LDAP_SEARCH_ATTRIB", "uid");
define("LDAP_SURNAME_ATTRIB", "sn");
define("LDAP_GIVENNAME_ATTRIB", "givenname");
define("LDAP_EMAIL_ATTRIB", "mail");

// Magic quotes handling (Removed in PHP 7.4+)
ini_set("magic_quotes_runtime", 0);

// Remove magic quotes from input data manually
function stripslashes_array($data) {
    return is_array($data) ? array_map('stripslashes_array', $data) : stripslashes($data);
}

$_SERVER = stripslashes_array($_SERVER);
$_GET = stripslashes_array($_GET);
$_POST = stripslashes_array($_POST);
$_COOKIE = stripslashes_array($_COOKIE);
$_FILES = stripslashes_array($_FILES);
$_ENV = stripslashes_array($_ENV);
$_REQUEST = stripslashes_array($_REQUEST);

if (isset($_SESSION)) {
    $_SESSION = stripslashes_array($_SESSION);
}
?>