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
define("LDAP_SERVER", "127.0.0.1");
define("LDAP_PREFIX", "cn=");
define("LDAP_USERNAME", "username");
define("LDAP_PASSWORD", "password");
define("LDAP_SUFFIX", ",dc=company,dc=org");
define("LDAP_SEARCH_ATTRIB", "uid");
define("LDAP_SURNAME_ATTRIB", "sn");
define("LDAP_GIVENNAME_ATTRIB", "givenname");
define("LDAP_EMAIL_ATTRIB", "mail");

// Remove Magic Quotes Code
// No longer needed as Magic Quotes are deprecated and removed in PHP 7.0+

// If you need to handle escaping data, consider using more modern methods like
// mysqli_real_escape_string() for MySQL data or prepared statements with PDO or MySQLi

// Using htmlspecialchars() for output to HTML to prevent XSS
function escape_output($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = escape_output($value);
        }
    } else {
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

// Example usage:
// echo escape_output($someUserInput);

?>
