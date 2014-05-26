<?php require_once("../includes/init.php"); ?>

<?php if(!is_admin()) redirect_to(HOME . "/login"); ?>

<?php $page = "add"; ?>
<?php include(layout("header.php")); ?>

<h2 class="add-title">Add Project</h2>

<section id="add-project" class="clearfix">
	<article class="project add">
		<div class="hover">
			<div class="thumb-dimensions">250x180<br>100KB</div>
			<div class="hover-inner one">
				<form id="thumb-form" class="clearfix" action="" method="post" enctype="multipart/form-data">
		        	<div class="upload-wrap">
						<input type="file" name="thumb_image" class="choose-btn">
						<div class="upload-btn"></div>
		        	</div>
		        </form>
			</div>
			<div class="upload-percent"></div>
		</div>
		<input type="text" name="title" class="project-title" placeholder="Add a title">
	</article>
	<div class="project-link-wrap">
		<input type="text" name="behance_link" placeholder="Behance"><span class="input-icon behance"></span>
		<input type="text" name="dribbble_link" placeholder="Dribbble"><span class="input-icon dribbble"></span>
		<input type="text" name="direct_link" placeholder="URL"><span class="input-icon direct"></span>
		<input type="text" name="priority" placeholder="Priority">
		<input type="text" name="published" placeholder="Show">
		<input type="submit" name="add_submit">
	</div>
</section>

<?php include(layout("footer.php")); ?>