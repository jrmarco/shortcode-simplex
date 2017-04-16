=== Shortcode Simplex ===
Contributors: jrmarco
Donate link: https://dev.bigm.it
Tags: shortcode, shortcodes, short code, services, service, plugin, admin, document, documents, international, adsense, jquery
Requires at least: 2.9
Tested up to: 4.7.2
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This lightweight plugin permit to create and manage easily user define Shortcode inside Wordpress. 

== Description ==

Shortcode Simplex it's a lightweight Shortcode plugin creator, permit all WP Admins to define new shortcode ( single or enclosed one ) to any kind of purpose. With it's minimal human readable and 
intuitive interface you can create and manage all your Shortcode.   

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the Wordpress plugins screen directly.
1. Result name will be: './wp-content/plugins/shortcode_simplex/'
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Click on the Shortcode Spx menu to create and edit your Shortcode 

== Frequently Asked Questions ==

= How can I create a shortcode ? =
Click on the *Shortcode Spx* menu. On the admin page, click on [Create New] : a form will be prompted asking for the shortcode caller name, some notes to remember what this shortcode do, a text area where to write or paste the code you like to print with this shortcode
= How can I edit a shortcode ? =
Click on the *Shortcode Spx* menu. On the admin page a list of the user created shortcode is shown. Click on the [Edit] button to edit the relative shortcode and make change into the form
= How can I delete a shortcode ? =
Click on the *Shortcode Spx* menu. On the admin page a list of the user created shortcode is shown. Click on the [Delete] button to permanently delete the relative shortcode. Once deleted the shortcode can't be recovered
= I have saved several shortcode, but nothing is shown on the topic/page =
Check on the Plugin page if Shortcode Simplex is active. If not, active now
= I have created a shortcode with PHP code inside, but is not working =
Shortcode Simplex does not parse PHP code, Wordpress handle it as text. Your code will be inside the page source, as commented text. Ex. <!--?php HERE-WILL-BE-YOUR-COMMENTED-PHP-CODE ?--> 
= I would like to show more then 10 shortcode per page. How can I change it? =
To change the number of shortcode shown in the admin page edit the *define( 'SCSMG_VAR_MAX_RES',10);* inside the shortcode_simplex_utility.php file. Instead of 10 place the number of elements you prefer

== Screenshots ==

== Changelog ==

= 1.0 =
* Initial release
