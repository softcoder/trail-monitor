# trail-monitor
A Wordpress Trail Status Updates plugin for backcountry users

Full details of this plugin can be found here: https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/  

=== Trail Monitor ===
Contributors: softcoder
Donate link: https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
Tags: trail, trails, outdoors, biking, hiking, widget, shortcode
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 5.6
Stable tag: 2.2
License: GPLv3

Display the status of trails on your website.

== License ==
Released under the terms of the GNU General Public License.

== Description ==
This plugin allows you to display the status of trails on your website. Any user can add a trail status (for review), while only editors and admin can add and update the trails and status names. The trails can have links to pages with more information about them. Both the shortcodes and widget are customizable. Widgets and shortcode can have different sets of trails and notes.

= Features =
*   Widget
*   Shortcode for showing:
*     - Trails in a list format
*     - Trails in a block format
*     - Add trail status (with optional anti spam google recaptcha)
*   Add images to trails for display in the shortcode
*   Placeholder images for trails without images
*   Add links to the trails
*   Support for embedded youtube video in the comment field
*   Set up statuses including their color
*   Trail name and status can be the status color
*   The order of both trails and statuses can be set
*   Notes can be added at the end which can include embedded videos
*   Separate notes for the shortcode and widget
*   Initial set of trail conditions
*   Admin that allows authors and higher users to update the current conditions

DISCLAIMER: Under no circumstances do we release this plugin with any warranty, implied or otherwise. We cannot be held responsible for any damage that might arise from the use of this plugin. Back up your WordPress database and files before installation.

== Installation ==

= Plugin Settings = 
Hiking Trail 'Category Type' to Monitor Statuses: Optionally lets you indicate which wordpress post category relates to trails. These are then selectable in the Name field when entering new trail statuses.
Google Recaptcha API Key v2 (anti-spam): Optional anti spam Google recaptcha API Site key.
Google Recaptcha API Secret Key v2 (anti-spam): Optional anti spam Google recaptcha API Secret key.
Send Notifications 'from' Email Address: Optional email address to send notifications 'from'. (if none specified we usue the default wordpress admin email)
Send Notifications 'to' Email Address: Optional email address to send notifications. (currently one email per day is sent if at least 1 trail status has been submitted on that day. The wordpress 'admin' email is used as the 'from' sender email address)

= Shortcode Usage =
Trail List Shortcode: [trail-status]
Trail Blocks Shortcode: [trail-status-blocks]
Trail Table List Shortcode: [trail-status-list]
Add Trail Status Shortcode: [trail-status-submit]
Embed Youtube video Shortcode: [trail-status-youtube]

= Options =
show_images: Show's the trail image. Trails without an image will get a placeholder image. (default: yes)
color_text: Set the text color for the trail name and status by the status. (default: yes)
box_shadow: Add box shadow to trail images. (default: yes)
small_images: If yes, the max-width of the trail images will be 50px. If no, the image will be the full thumbnail size. (default: no)
border: If yes, the table list will show a border. (default: no)

= Examples =
[trail-status]
[trail-status show_images="yes" color_text="no" box_shadow="yes" small_images="yes"]
[trail-status-blocks show_images="yes" color_text="yes" box_shadow="no" small_images="no"]
[trail-status-list show_images="yes" color_text="no" box_shadow="yes" small_images="yes" border="no"]
[trail-status-submit]
[trail-status-youtube width="300" height="300" src="https://youtu.be/E8x8VqCPn5g"]

= Features =
Trail Statuses may be submitted by unregistered users with optional photo preview, but are unapproved until reviewed by an admin
Optional email notifications may be sent to admins when a user has submitted a new trail status update
Filters to show unapproved trail status submissions
Ability to bulk update trail statuses
Ability to link trail status updates with wordpress posts containing a specified psot category
Optional anti-spam google recaptcha integration

== Screenshots ==

== Changelog ====
1.0.0 (2025-04-29)
- Initial WordPress.org release.

== Frequently Asked Questions ==

= Can I use HTML in the notes? =
* Yes. You can also use iframes to add things like embedded videos.
