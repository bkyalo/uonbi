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
 * A login page layout for the University of Nairobi theme.
 *
 * @package   theme_uonbi
 * @copyright 2022 Catalyst IT Europe, www.catalyst-eu.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$extraclasses[] = 'uonbi-login';
$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$logopos = theme_uonbi_get_setting('logopos');
$slideeffect = theme_uonbi_get_setting('slideeffect');
$hamburgerpos = theme_uonbi_get_setting('hamburgerpos');
$headerbgcolor = theme_uonbi_get_setting('headerbgcolor');

$extraclasses = [];
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
$enablewaiting = theme_uonbi_get_setting('enablewaiting');
$slideurls = get_slideimage_urls();
$hasslides = !empty($slideurls);
$slidetime = theme_uonbi_get_setting('slidetime') * 1000;
$hasslidetime = !empty($slidetime);
$copyright = theme_uonbi_get_setting('copyright_footer', 'format_html');

$social = ($fburl != '' || $linkedinurl != '' || $twurl != '' || $instaurl != '' || $pinurl != '' || $youtubeurl != '');
$contacts = ($address != '' || $phoneno != '' || $emailid != '' || $url !='');

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
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
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'logoposright' => $logopos,
    'enablewaiting' => $enablewaiting,
    'hasslides' => $hasslides,
    'slides' => $slideurls,
    'slidetime' => $slidetime,
    'hasslidetime' => $hasslidetime,
];

$templatecontext['slideeffect'] = false;
if ($slideeffect == 2) {
    $templatecontext['slideeffect'] = true;
}

$templatecontext['hamburgerpos'] = false;
if (!empty($theme->settings->hamburgerpos)) {
    $templatecontext['hamburgerpos'] = true;
}

$loginbtn = theme_uonbi_get_setting('loginbtn');
$loginbtnshow = $loginbtn == 2;
if (!empty($loginbtnshow) && !isloggedin()) {
    $templatecontext['loginbtn'] = true;
}

echo $OUTPUT->render_from_template('theme_uonbi/login', $templatecontext);
