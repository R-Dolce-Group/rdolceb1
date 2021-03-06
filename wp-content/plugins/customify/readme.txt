=== Customify - A Theme Customizer Booster ===
Contributors: pixelgrade, euthelup, babbardel, vlad.olaru, cristianfrumusanu, raduconstantin, razvanonofrei
Tags: customizer, css, editor, live, preview, customizer
Requires at least: 4.7.0
Tested up to: 4.9.8
Stable tag: 1.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customify is a Theme Customizer Booster that you can easily use to customizer Fonts, Colors, Live CSS Editor and other options for your site.

== Description ==

With [Customify](https://github.com/pixelgrade/customify), developers can easily create **advanced theme-specific options** inside the WordPress Customizer. Using those options, a user can make presentational changes without having to know or edit the theme code.

**[Types of Fields](https://github.com/pixelgrade/customify#list_of_fields)**

* **Color.** A color picker used to control any text or background color of an element.

* **Font.** A real font selector which includes a flexible library of fonts(Fonto, google fonts or added via fliter) and allows you to customize, with live preview, properties like font-weight, size, letter spacing, text align, text decoration etc.

* **Typography.** (Deprecated) A series of typographic options that allow you to access the massive **Google Fonts library** and make them available inside your theme customizer.

* **CSS Editor.** A powerful **Live CSS Editor** directly into your customizer! Useful for better control over the appearance of your theme without the need to create a child theme or worry about theme updates overwriting your customizations.

* **Text Field.** A simple text field that allows you to customize elements like Site Title or Footer Credits.

* **Select Dropdown.** A drop-down menu selector to be used when you have to choose from multiple options.

* **Range.** The html5 range element can be used to select number values.

* **[Preset](https://github.com/pixelgrade/customify/blob/master/README.md#presets_title).** A field which allows you to change a group of Customify fields.

* **And more others [this is the full list](https://github.com/pixelgrade/customify#list_of_fields)

**Made with love by Pixelgrade**

== Credits ==

* [Select2](https://select2.github.io) JavaScript library - License: MIT
* [CSSOM.js](https://github.com/NV/CSSOM) JavaScript library - License: MIT
* [Ace Editor](https://ace.c9.io/) JavaScript editor - License: BSD
* [jQuery React](https://github.com/natedavisolds/jquery-react) JavaScript jQuery plugin - License: MIT
* Default [image](https://unsplash.com/photos/OgM4RKdr2kY) for Style Manager Color Palette control - License: (Unsplash)[https://unsplash.com/license]

== Changelog ==

= 1.9.0 =
* Added ability to modify existing Customizer panels, sections, controls
* Added system for admin notifications
* Overall enhancements for more performance and stability

= 1.8.0 =
* Added altered state for colors in the current color palette when any of the controls connected to the color has been modified
* Added the colors from the current palette to all the color pickers in the Theme Options section
* Fixed bug where default values were being forced in Customizer Preview at first load
* Fixed bug preventing CSS output for color controls in the Style Manager section of the Customizer

= 1.7.4 =
* Reorganized Customizer custom sections and grouped them into Theme Options, thus making the Style Manager panel stand out.
* Refactored parts for more performance and clarity.

= 1.7.3 =
* Added HEX field for colors in the current Color Palette
* Updated Google Webfonts list

= 1.7.2 =
* Fixed issue with **Color Palettes** working only after choosing one variation
* Fixed bug preventing some options to live update the Customizer preview

= 1.7.1 =
* Fixed issue with **Color Palettes** overwriting custom colors in Live Preview

= 1.7.0 =
* Added **Dynamic Color Palettes** for a smoother experience
* Fixed issue with the Style Manager crashing the Customizer when not using a theme with support for it.

= 1.6.5 =
* Added **Color Palettes Variations** to the Style Manager Customizer section
* Improved Color Palettes logic to better handle differences between various color palettes
* Improved master color connected fields logic to allow for a smoother experience
* Updated Google Fonts list
* Fixed some issues with the connected fields logic
* Fixed some Customizer preview scaling issues
* Fixed a potential bug with the options' CSS config (multiple configs with the same property but with different selectors)

= 1.6.0 =
* Added **Style Manager** Customizer section with theme supports logic
* Added connected fields logic for easy chaining of Customizer controls
* Fixed a couple of styling inconsistencies regarding the Customizer

= 1.5.7 =
* Improved development logic for easier testing
* Improved and fixed reset settings buttons
* Fixed a couple of styling inconsistencies regarding the Customizer

= 1.5.6 =
* New Fields Styling Improvements

= 1.5.5 =
* Added Compatibility with WordPress 4.9

= 1.5.4 =
* Allow 0 values for fonts line-height and letter-spacing
* Improved the plugin loading process and the CSS inline output
* Fixed small style issues for the Customizer bar

= 1.5.3 =
* Update Style for WordPress 4.8
* Updated Google Fonts list
* Fixed the double output of the custom CSS
* Fixed Menu Add Button overlap

= 1.5.2 =
* Fixed Background field output
* Fixed Font's preview in wp-editor
* Added Reset Theme Mods tool

= 1.5.1 =
* Added support for `active_callback` argument for customizer controls
* Customizer assets refactor

= 1.5.0 =
* Plugin core refactored for a better performance
* Fixed Font Weight saving
* Fixed Font Subset saving
* Fix Select2 enqueue_script

= 1.4.2 =
* Improved Font style output in front-end. Now is just one style element with all the fonts inside.
* Improved Fonts panels, now only one can be opened to avoid confusion
* Fixed Presets with fonts
* Fixed Google Fonts with italic weights
* Fixed Range input field
* Small Fixes

= 1.4.1 =
* Fixed Multiple local fonts

= 1.4.0 =
* Make Customify compatible with the [4.7 customizer changes](https://make.wordpress.org/core/2016/10/12/customize-changesets-technical-design-decisions)
* Add `show_if` [config option](https://github.com/pixelgrade/customify#conditional-fields)
* Fix Conflict with Jetpack - Related posts
* Fix Javascript callbacks loss
* Switch de default storage from option to theme_mod
* Fixed Incorrect Color Panel Height
* Fixed Font field weight in customizer preview

= 1.3.1 =
* Fixed compatibility with PHP <= 5.3.x

= 1.3.0 =
* Added the new and awesome `font` selector
* The live CSS editor is now removed for 4.7, but don't worry, your style will be imported into the new [CSS Editor](https://make.wordpress.org/core/2016/11/26/extending-the-custom-css-editor/)
* Added compatibility with 4.7

= 1.2.7 =
* Added capability to control the Jetpack Sharing default options

= 1.2.6 =
* Added capability to define Jetpack default and hidden modules

= 1.2.5 =
* Fixed WordPress 4.7 incompatibilities

= 1.2.4 =
* Added: Support for Fonto plugin
* Improved the font selector
* Fixed presets on ssl

= 1.2.3 =
* Added: Support for conditional fields display
* Fixed weights for local fonts
* Fixed Ace editor warnings
* Fixed some rare PHP warnings

= 1.2.2 =
* Added: Customizer styling
* Fixed some rare warnings with google fonts

= 1.2.1 =
* Improve default fonts parse, and fix some legacy cases
* Remove google api code when google fonts is disabled

= 1.2.0 =
* Added: Compatibility with WordPress 4.4.0
* Added: Presets can now set fonts and font weights
* Fixed: Now range fields can have `0` as default
* Fixed: Font subsets style
* Fixed: Fixed some PHP and javascript warnings
* Updated: Font field style

= 1.1.7 =
* Added: Compatibility with WordPress 4.3.1
* Added: Custom fonts can be used now as defaults
* Fixed: Fonts preview
* Fixed: Some rare errors with PHP 5.2.x
* Fixed: Some font variants warnings with PHP 5.2.x

= 1.1.6 =
* Added: Custom background field with bacgkround-* css properties selects
* Added: Compatibility with WordPress 4.3.x
* Added: Compatibility with PHP 5.2.x
* Improved: Live CSS Editor is now live...for real
* Updated: ACE Editor
* Updated: The list of google fonts is now up to date


= 1.1.5 =
* Added: Live-preview support for `text` and `textarea` fields.
* Added: **Unit** parameter for css values(now we can use all the css units like em, rem, vh, all of them :D).
* Fixed: Editor style for Typekit fonts.
* Fixed: Editor style with default values.
* Fixed: Live Preview small fixes
* Updated: The list of google fonts is now up to date

= 1.1.4 =
* Added: Ace Editor field.
* Added: HTML field.
* Added: Sanitize callbacks parameter and a default sanitizer for the checkbox field.
* Fixed: Slight styling issues.

= 1.1.2 =
* Added: Option to add Customify's changes in the editor.
* Added: Possibility to load Typekit fonts through config.

= 1.1.1 =
* Added: Radio input with image label.
* Added: Javascript callback for css properties.
* Update: Updated Ace editor.

= 1.1.0 =
* Added: [Preset](https://github.com/pixelgrade/customify/blob/master/README.md#presets_title) field type.
* Added: Reset buttons (disabled by default).
* Added: Button field.

== Installation ==

1. Install Customify either via the WordPress.org plugin directory, or by uploading the files to your `/wp-content/plugins/` directory
2. After activating Customify go to `Appearance → Customize` and have fun with the new felds
3. For further instructions and how to setup your own fields, read our [detailed documentation](http://github.com/pixelgrade/customify/blob/dev/README.md)

== Frequently Asked Questions ==

= Is there a way to reset Customify to defaults? =
Reset buttons are available for all the options or for individual sections or panels.
They are disabled by default to avoid useless/accidental resets.
To enable them simply go to Dashboard -> Settings -> Customify and check "Enable Reset Buttons"
