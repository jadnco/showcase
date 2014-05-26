<?php require_once("../includes/init.php"); ?>

<?php if (!user_exists(user("full_name"))) redirect_to(HOME . "/setup"); ?>

<?php $page = "home"; ?>
<?php $page_num = (isset($_GET["page"])) ? $_GET["page"] : 1; ?>
<?php if ($page_num > pagination(null, "pages")) redirect_to(HOME . "/page/" . pagination(null, "pages")); ?>

<?php $welcome = array("Hi", "Hello", "Howdy"); ?>

<?php include(layout("header.php")); ?>

<h2 class="person"><a href="<?=HOME?>" title="Home"><?=(is_admin()) ? $welcome[rand(0, 2)]. "," : ""?> <?=user("full_name")?></a></h2>

<section id="projects-wrap" class="clearfix">

<?php if (is_admin()) { ?>
<article class="project add">
	<a href="<?=HOME?>/add" title="Add new project">
		<div class="hover">
			<div class="hover-inner one"><div class="icon-add"></div></div>
		</div>
	</a>
	<div class="project-title">Add project</div>
</article>
<?php } ?>

<?php foreach (pagination($page_num, "project") as $project => $id) { ?>
<article class="project <?=(!is_published($id)) ? "not-published" : ""?>" style="background-image: url(<?=project($id, "thumb")?>);">
	<div class="hover">
		<div class="hover-inner <?=count_links($id)?>">
			<?php if (project($id, "dribbble")) { ?>
				<a href="<?=project($id, "dribbble")?>" class="icon-dribbble" title="View on Dribbble"></a>
			<?php } ?>
			<?php if (project($id, "behance")) { ?>
				<a href="<?=project($id, "behance")?>" class="icon-behance" title="View on Behance"></a>
			<?php } ?>
			<?php if (project($id, "direct_url")) { ?>
				<a href="<?=project($id, "direct_url")?>" class="icon-eye" title="View website"></a>
			<?php } ?>
			<?php if (is_admin()) { ?>
				<a href="javascript:void(0);" class="icon-trash" onclick="delete_project(<?=$id?>, '<?=project($id, "title")?>', <?=$page_num?>)" title="Delete project"></a>
			<?php } ?>
		</div>
		<div class="date">Posted <?=project($id, "date")?></div>
	</div>
	<div class="project-title"><?=project($id, "title")?></div>
</article>
<?php } ?>

<?php if (!all_published()) { ?>
<div class="no-projects">No projects :(</div>
<?php } ?>

<?php if (pagination(null, "pages") > 1) { ?>
<div class="pagination">
	<ul>
		<?php for ($i = 1; $i <= pagination(null, "pages"); $i++) { ?>
			<li class="<?=($page_num == $i) ? "current": ""?>"><a href="<?=HOME . "/page/" . $i?>">&bull;</a></li>
		<?php } ?>
	</ul>
</div>
<?php } ?>

<section>
<?php } ?>

<?php include(layout("footer.php")); ?>
