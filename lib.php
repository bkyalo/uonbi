<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lib.php
 *
 * @package   theme_uonbi
 * @copyright 2022 onwards Catalyst IT Europe <http://catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Icon name regex. Allow only string from 'fa-' (Font Awesome) or 'icon-' (Simple Line Icons).
if (!defined('THEME_UONBI_SETTINGS_ICON_REGEX')) {
    define('THEME_UONBI_SETTINGS_ICON_REGEX', '/^((fa-)|(icon-))[a-z\-]+$/');
}

/**
 * Get the pre scss for the theme
 * @param string $theme
 * @return string $scss.
 */
function theme_uonbi_get_pre_scss($theme) {
    global $CFG, $PAGE;

    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['primary'],
        'brandcolorsecond' => ['secondary'],
        'buttonscolor' => ['btn-primary-bg'],
        'fonttype' => ['font-family-sans-serif'],
        'linkcolor' => ['link-color'],
        'headingscolor' => ['headings-color'],
        'footerbgcolor' => ['footer-bg'],
        'pagebgcolor' => ['body_bg'],
        'sidebgcolor' => ['nav-drawer-bg'],
        'headerheight' => ['navbar-height'],
        'headerborder' => ['navbar-border-height'],
        'headerbordercolor' => ['navbar-border-color'],
        'headerbgcolor' => ['navbar-bg-custom'],
        'headerlinkcolor' => ['navbar-link-color'],
        'navdrawertextcolor' => ['nav-drawer-text-color'],
        'navdrawerlink' => ['navdrawerlink']
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }
    $scss .= theme_uonbi_set_fontwww();

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_uonbi_get_extra_scss($theme) {

    return !empty($theme->settings->customcss) ? $theme->settings->customcss : '';

}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_uonbi_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_uonbi', 'preset', 0, '/', $filename))) {
        // This preset file was fetched from the file area for theme_uonbi and not theme_boost (see the line above).
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    // uonbi scss.
    $uonbivariables = file_get_contents($CFG->dirroot . '/theme/uonbi/scss/uonbi/_variables.scss');
    $uonbi = file_get_contents($CFG->dirroot . '/theme/uonbi/scss/uonbi.scss');

    // Combine them together.
    return $uonbivariables . "\n" . $scss . "\n" . $uonbi;
}

/**
 * Load the Jquery and migration files
 * Load the our theme js file
 *
 * @param  moodle_page $page [description]
 */
function theme_uonbi_page_init(moodle_page $page) {
    $page->requires->jquery();
    $page->requires->js('/theme/uonbi/javascript/theme.js');
}

/**
 * Loads the CSS Styles.
 *
 * @param string $css
 * @param string $theme
 * @return string
 */
function theme_uonbi_process_css($css, $theme) {
    global $OUTPUT, $CFG;
    $frontbg = $theme->setting_file_url('frontbg', 'frontbg');
    $css = theme_uonbi_pre_css_set_fontwww($css);
    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    return $css;
}

/**
 * Adds the frontpage background to CSS.
 *
 * @param string $scss The CSS.
 * @param string $frontbg The URL of the frontpage background.
 * @return string The parsed CSS
 */
function theme_uonbi_set_frontbg($scss, $frontbg) {
    $tag = '[[setting:frontbg]]';
    $replacement = $frontbg;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $scss = str_replace($tag, $replacement, $scss);
    return $scss;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_uonbi_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $theme = theme_config::load('uonbi');

    if (empty($theme)) {
        $theme = theme_config::load('uonbi');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {

    if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'logo') {
        return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
    } else if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'frontbg') {
        return $theme->setting_file_serve('frontbg', $args, $forcedownload, $options);
    } else if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'slideimages') {
        return $theme->setting_file_serve('slideimages', $args, $forcedownload, $options);
    } else if ($filearea === 'favicon') {
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve('favicon', $args, $forcedownload, $options);
    } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Serves CSS for image file updated to styles.
 *
 * @param string $filename
 * @return string
 */
function theme_uonbi_serve_css($filename) {
    global $CFG;
    if (!empty($CFG->themedir)) {
        $thestylepath = $CFG->themedir . '/uonbi/style/';
    } else {
        $thestylepath = $CFG->dirroot . '/theme/uonbi/style/';
    }
    $thesheet = $thestylepath . $filename;
    $etagfile = md5_file($thesheet);
    // File.
    $lastmodified = filemtime($thesheet);
    // Header.
    $ifmodifiedsince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
    $etagheader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
    if ((($ifmodifiedsince) && (strtotime($ifmodifiedsince) == $lastmodified)) || $etagheader == $etagfile) {
        theme_uonbi_send_unmodified($lastmodified, $etagfile);
    }
    theme_uonbi_send_cached_css($thestylepath, $filename, $lastmodified, $etagfile);
}

/**
 * Set browser cache used in php header.
 *
 * @param  string $lastmodified
 * @param  string $etag
 */
function theme_uonbi_send_unmodified($lastmodified, $etag) {
    $lifetime = 60 * 60 * 24 * 60;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}

/**
 * Cached css.
 * @param  string $path
 * @param  string $filename
 * @param  int $lastmodified
 * @param  string $etag
 */
function theme_uonbi_send_cached_css($path, $filename, $lastmodified, $etag) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php');
    // 60 days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;
    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($path . $filename));
    }
    readfile($path . $filename);
    die;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_uonbi_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add Clean specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_uonbi_get_html_for_settings(renderer_base $output, moodle_page $page) {
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">'.format_text($page->theme->settings->footnote).'</div>';
    }

    return $return;
}

/**
 * Loads the CSS Styles and put the font path
 *
 * @return string
 */
function theme_uonbi_set_fontwww() {
    global $CFG;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot."/theme";
    } else {
        $themewww = $CFG->themewww;
    }
    $theme = theme_config::load('uonbi');
    $fontwww = '$fontwww: "'. $themewww.'/uonbi/fonts/"'.";\n";
    return $fontwww;
}

/**
 * Process the css for font url
 * @param string $css
 * @return string
 */
function theme_uonbi_pre_css_set_fontwww($css) {
    global $CFG;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot."/theme";
    } else {
        $themewww = $CFG->themewww;
    }
    $tag = '[[setting:fontwww]]';
    $theme = theme_config::load('uonbi');
    $css = str_replace($tag, $themewww.'/uonbi/fonts/', $css);
    return $css;
}

// Loads the login page background image paths
// @ return string.

if (!function_exists('get_slideimage_urls')) {
    /**
     * Description
     * @return array
     */
    function get_slideimage_urls() {
        static $theme;
        if (empty($theme)) {
            $theme = theme_config::load('uonbi');
        }

        // Discover all images in the filearea, then iterate through.
        $fs = get_file_storage();
        $context = context_system::instance();
        $themename = 'theme_' . $theme->name;
        $bannerfiles = $fs->get_area_files($context->id, $themename, 'slideimages', 0);

        $urls = [];
        $first = true;
        foreach ($bannerfiles as $file) {
            if ($file->is_valid_image()) {
                $urls[] = ['first' => $first, 'url' => moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                )];
                $first = false;
            }
        }

        return $urls;
    }
}

// Loads the front page background image
// @ return string.
if (!function_exists('get_frontbg_url')) {
    /**
     * Description
     * @return type|string
     */
    function get_frontbg_url() {
        static $theme;
        if (empty($theme)) {
            $theme = theme_config::load('uonbi');
        }
        $frontbg = $theme->setting_file_url('frontbg', 'frontbg');
        return $frontbg;
    }
}

/**
 * Extend the University of Nairobi navigation
 *
 * @param flat_navigation $flatnav
 */
function theme_uonbi_extend_flat_navigation(theme_uonbi\nav\flat_navigation $flatnav) {

    theme_uonbi_delete_menuitems($flatnav);

    theme_uonbi_add_coursesections_to_navigation($flatnav);
}

/**
 * Remove items from navigation
 *
 * @param flat_navigation $flatnav
 */
function theme_uonbi_delete_menuitems(theme_uonbi\nav\flat_navigation $flatnav) {

    $itemstodelete = [
        'coursehome',
        'courseindexpage'
    ];

    foreach ($flatnav as $item) {
        if (in_array($item->key, $itemstodelete)) {
            $flatnav->remove($item->key);

            continue;
        }

        if (isset($item->parent->key) && $item->parent->key == 'mycourses' &&
            isset($item->type) && $item->type == \navigation_node::TYPE_COURSE) {

            $flatnav->remove($item->key, \navigation_node::TYPE_COURSE);
        }
    }
}

/**
 * Improve flat navigation menu
 *
 * @param flat_navigation $flatnav
 */
function theme_uonbi_add_coursesections_to_navigation(theme_uonbi\nav\flat_navigation $flatnav) {
    global $PAGE;

    $participantsitem = $flatnav->find('participants', \navigation_node::TYPE_CONTAINER);

    if (!$participantsitem || !in_array($participantsitem->key, $flatnav->get_key_list())) {
        return;
    }

    if ($PAGE->course->format != 'singleactivity') {
        $coursesectionsoptions = [
            'text' => get_string('coursesections', 'theme_uonbi'),
            'shorttext' => get_string('coursesections', 'theme_uonbi'),
            'icon' => new pix_icon('t/viewdetails', ''),
            'type' => \navigation_node::COURSE_CURRENT,
            'key' => 'course-sections',
            'parent' => $participantsitem->parent
        ];
    }

    $mycourses = $flatnav->find('mycourses', \navigation_node::NODETYPE_LEAF);
    $privatefiles = $flatnav->find('privatefiles');

    if (!empty($mycourses) && !empty($privatefiles)) {
        $flatnav->remove($mycourses->key);

        $flatnav->add($mycourses, 'privatefiles');
    }
}

/**
 * Check if a certificate plugin is installed.
 *
 * @return bool
 */
function theme_uonbi_has_certificates_plugin() {
    $simplecertificate = \core_plugin_manager::instance()->get_plugin_info('mod_simplecertificate');

    $customcert = \core_plugin_manager::instance()->get_plugin_info('mod_customcert');

    if ($simplecertificate || $customcert) {
        return true;
    }

    return false;
}

/**
 * Function to get the theme setting
 * @param  string $setting
 * @param  boolean $format
 * @return string
 */
function theme_uonbi_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('uonbi');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}

function theme_uonbi_strip_html_tags( $text ) {
    $text = preg_replace(
        array(
            // Remove invisible content.
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks.
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
            ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
            ),
        $text
        );
return strip_tags( $text );
}

/**
 * Cut the Course content.
 *
 * @param $str
 * @param $n
 * @param $end_char
 * @return string
 */
function theme_uonbi_course_trim_char($str, $n = 500, $endchar = '&#8230;') {
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));
    if (strlen($str) <= $n) {
        return $str;
    }

    $out = "";
    $small = substr($str, 0, $n);
    $out = $small.$endchar;
    return $out;
}

// Return the current theme url.
// @ return string.
if (!function_exists('theme_url')) {
    /**
     * theme_url
     *
     * @return string
     */
    function theme_url() {
        global $CFG, $PAGE;
        $themeurl = $CFG->wwwroot.'/theme/'. $PAGE->theme->name;
        return $themeurl;
    }
}

/**
 * Get the current copyright year
 */
function theme_uonbi_get_copyright_year() {
    return userdate(time(), '%Y');
}

/**
 * Helper function to reset the icon system used as updatecallback function when saving some of the plugin's settings.
 */
function theme_uonbi_reset_icon_caches() {
    theme_reset_all_caches();
    js_reset_all_caches();
}

/**
 * Returns all custom fields shortname.
 *
 * @return array
 */
function get_course_custom_fields_shortname() {
    global $DB;
    $records = $DB->get_records('customfield_field', array('type' => 'textarea'), 'id ASC');
    $customfields = array_column($records, 'shortname');
    return $customfields;
}

/**
 * Returns given course's custom fields value.
 *
 * @param core_course_list_element $course
 * @return array
 */
function get_course_custom_fields_value($course) {
    $handler = \core_customfield\handler::get_handler('core_course', 'course');
    $datas = $handler->get_instance_data($course->id);
    $customfields = [];
    foreach ($datas as $data) {
        if (empty($data->get_value())) {
            continue;
        }
        $customfields[$data->get_field()->get('shortname')] = $data->get_value();
    }
    return $customfields;
}
