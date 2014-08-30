<?php

error_reporting(0);

// Create new constants for dependent json files
define("USER_JSON", "../user.json");
define("PROJECTS_JSON", "../projects.json");

define("ACTIVITY_LOG", "../activity.log");

// Get ROOT_PATH by calling __DIR__ (without /public)
define("ROOT_PATH", "/Applications/XAMPP/xamppfiles/htdocs/showcase");
define("INC_PATH", ROOT_PATH . "/includes/");

// Base url (public folder); eg. http://example.com/public
define("BASE_URL", "http://localhost/showcase/public");

// Add the home url; eg. http://example.com
define("HOME", "http://localhost/showcase");

// Timezone
date_default_timezone_set("America/Winnipeg");

session_start();

// All the main functions
require_once("functions.php");

if (file_exists(PROJECTS_JSON)) {
    if (fileperms(PROJECTS_JSON) !== 0600) chmod(PROJECTS_JSON, 0600);
} else {
    create_file(PROJECTS_JSON);
}

if (file_exists(USER_JSON)) {
    if (fileperms(USER_JSON) !== 0600) chmod(USER_JSON, 0600);
} else {
    create_file(PROJECTS_JSON);
}

if (file_exists(ACTIVITY_LOG)) {
    if (fileperms(ACTIVITY_LOG) !== 0600) chmod(ACTIVITY_LOG, 0600);
} else {
    create_file(ACTIVITY_LOG);
}

?>
