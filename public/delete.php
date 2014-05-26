<?php

require_once("../includes/init.php");

if (!is_admin()) redirect_to(HOME . "/login");

delete_project($_GET["project"]);

if (isset($_GET["ref"])) {
	redirect_to(HOME . "/page/" . $_GET["ref"]);
} else {
	redirect_to(HOME);
}

?>