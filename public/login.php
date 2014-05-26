<?php require_once("../includes/init.php"); ?>

<?php

if (is_admin()) redirect_to(HOME);

if (isset($_POST["login_submit"])) {
	if (login($_POST["full_name"], $_POST["password"])) {
		redirect_to(HOME);
	} elseif (empty($_POST["password"]) || empty($_POST["full_name"])) {
		$error = "Both fields must be filled.";
	} else {
	    activity_log(time(), "Failed login attempt.", null);
		$error = "Couldn't login. Please try again.";
	}
}

?>

<?php $page = "login"; ?>
<?php include(layout("header.php")); ?>

<h2 class="login-title">Login</h2>

<form class="login-form" method="post" action="">
	<input type="text" name="full_name" placeholder="Full Name" value="<?=(isset($_POST["full_name"])) ? $_POST["full_name"] : ""?>">
	<input type="password" name="password" placeholder="Password">
	<input type="submit" name="login_submit">
	<?php if (isset($error) && !empty($error)) { ?>
	<div class="error"><?=$error?></div>
	<?php } ?>
</form>

<?php include(layout("footer.php")); ?>