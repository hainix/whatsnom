$(function() {
  var submitImageDeleteFunc = function() {
	var element = $(this);
	var element_id = element.attr("proj");

	var submitDiv = '#name_project_submit_div_'+element_id;
	var dataString =
		'id=' + escape(element_id)
            ;
	$(submitDiv).html("deleting...");
	$.ajax({
		type: "POST",
		    url: "handlers/imageCMSHandler.php",
		    data: dataString,
		    success: function() {$(submitDiv).html(
		      '<span style="color: red;">deleted</span>');
		}
	    });
	return false;
  }
  
  $(".submit_comment").click(submitImageDeleteFunc);

  var submitCastDeleteFunc = function() {
	var element = $(this);
	var element_id = element.attr("proj");

	var submitDiv = '#person_delete_div_'+element_id;
	var dataString =
		'id=' + escape(element_id)
            ;
	$(submitDiv).html("deleting person..");
	$.ajax({
		type: "POST",
		    url: "handlers/personDeleteCMSHandler.php",
		    data: dataString,
		    success: function() {$(submitDiv).html(
		      '<span style="color: red;">deleted person</span>');
		}
	    });
	return false;
  }  
  $(".delete_person").click(submitCastDeleteFunc);

  var submitProfilePicFunction = function() {
	var element = $(this);
	var element_id = element.attr("proj");

	var submitDiv = '#profile_pic_promote_div_'+element_id;
	var dataString =
		'id=' + escape(element_id)
            ;
	$(submitDiv).html("saving profile pic...");
	$.ajax({
		type: "POST",
		    url: "imageProfileUpgradeCMSHandler.php",
		    data: dataString,
		    success: function() {$(submitDiv).html(
		      '<span style="color: green;">[saved profile pic]</span>');
		}
	    });
	return false;
  }  
  $(".profile_pic_upgrade").click(submitProfilePicFunction);



});