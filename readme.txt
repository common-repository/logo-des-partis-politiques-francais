=== Logos des partis politiques francais ===
Contributors: bastho, ecolosites
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RR4ACWX2S39AN
Tags: widget
Requires at least: 3.1
Tested up to: 5.2.3
Stable tag: stable
License: CC BY-NC 3.0
License URI: http://creativecommons.org/licenses/by-nc/3.0/



== Description ==
A widget to display logos and make a link to major french political movements

= Parameters =
* Custom title (optionnal)
* Selectbox to choose movement
* Size (small, medium, large)
* Custom sizes (optionnal : width, height)
* Custom link (optionnal)

Available languages :
FR
EN

.pot ready to translate file.


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress admin
3. You can easily add or remove logos by uploading /deleting files from plugin_directory/logos/ folder.
Just respect the filename format as well :
ACCRONYM_urlofthelink.jpg
you can use .jpg .gif or .png images
You may use larger images you can, because the plugins caches resized images.
The cache folder is plugin_directory/logos/resized, so cached images can be used by many sites on a multisite plateform and will be deleted on plugin uninstallation.

== Frequently asked questions ==

= Can I add / remove / change logos ? =
Yes, referre to installation instructions

= Can I edit default links ? =
Yes, referre to installation instructions


== Screenshots ==

<img src="http://ecolosites.eelv.fr/files/2013/06/logopartipolitiquefrancais.png"/>

== Changelog ==

= 1.6 =
release date: sept. 2019

* Load CSS only if needed
* Manage images size even if GD lib is not available
* Update logos : LFI, PS, RM, RN
* Code refactoring

= 1.5.0 =
release date: aug. 27 2015

* Lighter logos
* target option : _blank or _self

= 1.4.3 =
* more WP 4.3 compliant

= 1.4.2 =
release date: aug. 14 2015

* WP 4.3 compliant

= 1.4.1 =
* Add: custom class option
* Fix: better check before generating style attribute

= 1.4.0 =
* Add: full size option
* Add: transparent background
* Add: update logos (EELV, PS, LR)
* Code cleanup

= 1.3.0 =
* Add: option to display rounded logotypes

= 1.2.0 =
* Add: New logos (Nouvelle Donne, UDI)
* Add: Cleaner ban management UI
* Add: Widget background color picker
* Fix: Improve security
* Fix: Remove PHP strict-standards warnings

= 1.1.0 =
* Add: Possibility to ban some logos from network

= 1.0.1 =
* Add: Parti Pirate

= 1.0 =
* Plugin release

== Upgrade notice ==

No particular informations
