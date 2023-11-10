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
 * settings.php
 *
 * @package   theme_uonbi
 * @copyright 2022 onwards Catalyst IT Europe <http://catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot.'/lib/externallib.php');
use theme_uonbi\external\course;

$settings = null;

if (is_siteadmin()) {

    $settings = new theme_boost_admin_settingspage_tabs('themesettinguonbi', get_string('configtitle', 'theme_uonbi'));
    $ADMIN->add('themes', new admin_category('theme_uonbi', 'uonbi'));

    // General Settings
    $temp = new admin_settingpage('theme_uonbi_generalheader', get_string('generalheading', 'theme_uonbi'));

    // Font replace.
    $name = 'theme_uonbi/fontgoogle';
    $title = get_string('fontgoogle', 'theme_uonbi');
    $description = get_string('fontgoogle_desc', 'theme_uonbi');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Font replace.
    $name = 'theme_uonbi/fonttype';
    $title = get_string('fonttype', 'theme_uonbi');
    $description = get_string('fonttype_desc', 'theme_uonbi');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Primary brand colour.
    $name = 'theme_uonbi/brandcolor';
    $title = get_string('brandcolor', 'theme_uonbi');
    $description = get_string('brandcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Secondary brand colour.
    $name = 'theme_uonbi/brandcolorsecond';
    $title = get_string('brandcolorsecond', 'theme_uonbi');
    $description = get_string('brandcolorsecond_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Links colour.
    $name = 'theme_uonbi/linkcolor';
    $title = get_string('linkcolor', 'theme_uonbi');
    $description = get_string('linkcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Headings colour.
    $name = 'theme_uonbi/headingscolor';
    $title = get_string('headingscolor', 'theme_uonbi');
    $description = get_string('headingscolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Primary buttons colour.
    $name = 'theme_uonbi/buttonscolor';
    $title = get_string('buttonscolor', 'theme_uonbi');
    $description = get_string('buttonscolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Nav background colour.
    $name = 'theme_uonbi/sidebgcolor';
    $title = get_string('sidebgcolor', 'theme_uonbi');
    $description = get_string('sidebgcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Nav drawer text color
    $name = 'theme_uonbi/navdrawertextcolor';
    $title = get_string('navdrawertextcolor', 'theme_uonbi');
    $description = get_string('navdrawertextcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Nav link background colour.
    $name = 'theme_uonbi/navdrawerlink';
    $title = get_string('navdrawerlink', 'theme_uonbi');
    $description = get_string('navdrawerlink', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // temp background colour.
    $name = 'theme_uonbi/pagebgcolor';
    $title = get_string('pagebgcolor', 'theme_uonbi');
    $description = get_string('pagebgcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Custom CSS file.
    $name = 'theme_uonbi/customcss';
    $title = get_string('customcss', 'theme_uonbi');
    $description = get_string('customcssdesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Favicon file setting.
    $name = 'theme_uonbi/favicon';
    $title = get_string('favicon', 'theme_uonbi');
    $description = get_string('favicondesc', 'theme_uonbi');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Waiting spinner.
    $name = 'theme_uonbi/enablewaiting';
    $title = get_string('enablewaiting', 'theme_uonbi');
    $description = get_string('enablewaiting_desc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Course icon.
    $name = 'theme_uonbi/courseicon';
    $title = get_string('courseicon', 'theme_uonbi');
    $description = get_string('courseicon_desc', 'theme_uonbi');
    $setting = new admin_setting_configtext($name, $title, $description, 'icon-graduation');
    $setting->paramtype = THEME_UONBI_SETTINGS_ICON_REGEX;
    $setting->set_updatedcallback('theme_uonbi_reset_icon_caches');
    $temp->add($setting);

    // Use course/info.php page.
    $name = 'theme_uonbi/courseinfo';
    $title = get_string('courseinfo', 'theme_uonbi');
    $description = get_string('courseinfo_desc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Progress chart.
    $name = 'theme_uonbi/progresschart';
    $title = get_string('progresschart', 'theme_uonbi');
    $description = get_string('progresschart_desc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    // Alert Settings
    $temp = new admin_settingpage('theme_uonbi_alerts', get_string('alert_heading', 'theme_uonbi'));
    $temp->add(new admin_setting_heading('theme_uonbi_alerts', get_string('alert_settingssub', 'theme_uonbi'),
        format_text(get_string('alert_desc', 'theme_uonbi'), FORMAT_MARKDOWN)));
    $information = get_string('alertinfodesc', 'theme_uonbi'); // Standard for each of the descriptors.

    // This is the descriptor for Alert One.
    $name = 'theme_uonbi/alert1info';
    $heading = get_string('alert1', 'theme_uonbi');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Enable Alert.
    $name = 'theme_uonbi/enable1alert';
    $title = get_string('enablealert', 'theme_uonbi');
    $description = get_string('enablealertdesc', 'theme_uonbi');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Staff Only Alert.
    $name = 'theme_uonbi/staffonly1alert';
    $title = get_string('staffonlyalert', 'theme_uonbi');
    $description = get_string('staffonlyalertdesc', 'theme_uonbi');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Type.
    $name = 'theme_uonbi/alert1type';
    $title = get_string('alerttype', 'theme_uonbi');
    $description = get_string('alerttypedesc', 'theme_uonbi');
    $alertinfo = get_string('alert_info', 'theme_uonbi');
    $alertwarning = get_string('alert_warning', 'theme_uonbi');
    $alertgeneral = get_string('alert_general', 'theme_uonbi');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'warning' => $alertwarning, 'success' => $alertgeneral);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Title.
    $name = 'theme_uonbi/alert1title';
    $title = get_string('alerttitle', 'theme_uonbi');
    $description = get_string('alerttitledesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Text.
    $name = 'theme_uonbi/alert1text';
    $title = get_string('alerttext', 'theme_uonbi');
    $description = get_string('alerttextdesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // This is the descriptor for Alert Two.
    $name = 'theme_uonbi/alert2info';
    $heading = get_string('alert2', 'theme_uonbi');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Enable Alert.
    $name = 'theme_uonbi/enable2alert';
    $title = get_string('enablealert', 'theme_uonbi');
    $description = get_string('enablealertdesc', 'theme_uonbi');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Staff Only Alert.
    $name = 'theme_uonbi/staffonly2alert';
    $title = get_string('staffonlyalert', 'theme_uonbi');
    $description = get_string('staffonlyalertdesc', 'theme_uonbi');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Type.
    $name = 'theme_uonbi/alert2type';
    $title = get_string('alerttype', 'theme_uonbi');
    $description = get_string('alerttypedesc', 'theme_uonbi');
    $alertinfo = get_string('alert_info', 'theme_uonbi');
    $alertwarning = get_string('alert_warning', 'theme_uonbi');
    $alertgeneral = get_string('alert_general', 'theme_uonbi');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'warning' => $alertwarning, 'success' => $alertgeneral);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Title.
    $name = 'theme_uonbi/alert2title';
    $title = get_string('alerttitle', 'theme_uonbi');
    $description = get_string('alerttitledesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Text.
    $name = 'theme_uonbi/alert2text';
    $title = get_string('alerttext', 'theme_uonbi');
    $description = get_string('alerttextdesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // This is the descriptor for Alert Three.
    $name = 'theme_uonbi/alert3info';
    $heading = get_string('alert3', 'theme_uonbi');
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Enable Alert.
    $name = 'theme_uonbi/enable3alert';
    $title = get_string('enablealert', 'theme_uonbi');
    $description = get_string('enablealertdesc', 'theme_uonbi');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Staff Only Alert.
    $name = 'theme_uonbi/staffonly3alert';
    $title = get_string('staffonlyalert', 'theme_uonbi');
    $description = get_string('staffonlyalertdesc', 'theme_uonbi');
    $default = false;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Type.
    $name = 'theme_uonbi/alert3type';
    $title = get_string('alerttype', 'theme_uonbi');
    $description = get_string('alerttypedesc', 'theme_uonbi');
    $alertinfo = get_string('alert_info', 'theme_uonbi');
    $alertwarning = get_string('alert_warning', 'theme_uonbi');
    $alertgeneral = get_string('alert_general', 'theme_uonbi');
    $default = 'info';
    $choices = array('info' => $alertinfo, 'warning' => $alertwarning, 'success' => $alertgeneral);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Title.
    $name = 'theme_uonbi/alert3title';
    $title = get_string('alerttitle', 'theme_uonbi');
    $description = get_string('alerttitledesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Alert Text.
    $name = 'theme_uonbi/alert3text';
    $title = get_string('alerttext', 'theme_uonbi');
    $description = get_string('alerttextdesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Must add the temp after definiting all the settings!
    $settings->add($temp);

    // Header Settings
    $temp = new admin_settingpage('theme_uonbi_header', get_string('headerheading', 'theme_uonbi'));


    // Logo position.
    $name = 'theme_uonbi/logopos';
    $title = get_string('logopos', 'theme_uonbi');
    $description = get_string('logopos_desc', 'theme_uonbi');
    $default = 0;
    $options = [];
    $options[0] = get_string('logoposleft', 'theme_uonbi');
    $options[1] = get_string('logoposright', 'theme_uonbi');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $temp->add($setting);

    // Nav hamburger position.
    $name = 'theme_uonbi/hamburgerpos';
    $title = get_string('hamburgerpos', 'theme_uonbi');
    $description = get_string('hamburgerpos_desc', 'theme_uonbi');
    $default = 1;
    $options = [];
    $options[0] = get_string('hamburgerposright', 'theme_uonbi');
    $options[1] = get_string('hamburgerposleft', 'theme_uonbi');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header height.
    $name = 'theme_uonbi/headerheight';
    $title = get_string('headerheight', 'theme_uonbi');
    $description = get_string('headerheight_desc', 'theme_uonbi');
    $default = '64px';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_RAW_TRIMMED, 12);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Hide/show login button in header.
    $name = 'theme_uonbi/loginbtn';
    $title = get_string('loginbtn', 'theme_uonbi');
    $description = get_string('loginbtn_desc', 'theme_uonbi');
    $options = [];
    $options[1] = get_string('show', 'theme_uonbi');
    $options[2] = get_string('hide', 'theme_uonbi');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header background colour.
    $name = 'theme_uonbi/headerbgcolor';
    $title = get_string('headerbgcolor', 'theme_uonbi');
    $description = get_string('headerbgcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header link colour.
    $name = 'theme_uonbi/headerlinkcolor';
    $title = get_string('headerlinkcolor', 'theme_uonbi');
    $description = get_string('headerlinkcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header bottom border.
    $name = 'theme_uonbi/headerborder';
    $title = get_string('headerborder', 'theme_uonbi');
    $description = get_string('headerborder_desc', 'theme_uonbi');
    $default = 0;
    $from0to20px = array();
    for ($i = 0; $i < 21; $i++) {
        $from0to20px[$i . 'px'] = $i . 'px';
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $from0to20px);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Header bottom border colour.
    $name = 'theme_uonbi/headerbordercolor';
    $title = get_string('headerbordercolor', 'theme_uonbi');
    $description = get_string('headerbordercolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Toggle courses display in custommenu.
    $name = 'theme_uonbi/displaymycourses';
    $title = get_string('displaymycourses', 'theme_uonbi');
    $description = get_string('displaymycoursesdesc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, false, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    // Front temp Settings
    $temp = new admin_settingpage('theme_uonbi_frontpage', get_string('frontpageheading', 'theme_uonbi'));

    // Frontpage BG file setting.
    $name = 'theme_uonbi/frontbg';
    $title = get_string('frontbg', 'theme_uonbi');
    $description = get_string('frontbgdesc', 'theme_uonbi');
    $opts = array('accepted_types' => array('.png', '.jpg', '.jpeg', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'frontbg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    // Login temp Settings
    $temp = new admin_settingpage('theme_uonbi_loginpage', get_string('loginpageheading', 'theme_uonbi'));
    $temp->add(new admin_setting_heading('theme_uonbi_loginpage', get_string('loginpage_settingssub', 'theme_uonbi'),
        format_text(get_string('loginpage_desc', 'theme_uonbi'), FORMAT_MARKDOWN)));

    // Login background carousel file settings.

    // Carousel slide effect.
    $name = 'theme_uonbi/slideeffect';
    $title = get_string('slideeffect', 'theme_uonbi');
    $description = get_string('slideeffectdesc', 'theme_uonbi');
    $options = [];
    $options[1] = get_string('slide', 'theme_uonbi');
    $options[2] = get_string('fade', 'theme_uonbi');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_uonbi/slidetime';
    $title = get_string('slidetime', 'theme_uonbi');
    $description = get_string('slidetimedesc', 'theme_uonbi');
    $setting = new admin_setting_configtext($name, $title, $description, 5, PARAM_INT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $name = 'theme_uonbi/slideimages';
    $title = get_string('slideimages', 'theme_uonbi');
    $description = get_string('slideimagesdesc', 'theme_uonbi');
    $opts = array('accepted_types' => array('.png', '.jpg', '.jpeg', '.svg'), 'maxfiles' => -1);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'slideimages', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    // Course temp Settings
    $temp = new admin_settingpage('theme_uonbi_coursepage', get_string('coursepageheading', 'theme_uonbi'));

    // Section-0 heading hide/show option.
    $name = 'theme_uonbi/sectionzero';
    $title = get_string('sectionzero', 'theme_uonbi');
    $description = get_string('sectionzerodesc', 'theme_uonbi');
    $options = [];
    $options[1] = get_string('show', 'theme_uonbi');
    $options[2] = get_string('hide', 'theme_uonbi');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Show hidden course alert in course pages.
    $name = 'theme_uonbi/hiddencoursealert';
    $title = get_string('hiddencoursealert', 'theme_uonbi');
    $description = get_string('hiddencoursealert_desc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Show course contacts on course info pages and course category.
    $name = 'theme_uonbi/coursecontacts';
    $title = get_string('coursecontacts', 'theme_uonbi');
    $description = get_string('coursecontacts_desc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Course custom summary field for course info page.
    $name = 'theme_uonbi/coursecustomfieldsummary';
    $title = get_string('coursecustomfieldsummary', 'theme_uonbi');
    $description = get_string('coursecustomfieldsummarydesc', 'theme_uonbi');
    $coursecustomfields = get_course_custom_fields_shortname();
    $default = 0;
    $options = [];
    $options[0] = get_string('coursecustomfieldsummarydefault', 'theme_uonbi');
    foreach ($coursecustomfields as $key => $value) {
        $options[$value] = $value;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    // block_course_overview card display settings.
    $temp = new admin_settingpage('theme_uonbi_coursecard', get_string('coursecardheading', 'theme_uonbi'));

    // Checkboxes for which buttons to display on when using card view.
    $cardoptions = course::CARD_OPTIONS;

    sort($cardoptions);

    foreach ($cardoptions as $cardoption) {
        $name = "theme_uonbi/{$cardoption}_button";
        $title = get_string("display{$cardoption}", 'theme_uonbi');
        $description = get_string("display{$cardoption}_desc", 'theme_uonbi');
        $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);
    }

    // Change the behaviour of the settings button.
    $name = 'theme_uonbi/alt_behaviour_settings_button';
    $title = get_string('changesettingsbutton', 'theme_uonbi');
    $description = get_string('changesettingsbutton_desc', 'theme_uonbi');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    $settings->add($temp);

    // Footer Settings start
    $temp = new admin_settingpage('theme_uonbi_footer', get_string('footerheading', 'theme_uonbi'));

    // Footer background colour.
    $name = 'theme_uonbi/footerbgcolor';
    $title = get_string('footerbgcolor', 'theme_uonbi');
    $description = get_string('footerbgcolor_desc', 'theme_uonbi');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Footer links list heading 1
    $name = 'theme_uonbi/footerlinkslistheading1';
    $title = get_string('footerlinkslistheading1', 'theme_uonbi');
    $description = get_string('footerlinkslistheading1_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list 1
    $name = 'theme_uonbi/footerlinkslist1';
    $title = get_string('footerlinkslist1', 'theme_uonbi');
    $description = get_string('footerlinkslist1_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list heading 2
    $name = 'theme_uonbi/footerlinkslistheading2';
    $title = get_string('footerlinkslistheading2', 'theme_uonbi');
    $description = get_string('footerlinkslistheading2_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list 2
    $name = 'theme_uonbi/footerlinkslist2';
    $title = get_string('footerlinkslist2', 'theme_uonbi');
    $description = get_string('footerlinkslist2_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list heading 3
    $name = 'theme_uonbi/footerlinkslistheading3';
    $title = get_string('footerlinkslistheading3', 'theme_uonbi');
    $description = get_string('footerlinkslistheading3_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list 3
    $name = 'theme_uonbi/footerlinkslist3';
    $title = get_string('footerlinkslist3', 'theme_uonbi');
    $description = get_string('footerlinkslist3_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list heading 4
    $name = 'theme_uonbi/footerlinkslistheading4';
    $title = get_string('footerlinkslistheading4', 'theme_uonbi');
    $description = get_string('footerlinkslistheading4_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Footer links list 4
    $name = 'theme_uonbi/footerlinkslist4';
    $title = get_string('footerlinkslist4', 'theme_uonbi');
    $description = get_string('footerlinkslist4_desc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // Footer Content
    $name = 'theme_uonbi/footnote';
    $title = get_string('footnote', 'theme_uonbi');
    $description = get_string('footnotedesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $temp->add($setting);

    // Copyright.
    $name = 'theme_uonbi/copyright_footer';
    $title = get_string('copyright_footer', 'theme_uonbi');
    $description = '';
    $default = get_string('copyright_default', 'theme_uonbi');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Address , Email , Phone No
    $name = 'theme_uonbi/address';
    $title = get_string('address', 'theme_uonbi');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/emailid';
    $title = get_string('emailid', 'theme_uonbi');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/phoneno';
    $title = get_string('phoneno', 'theme_uonbi');
    $description = '';
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Facebook, LinkedIn, Twitter, Google+ Settings
    $name = 'theme_uonbi/fburl';
    $title = get_string('fburl', 'theme_uonbi');
    $description = get_string('fburldesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/linkedinurl';
    $title = get_string('linkedinurl', 'theme_uonbi');
    $description = get_string('linkedinurldesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/twurl';
    $title = get_string('twurl', 'theme_uonbi');
    $description = get_string('twurldesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/instaurl';
    $title = get_string('instaurl', 'theme_uonbi');
    $description = get_string('instaurldesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/youtubeurl';
    $title = get_string('youtubeurl', 'theme_uonbi');
    $description = get_string('youtubeurldesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_uonbi/pinurl';
    $title = get_string('pinurl', 'theme_uonbi');
    $description = get_string('pinurldesc', 'theme_uonbi');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $settings->add($temp);
    //  Footer Settings end
}