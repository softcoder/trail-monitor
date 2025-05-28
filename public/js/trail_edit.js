/** Javascript for Trail edit
 * @Package: 		com.vsoft.trailmonitor
 * @File			trail_edit.js
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

// When user selects a trail name, auto populate the link to that trail if the link field is blank
function autoPopulateTrailLink() {
    //debugger;
    var trailName = document.getElementById('trail_name');
    trailName.addEventListener('input', function () {
        //debugger;
        const inputValue = this.value;
        const datalistId = this.getAttribute('list');
        const selectedOption = document.querySelector(`#${datalistId} option[value="${inputValue}"]`);
        if (selectedOption) {
            const itemUrl = selectedOption.attributes.trailurl.value;
            //console.log('Selected Item URL:', itemUrl);
            if(document.getElementById('link').value == "") {
                document.getElementById('link').value = itemUrl;
            }
        } 
        else {
            console.log('No matching item found.');
        }
    });
}

// When user selects a photo, show the size of the file
function checkImageUploadeSize() {
    //debugger;
    var image_upload_file = document.getElementById('image_upload');
    if(image_upload_file !== 'undefined' && image_upload_file != null) {
        image_upload_file.addEventListener('change', function (event) {
            //debugger;
            var file_size = image_upload_file.files[0].size;
            if(file_size > 0) {
                file_size = (file_size / 1024 / 1024).toFixed(2);
            }
            var image_upload_size_ctl = document.getElementById('image_upload_size');
            image_upload_size_ctl.textContent = 'Selected photo file size:' + file_size + ' MB';
        });
    }
}

function verifyRecaptchaNotEmpty() {
    if (typeof grecaptcha !== 'undefined') {
        var response = grecaptcha.getResponse();
        if(response.length == 0) {
            //reCaptcha not entered
            alert('Please fill out the anti spam field, before submitting!');
            return false;
        }
        else {
            //reCaptch entered
            return true;
        }
    }
    return true;
}
