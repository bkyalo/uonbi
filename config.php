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
 * config.php
 *
 * @package    theme_uonbi
 * @copyright  2022 onwards Catalyst IT Europe <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$THEME->name = 'uonbi';
$THEME->doctype = 'html5';
$THEME->parents = array('boost');
$THEME->sheets = [];
$THEME->javascripts_footer = array('theme');
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = array();
$THEME->enable_dock = false;
$THEME->editor_scss = ['editor'];
$THEME->usefallback = true;
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

$THEME->scss = function($theme) {
    $parentconfig = theme_config::load('boost');
    return theme_uonbi_get_main_scss_content($parentconfig);
};

$THEME->extrascsscallback = 'theme_uonbi_get_extra_scss';

$THEME->prescsscallback = 'theme_uonbi_get_pre_scss';
$THEME->precompiledcsscallback = 'theme_boost_get_precompiled_css';
$THEME->iconsystem = '\theme_uonbi\util\icon_system';

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_uonbi_process_css';
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
// By default, all boost theme do not need their titles displayed.
$THEME->activityheaderconfig = [
    'notitle' => true
];

$THEME->layouts = [
    'base' => array(
        'file' => 'drawers.php',
        'regions' => array(),
    ),
    'standard' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    'course' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    'coursecategory' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    // The site home page.
    'frontpage' => array(
        'file' => 'frontpage.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true),
    ),
    // Server administration scripts.
    'admin' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    // My dashboard page.
    'mydashboard' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true, 'langmenu' => true, 'nocontextheader' => true),
    ),
    'mycourses' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    'mypublic' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    'login' => array(
        'file' => 'login.php',
        'regions' => array(),
        'options' => array('langmenu' => true),
    ),
    // The pagelayout used for reports.
    'report' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'page-top', 'page-btm'),
        'defaultregion' => 'side-pre',
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header and bare activity header.
    'popup' => array(
        'file' => 'columns1.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nonavbar' => true,
            'activityheader' => [
                'notitle' => true,
                'nocompletion' => true,
                'nodescription' => true
            ]
        ),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'file' => 'columns1.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nocoursefooter' => true,
            'activityheader' => [
                'nocompletion' => true
            ]
        ),
    ),
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => array(
        'file' => 'embedded.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    'maintenance' => array(
        'file' => 'maintenance.php',
        'regions' => array(),
    ),
    // Should display the content and basic headers only.
    'print' => array(
        'file' => 'columns1.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nonavbar' => false, 'noactivityheader' => true),
    ),
    // The pagelayout used when a redirection is occuring.
    'redirect' => array(
        'file' => 'embedded.php',
        'regions' => array(),
    ),
    // The pagelayout used for safebrowser and securewindow.
    'secure' => array(
        'file' => 'secure.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('nofooter' => true),
    )
    
];
