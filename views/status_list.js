/** Javascript for Status list
 * @Package: 		com.vsoft.trailmonitor
 * @File			status_list.js
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

var tableData = [];

function addTableData (status_id, trail_name, sort_order, style, color) {
    //debugger;
    tableData.push(
		[
			'<input type="checkbox" name="bulk_action_list[]" value="'+status_id+'" class="vstm_list_checkbox">',
			'<span id="vstm_inline_no_edit_'+status_id+'_name">'+trail_name+'</span>'
				+ '<input id="vstm_inline_edit_'+status_id+'_name" value="'+trail_name+'" maxlength="50">',
			'<span id="vstm_inline_no_edit_'+status_id+'_sort_order">'+sort_order+'</span>'
				+ '<select id="vstm_inline_edit_' + status_id + '_sort_order" name="vstm_inline_edit_' + status_id + '_sort_order" class="">'
                + getSelectOptions(sort_order)
				+ '</select>',
			'<span id="vstm_inline_no_edit_'+status_id+'_color"'+style+'>'+color+'</span>'
				+ '	<div id="vstm_inline_edit_'+status_id+'_cbox"><input id="vstm_'+status_id+'_color" value="'+color+'" class="vstm_colorpicker"></div>',
			'<a href="javascript:void(0)" onclick="showInlineEditableFields('+status_id+')" id="vstm_inline_no_edit_'+status_id+'_edit" class="vstm_intable_button">Edit</a>'
				+	'<a href="javascript:void(0)" onclick="saveInlineEditableFields('+status_id+')" id="vstm_inline_edit_'+status_id+'_save" class="vstm_intable_button">Save</a>'
				+	'<a href="javascript:void(0)" onclick="hideInlineEditableFields('+status_id+')" id="vstm_inline_edit_'+status_id+'_cancel" class="vstm_intable_button">Cancel</a>'
		]
    );
}

function getSelectOptions(sort_order) {
    var html_out = '';
    for (let i = 1; i < 21; i++) {
        if (sort_order == i) {
            html_out += '<option value="'+i+'" selected="selected">'+i+'</option>';
	    } 
        else {
            html_out += '<option value="'+i+'">'+i+'</option>';
        }  
    }
    return html_out;
}

function buildTableData () {
    //debugger;
    jQuery(document).ready(function () {
        //debugger;
        jQuery("#table").DataTable( {
            data: tableData,
            autoWidth: false,
            pageLength: 25,
            stateSave: true,
            columnDefs: [ 
                {orderable: false, targets: [0, 4]}
            ],
            order: [[1, "asc"]]
        });
    });
}