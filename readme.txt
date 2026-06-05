=== KlenWriter ===
Contributors: artemklen
Tags: dark mode, reading mode, distraction free, accessibility, writing
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds dark mode and distraction-free reading controls to single posts and pages.

== Description ==

KlenWriter is a lightweight WordPress plugin for comfortable reading.

It adds two floating controls to single posts and pages:

* Dark mode
* Distraction-free reading mode

The plugin remembers the visitor's choice between sessions using localStorage and cookies. Dark mode and distraction-free mode work independently and can be enabled together.

More details and screenshots are available at:
https://klenwriter.arklen.ru

GitHub repository:
https://github.com/artemklen/KlenWriter

== Features ==

* Controls appear only on single posts and pages.
* Controls do not appear on the homepage, archive pages, or inside the WordPress admin area.
* User choices are saved between sessions.
* Dark mode and distraction-free mode can be combined.
* Distraction-free mode can hide headers, footers, sidebars, widgets, comments, navigation, and custom selectors.
* Custom author logo upload through the WordPress Media Library.
* Custom button labels.
* Custom dark mode colors.
* Custom reading font size.
* Left or right control position.
* Vanilla JavaScript on the frontend.

== Installation ==

1. Upload the `klenwriter` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins screen in WordPress.
3. Go to Settings -> KlenWriter.
4. Configure the appearance and reading options.

More details and screenshots available at:
https://klenwriter.arklen.ru

== Frequently Asked Questions ==

= Why do the controls not appear on the homepage? =

KlenWriter intentionally displays controls only on single posts and pages.

= Can dark mode and distraction-free mode be enabled at the same time? =

Yes. The two modes are independent and can work together.

= How do I exit distraction-free mode? =

Click the floating return button or press the Esc key.

= What should I do if a theme element remains white in dark mode? =

Go to Settings -> KlenWriter and add that element's CSS selector to the white background elements field.

== Screenshots ==

1. Floating reading controls on a single post.
2. KlenWriter settings page.
3. Dark mode with distraction-free reading enabled.

== Changelog ==

= 1.0 =

* Initial release.

== Support ==

For questions and suggestions, visit:
https://klenwriter.arklen.ru

Or open an issue on GitHub:
https://github.com/artemklen/KlenWriter/issues
