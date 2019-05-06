# Magic Hat 1.0.0
## The "do it for me" starter theme!
Magic Hat is a starter theme based on [\_s](https://github.com/Automattic/_s) with one basic goal: to provide a theme with layout, functionality, and file organization already complete, so you can focus on colors, fonts, images... you know, the fun part.

## Features
- Built-in breadcrumbs eliminate the need for a plugin (which usually requires you to stick a function in your template files anyway!).
- Images and galleries linked to their direct media file urls (not attachment pages) will open in Swipebox.
- Post comments and load comment pages via ajax.
- Custom header, background, and logo, plus a slew of built-in options:
  - Show/hide link underlines
  - Set copyright text
  - Show/hide theme credit
  - Set sidebar side
  - Boxed/full-width layout
  - Show/hide adjacent post navigation
  - Enable/disable ajax comment script
  - Enable/disable lightbox
- Front-end Gutenberg block styles.
- Support for some Jetpack features and Roots Soil.

## Installation
### Requirements
1. WordPress 4.8+
1. php 7.0+

To install the theme:
1. Clone the repository to your wp-content/themes folder, or
1. Download a zip archive of the theme and upload it to WordPress through the Add a Theme page.

## Usage (basic)
If you only need to tweak a little bit or don't want to compile your own Sass, you can use Magic Hat as a theme by itself without any problems, and make adjustments as necessary in the Customizer.

You can also make a Magic Hat child theme. Making a child theme offers more robust customization options (like editing html markup) without having to get your hands too dirty.
1. Install Magic Hat.
1. Create a new directory in your wp-content/themes folder. The WordPress codex would advise naming your child theme directory in the style of "magic-hat-child," but you can pick whatever name you want.
1. Make a file called <code>style.css</code> in your child theme folder.
1. Open <code>style.css</code> and add the following:
```
/*
 Theme Name:   [Your Theme Name]
 Template:     magic-hat
 Text Domain:  [your-theme-slug]
 Theme URI:    [https://example.com/your-theme-name]
 Description:  [Magic Hat child theme]
 Author:       [Your Name]
 Author URI:   [https://example.com]
 Version:      1.0.0
 License:      [GNU General Public License v3 or later]
 License URI:  [http://www.gnu.org/licenses/gpl-3.0.html]
 Tags:         blog, portfolio, one-column, two-columns, left-sidebar, right-sidebar, custom-background, custom-logo, custom-header, custom-menu, featured-images, footer__widgets, full-width-template, post-formats, sticky-post, threaded-comments, translation-ready
*/
```
Only the first two lines are necessary. Edit the items in brackets to suit your new theme.
1. Activate your child theme in WordPress.

Now you should be able to add styles as necessary to <code>style.css</code>. Place any new functions you want to add (or pluggable functions you want to replace) in <code>functions.php</code>. To replace any template files you don't like, just create a file of the same name in your child theme folder.

## Usage (advanced)
To make your own theme based on Magic Hat:

1. Find and replace "Magic Hat", "Magic_Hat", "magic-hat", and "magic_hat" in all theme files with your appropriate corresponding theme slug.

For most cosmetic changes, take a look at <code>assets/variables/\_skin.scss</code>, which contains aesthetic style declarations like fonts and colors.

### Linting/Compiling Scripts
Magic Hat comes with a <code>gulpfile</code> for Gulp 4.0+, which handles autoprefixing, compiling, and minifying scss and linting/minifying javascript.

It also has a <code>package.json</code> flie configured for use with Stylelint 9.0+ (with stylelint-config-wordpress 13.1+) and Eslint 5.0+ (with eslint-config-wordpress 2.0+).

PHP files are set up to work with PHP_CodeSniffer 3.3.1+ using the WPThemeReview standard 0.1.0.

## Todo
- Screenshot
- Post formats plugin
- Ajax for delete/spam/edit comment buttons
- Author bios after post
- Matching back-end editor styles for Gutenberg blocks
- (Jetpack) Customizer option to change where sharing buttons are displayed (above or below post)
- (Jetpack) Infinite scrolling
- Woocommerce support
- Option to hide the "last updated" date on posts

## License
GNU GPL 3.0 or later; see LICENSE for third-party libraries used.

## Releases
### 1.0.0
- Initial release. It probably has many bugs.
