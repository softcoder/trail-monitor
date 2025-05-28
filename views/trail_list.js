/** Javascript for Trail list
 * @Package: 		com.vsoft.trailmonitor
 * @File			trail_list.js
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

var tableData = [];

function addTableData (trail_id, trail_name, visit_date, created, link, comment, submitter_name, image_url, 
                       status_name, hidden, sort_order, show_widget, show_shortcode) {
    //debugger;
    tableData.push(
        [
            '<input type="checkbox" name="bulk_action_list[]" value="' + trail_id + '" class="vstm_list_checkbox">',
            '<a href="admin.php?page=trail-status-2-edit&trail=' + trail_id + '" class="row-title">' + trail_name  + '</a>',
            visit_date,
            created,
            (link != '' ? '<a href="' + link + '" target="_blank">Visit Website</a>' : ''),
            '<p>' + comment + '</p>',
            submitter_name,
            (image_url != '' ? '<img src="' + image_url + '" style="width: 33px; height: 33px;">' : ''),
            status_name,
            hidden,
            sort_order,
            show_widget,
            show_shortcode
        ]
    );
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
                {orderable: false, targets: [0, 5]}
            ],
            order: [[1, "asc"]]
        });
    });
}