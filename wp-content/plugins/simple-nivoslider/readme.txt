=== Simple NivoSlider ===
Contributors: Katsushi Kawamori
Donate link: https://shop.riverforest-wp.info/donate/
Tags: slider, nivoslider, jquery, gallery, images
Requires at least: 4.7
Requires PHP: 5.6
Tested up to: 5.8
Stable tag: 5.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates NivoSlider into WordPress.

== Description ==

= Integrates NivoSlider into WordPress. =
* Add effect to images inserted into WordPress posts, custom posts and pages.
* Add effect to the WordPress gallery.
* Apply the effect by embedding the shortcode into text field.
* Apply the effect by embedding the shortcode into template.

== Installation ==

1. Upload `simple-nivoslider` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
= Write a Shortcode. The following text field. Enclose image tags and gallery shortcode. =
* Example
`[simplenivoslider]<a href="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f8e6a6a7.jpg"><img src="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f8e6a6a7.jpg" alt="" width="1000" height="626" class="alignnone size-full wp-image-275" /></a>
<a href="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f878ff71.jpg"><img src="http://blog3.localhost.localdomain/wp-content/uploads/sites/8/2017/01/f878ff71.jpg" alt="" width="1000" height="666" class="alignnone size-full wp-image-274" /></a>[gallery size="full" ids="273,272,271,270"][/simplenivoslider]`
= Write a Shortcode. The following template. Enclose image tags and gallery shortcode. =
* Example
`<?php echo do_shortcode('[simplenivoslider controlnav="false"][gallery link="none" size="full" ids="271,270,269,268"][/simplenivoslider]'); ?>`

== Frequently Asked Questions ==

none

== Screenshots ==

1. Example
2. Short code
3. Add button to Quicktags
4. Nivo Slider Settings

== Changelog ==

= 5.12 =
Supported WordPress 5.6.

= 5.11 =
Can specify without writing the shortcode.

= 5.10 =
Conformed to the WordPress coding standard.

= 5.09 =
Removed unnecessary code.

= 5.08 =
Fixed problem original gallery shortcode.

= 5.07 =
Fixed problem original gallery shortcode.

= 5.06 =
Fixed fine problem.

= 5.05 =
Removed unnecessary code.

= 5.04 =
Fixed problem of shortcode.
Fixed fine problem.

= 5.03 =
Changed donate link.

= 5.02 =
Security measures.

= 5.01 =
Removed unnecessary code.

= 5.00 =
Specialized in operation with just the Shortcode.
Can specify NivoSlider options to Shortcode attributes.

== Upgrade Notice ==

= 5.02 =
Security measures.

