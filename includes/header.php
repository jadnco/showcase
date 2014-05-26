<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>
			<?php
				if (isset($page)) {
					switch ($page) {
						case "login": echo "Login"; break;
						case "home":  echo "Projects"; break;
						case "add":   echo "Add Project"; break;
						case "setup": echo "Setup"; break;
					}
				}
			?>
		</title>
		<base href="<?=BASE_URL?>">
		<meta name="author" content="Jaden Dessureault">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/style.css">
		<script src="<?=BASE_URL?>/assets/js/jquery.min.js"></script>
		<?php if (is_admin()) { ?>
		<link rel="stylesheet" href="<?=BASE_URL?>/assets/css/admin.css">
		<script src="<?=BASE_URL?>/assets/js/admin.js"></script>
		<script src="http://malsup.github.com/jquery.form.js"></script>
		<?php } ?>
	</head>
	<body>
		<?php if (!is_home() && $page != "setup") { ?>
		<a class="home" href="<?=HOME?>">Home</a>
		<?php } ?>

		<?php if (is_admin()) { ?>
		<a class="logout" href="<?=HOME?>/logout">Logout</a>
		<?php } ?> <?php elseif ($page != "login" && $page != "setup" && !is_admin()) { ?>
		<a class="login" href="<?=HOME?>/login">Login</a>
		<?php } ?>
