<?php require_once("../includes/init.php"); ?>

<?php

if (user_exists(user("full_name"))) redirect_to(HOME);

if (isset($_POST["setup_submit"])) {
	if (setup() && create_user($_POST["full_name"], $_POST["password"])) {
		if (login($_POST["full_name"], $_POST["password"])) redirect_to(HOME);
	}
}

?>

<?php $page = "setup"; ?>
<?php include(layout("header.php")); ?>

<h2 class="setup-title">Setup</h2>

<form class="setup-form" method="post" action="">
	<input type="text" name="full_name" placeholder="Full name">
	<input type="password" name="password" placeholder="Password">
	<input type="submit" name="setup_submit">
</form>

<?php include(layout("footer.php")); ?>