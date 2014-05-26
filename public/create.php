<?php

require_once("../includes/init.php");

if (isset($_POST["title"]) || !empty($_POST["title"])) {
	$title    = $_POST["title"];
	$behance  = $_POST["behance_link"];
	$dribbble = $_POST["dribbble_link"];
	$direct   = $_POST["direct_link"];
	$priority = (int)$_POST["priority"];
	$published = (int)$_POST["published"];

	if ($published > 1) {
    	$published = 1;
	}

	$thumb = get_thumb_url(new_id() + 1);

	create_project($title, $thumb, $behance, $dribbble, $direct, $priority, $published);

	echo HOME;
} else {
	// Return an error to AJAX
	echo 0;
}

?>