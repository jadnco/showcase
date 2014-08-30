$(document).ready(function() {
	$(".project-link-wrap input[name='add_submit']").on("click", function(event) {
		event.preventDefault();
		create_project();
	});

	$("#add-project #thumb-form .upload-btn").on("click", function() {
		if (!$(this).hasClass("icon-check")) {
			$("#thumb-form").submit();
		}

		return false;
	});

	$("#add-project input[type='file']").change(function() {
		$("#add-project #thumb-form .choose-btn").hide();
		$("#add-project #thumb-form .upload-btn").show();
	});

	function create_project() {
		var projectTitle     = $(".project.add input[name='title']").val(),
			projectBehance   = $(".project-link-wrap input[name='behance_link']").val(),
			projectDribbble  = $(".project-link-wrap input[name='dribbble_link']").val(),
			projectDirect    = $(".project-link-wrap input[name='direct_link']").val(),
			projectPriority  = $(".project-link-wrap input[name='priority']").val(),
			projectPublished = $(".project-link-wrap input[name='published']").val();

		// Make sure the title is provided; will be given last uploaded image
		if (projectTitle.length > 0) {
			$.ajax({
			    url: "public/create.php",
			    method: "POST",
			    data: {
					title: projectTitle,
					behance_link: projectBehance,
					dribbble_link: projectDribbble,
					direct_link: projectDirect,
					priority: projectPriority,
					published: projectPublished
				},
			    success: function(data) {
			        window.location.replace(data);
			    }
		    });
		} else {
			if (!$("#add-project").find(".add-error").length) {
				$("#add-project").append("<div class=\"add-error\">Please enter a title.</div>");
			} else {
				$("#add-project .add-error").text("Please enter a title!!");
			}
		}
	}

	var percentBar = $("#add-project .upload-percent");

	$("#thumb-form").ajaxForm({
		url: "public/upload_thumb.php",
		beforeSend: function() {
		    var percentVal = '0%';
		    percentBar.width(percentVal)
		},
		uploadProgress: function(event, position, total, percentComplete) {
		    var percentVal = percentComplete + '%';

		    percentBar.animate({"width": percentVal}, 50);

		    if (percentComplete > 60) {
				$("#add-project #thumb-form .upload-btn").hide();
		    }

		    if (percentComplete > 95) {
				percentBar.addClass("full");
		    }
		},
		success: function(xhr, statusText, data) {
		    var percentVal = '100%';
		    percentBar.animate({"width": percentVal}, 50);

		    if (!percentBar.hasClass("full")) {
				percentBar.addClass("full");
		    }

		    console.log("upload done");

		    $(".photo-modal #header-form input[type='file']").hide();
		    $("#add-project #thumb-form .upload-btn").removeClass("upload-btn").addClass("icon-check").show();
		}
	});

});

function delete_project(projectId, projectTitle, refPage) {
	if (confirm("Do you really want to delete \"" + projectTitle + "\"?")) {
		// Manually insert BASE_URL constant
		var base_url = "http://localhost/showcase/public";

		window.location.replace(base_url+"/delete.php?project=" + projectId + "&ref=" + refPage);
	} else {
		return false;
	}
}
