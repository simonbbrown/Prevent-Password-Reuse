=== Prevent Password Reuse ===
Contributors: simonbbrown
Tags: password, security, prevent reuse
Requires at least: 3.7
Tested up to: 4.0
Stable tag: 1.1

== Description ==

This plugin keeps records of your users hashed passwords in order to prevent them from reusing their current or a previous password

== Installation ==

1. Download
2. Upload to your `/wp-contents/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

Q: Are stored passwords secure?
A: The passwords are as secure as the current password for your user, the plugin only stores the hashed version of the password.

Q: How can I reset the stored passwords?
A: run the following mysql - TRUNCATE TABLE `###password_log` - where ### is your table prefix (this can be found on your wp-config.php

== Changelog ==

= 1.1 =
Code refactoring for best practices.

= 1.0 =
Initial release version
