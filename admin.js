/** Javascripts
 * @Package: 		com.vsoft.trailmonitor
 * @File			admin.js
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

//debugger;
var image_selector;
jQuery(document).ready(function ($) {
	//debugger;
	$("#upload-btn").click(function (e) {
//		debugger;
		e.preventDefault();

		image_selector = wp.media({title: "Trail Image", multiple: false}).open()
			.on("select", function (e2) {
				var uploaded_image = image_selector.state().get("selection").first();
				$("#image_id").val(uploaded_image.toJSON().id);
				$("#vstm_trail_image").attr("src", uploaded_image.attributes.sizes.thumbnail.url);
				$(".media-modal-close").click();
			});
	});

	// *** Keep the Bulk Action Selects Synced ***
	$("#bulk-action-selector-top").change(function (e) {
		$("#bulk-action-selector-bottom").val($("#bulk-action-selector-top").val());
	});
	$("#bulk-action-selector-bottom").change(function (e) {
		$("#bulk-action-selector-top").val($("#bulk-action-selector-bottom").val());
	});

	// *** Check All Box ***
	$("#cb-select-all-1").change(function (e) {
		if ($("#cb-select-all-1").prop("checked")) {
			$(".vstm_list_checkbox").prop("checked", true);
		} else {
			$(".vstm_list_checkbox").prop("checked", false);
		}
	});

	$(".vstm_colorpicker").wpColorPicker();
});

/** Shows elements for making inline edits and hides non-editable fields they replace
 * @param {int} group
 */
function showInlineEditableFields (group) {
	jQuery("[id*='vstm_inline_no_edit_" + group + "_']").hide();
	jQuery("[id*='vstm_inline_edit_" + group + "_']").show(350);
}

/** Hides elements for making inline edits and shows non-editable fields they replace
 * @param {int} group
 */
function hideInlineEditableFields (group) {
	jQuery("[id*='vstm_inline_no_edit_" + group + "_']").show(200);
	jQuery("[id*='vstm_inline_edit_" + group + "_']").hide();
}

/** Saves the changes to the status and updates the non-editable fields
 * @param {int} group
 */
function saveInlineEditableFields (group) {
	hideInlineEditableFields(group);
	jQuery(document).ready(function ($) {
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				action: "vstm_update_status",
				status_id: group,
				name: $("#vstm_inline_edit_" + group + "_name").val(),
				sort_order: $("#vstm_inline_edit_" + group + "_sort_order").val(),
				color: $("#vstm_" + group + "_color").val(),
				wp_nonce: $("#_wpnonce").val()
			},
			success: function (data) {
				if (data == "true") {
					$("#vstm_inline_no_edit_" + group + "_name").html($("#vstm_inline_edit_" + group + "_name").val());
					$("#vstm_inline_no_edit_" + group + "_sort_order").html($("#vstm_inline_edit_" + group + "_sort_order").val());
					$("#vstm_inline_no_edit_" + group + "_color").html($("#vstm_" + group + "_color").val());
					$("#vstm_inline_no_edit_" + group + "_color").css("color", $("#vstm_" + group + "_color").val());
				} else {
					alert('Save failed');
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert("Save failed");
//				console.log("XMLHttpRequest: " + XMLHttpRequest);
//				console.log("textStatus: " + textStatus);
//				console.log("errorThrown: " + errorThrown);
			}
		});
	});
}