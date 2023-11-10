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
 * A drawer based layout for the boost theme.
 *
 * @package   theme_uonbi
 * @copyright 2022 Catalyst IT Europe, www.catalyst-eu.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
    $courseindexopen = (get_user_preferences('drawer-open-index') == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $navdraweropen = false;
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
    $navdraweropen = false;
}

$logopos = theme_uonbi_get_setting('logopos');
$hamburgerpos = theme_uonbi_get_setting('hamburgerpos');
$headerbgcolor = theme_uonbi_get_setting('headerbgcolor');

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
if ($logopos) {
    $extraclasses[] = 'logo-right';
}
if ($hamburgerpos) {
    $extraclasses[] = 'hamburger-left';
}
if ($headerbgcolor) {
    $extraclasses[] = 'custom-header';
}

$theme = theme_config::load('uonbi');

$blockshtml = $OUTPUT->blocks('side-pre');
$pagetopblockshtml = $OUTPUT->blocks('page-top');
$pagebtmblockshtml = $OUTPUT->blocks('page-btm');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
$haspagetopblocks = (strpos($pagetopblockshtml, 'data-block=') !== false || !empty($addblockbutton));
$haspagebtmblocks = (strpos($pagebtmblockshtml, 'data-block=') !== false || !empty($addblockbutton));

if (!$hasblocks) {
    $blockdraweropen = false;
}

$sectionzero = theme_uonbi_get_setting('sectionzero');
if ($sectionzero == 2) {
    $extraclasses[] = 'sectionzero-heading';
}

$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();
$uppercontent = $OUTPUT->uonbi_course_info();

$hiddencourse = '';
if ($COURSE->id != SITEID) {
    $context = context_course::instance($COURSE->id);
    if ($COURSE->visible == false && (has_capability('moodle/course:update', $context) ||
                                        has_capability('moodle/course:viewhiddencourses', $context))) {
        $courseurl = new moodle_url('/course/edit.php', ['id' => $COURSE->id]);
        $notification = new \core\output\notification(get_string('hiddencourse', 'theme_uonbi', $courseurl->out()), 'info');
        $notification->set_extra_classes(['hiddencourse']);
        $hiddencourse = $OUTPUT->render_from_template($notification->get_template_name(),
                                                        $notification->export_for_template($OUTPUT));
    }
}

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$fburl = theme_uonbi_get_setting('fburl');
$instaurl = theme_uonbi_get_setting('instaurl');
$pinurl = theme_uonbi_get_setting('pinurl');
$youtubeurl = theme_uonbi_get_setting('youtubeurl');
$linkedinurl = theme_uonbi_get_setting('linkedinurl');
$twurl = theme_uonbi_get_setting('twurl');
$url = theme_uonbi_get_setting('url');
$address = theme_uonbi_get_setting('address');
$emailid = theme_uonbi_get_setting('emailid');
$phoneno = theme_uonbi_get_setting('phoneno');
$fontgoogle = theme_uonbi_get_setting('fontgoogle');
$footnote = theme_uonbi_get_setting('footnote');
$footerlinks1 = theme_uonbi_get_setting('footerlinkslist1');
$footerlinks2 = theme_uonbi_get_setting('footerlinkslist2');
$footerlinks3 = theme_uonbi_get_setting('footerlinkslist3');
$footerlinks4 = theme_uonbi_get_setting('footerlinkslist4');
$footerlinksheading1 = theme_uonbi_get_setting('footerlinkslistheading1');
$footerlinksheading2 = theme_uonbi_get_setting('footerlinkslistheading2');
$footerlinksheading3 = theme_uonbi_get_setting('footerlinkslistheading3');
$footerlinksheading4 = theme_uonbi_get_setting('footerlinkslistheading4');
$copyrightfooter = theme_uonbi_get_setting('copyright_footer');
$phone = get_string('phone', 'theme_uonbi');
$email = get_string('email', 'theme_uonbi');
$copyright = theme_uonbi_get_setting('copyright_footer', 'format_html');
$enablewaiting = theme_uonbi_get_setting('enablewaiting');

$social = ($fburl != '' || $linkedinurl != '' || $twurl != '' || $instaurl != '' || $pinurl != '' || $youtubeurl != '');
$contacts = ($address != '' || $phoneno != '' || $emailid != '' || $url !='');

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'haspagetopblocks' => $haspagetopblocks,
    'haspagebtmblocks' => $haspagebtmblocks,
    'fontgoogle' => $fontgoogle,
    'footnote' => $footnote,
    'footerlinks1' => $footerlinks1,
    'footerlinks2' => $footerlinks2,
    'footerlinks3' => $footerlinks3,
    'footerlinks4' => $footerlinks4,
    'footerlinksheading1' => $footerlinksheading1,
    'footerlinksheading2' => $footerlinksheading2,
    'footerlinksheading3' => $footerlinksheading3,
    'footerlinksheading4' => $footerlinksheading4,
    'phoneno' => $phoneno,
    'mailid' => $emailid,
    'fburl' => $fburl,
    'instaurl' => $instaurl,
    'pinurl' => $pinurl,
    'youtubeurl' => $youtubeurl,
    'linkedinurl' => $linkedinurl,
    'twurl' => $twurl,
    'address' => $address,
    'emailid' => $emailid,
    'phoneno' => $phoneno,
    'copyright' => $copyright,
    'social' => $social,
    'contacts' => $contacts,
    'bodyattributes' => $bodyattributes,
    'courseindex' => $courseindex,
    'courseindexopen' => $courseindexopen,
    'navdraweropen' => $navdraweropen,
    'pagetopblocks' => $pagetopblockshtml,
    'pagebtmblocks' => $pagebtmblockshtml,
    'blockdraweropen' => $blockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'uppercontent' => $uppercontent,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'hamburgerposleft' => !empty($hamburgerpos),
    'logoposright' => $logopos,
    'enablewaiting' => $enablewaiting,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'hidden' => $hiddencourse,
];

$templatecontext['hamburgerpos'] = false;
if (!empty($theme->settings->hamburgerpos)) {
    $templatecontext['hamburgerpos'] = true;
}

$templatecontext['hiddencoursealert'] = true;
if (!empty($theme->settings->hiddencoursealert)) {
    $templatecontext['hiddencoursealert'] = false;
}

$loginbtn = theme_uonbi_get_setting('loginbtn');
$loginbtnshow = $loginbtn == 2;
if (!empty($loginbtnshow) && !isloggedin()) {
    $templatecontext['loginbtn'] = true;
}

// Improve boost navigation.
$nav = theme_uonbi\nav\flat_navigation::get_flat_nav($PAGE);
$nav->initialise();
theme_uonbi_extend_flat_navigation(theme_uonbi\nav\flat_navigation::get_flat_nav($PAGE));
$templatecontext['flatnavigation'] = theme_uonbi\nav\flat_navigation::get_flat_nav($PAGE);
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();

echo $OUTPUT->render_from_template('theme_uonbi/drawers', $templatecontext);
