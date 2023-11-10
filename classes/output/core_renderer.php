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
 * Overriden theme boost core renderer.
 *
 * @package    theme_uonbi
 * @copyright  2017 Willian Mano - http://catalyst-eu.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_uonbi\output;

use html_writer;
use stdClass;
use pix_icon;
use moodle_url;
use theme_config;
use context_course;
use context_system;
use core_course_list_element;
use custom_menu;

require_once ($CFG->dirroot . "/course/renderer.php");

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_uonbi
 * @copyright  2017 Willian Mano - http://catalyst-eu.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */

    public function course_full_header() {
        global $COURSE, $OUTPUT, $PAGE;

        $course = $PAGE->course;
        $coursecontext = context_course::instance($course->id);

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        if (has_capability('moodle/course:update', $coursecontext)) {
            $header->editbutton = $this->editbutton();
        }
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        return $this->render_from_template('theme_uonbi/header', $header);
    }

    /**
     * Return University of Nairobi editing button.
     * @return string
     */
    public function editbutton() {
        global $SITE, $PAGE, $USER, $CFG, $COURSE;
        if (!$PAGE->user_allowed_editing() || $COURSE->id <= 1) {
            return '';
        }
        if  ($PAGE->pagelayout == 'course') {
            $url = new moodle_url($PAGE->url);
            $url->param('sesskey', sesskey());
            if ($PAGE->user_is_editing()) {
                $url->param('edit', 'off');
                $btn = 'btn btn-secondary active';
                $title = get_string('editoff', 'theme_uonbi');
                $icon = 'fa-pencil';
            }
            else {
                $url->param('edit', 'on');
                $btn = 'btn btn-secondary';
                $title = get_string('editon', 'theme_uonbi');
                $icon = 'fa-pencil';
            }
            return html_writer::tag('a', html_writer::start_tag('i', array(
                'class' => $icon . ' fa fa-fw'
            )) . html_writer::end_tag('i'), array(
                'href' => $url,
                'class' => 'btn edit-btn ' . $btn,
                'data-tooltip' => "tooltip",
                'data-placement'=> "bottom",
                'title' => $title,
            ));
            return $output;
        }
    }

    /**
    * Outputs the course info
    * @return string.
    */
    public function uonbi_course_info() {
        global $PAGE, $COURSE;

        if ($PAGE->pagetype == 'course-info') {
            $courserenderer = $PAGE->get_renderer('core', 'course');
            $uppercontent = $courserenderer->uonbi_course_summary_box (
                $COURSE,
                false,
                true);
        } else {
            $uppercontent = '';
        }
        return $uppercontent;
    }

    // /**
    //  * Renders the login form.
    //  *
    //  * @param \core_auth\output\login $form The renderable.
    //  * @return string
    //  */
    // public function render_login(\core_auth\output\login $form) {
    //     global $SITE;

    //     $context = $form->export_for_template($this);

    //     // Override because rendering is not supported in template yet.
    //     $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
    //     $context->errorformatted = $this->error_text($context->error);

    //     $context->sitename = format_string($SITE->fullname, true, array('context' => \context_course::instance(SITEID)));

    //     return $this->render_from_template('core/login', $context);
    // }

    /**
     * Override core arrows for activity navigation only.
     *
     * @return string
     */
    public function rarrow() {
        if ($this->page->pagelayout == 'incourse') {
            return $this->render_from_template('theme_uonbi/rarrow', new \stdClass());
        }
        return $this->page->theme->rarrow;
    }

    /**
     * Override core arrows for activity navigation only.
     *
     * @return string
     */
    public function larrow() {
        if ($this->page->pagelayout == 'incourse') {
            return $this->render_from_template('theme_uonbi/larrow', new \stdClass());
        }
        return $this->page->theme->larrow;
    }

    /**
     * Outputs the favicon urlbase.
     *
     * @return string an url
     */
    public function favicon() {
        $theme = theme_config::load('uonbi');

        $favicon = $theme->setting_file_url('favicon', 'favicon');

        if (!empty(($favicon))) {
            return $favicon;
        }

        return parent::favicon();
    }

    /**
    * Outputs the pix url base
    *
    * @return string an URL.
    */
    public function get_pix_image_url_base() {
        global $CFG;

        return $CFG->wwwroot . "/theme/uonbi/pix";
    }

    /**
     * Context for user alerts mustache template.
     * @copyright 2017 theme_uonbi Richard Oelmann https://moodle.org/user/profile.php?id=480148
     * @package    theme_uonbi
     *
     * @return renderer context for displaying user alerts.
     */
    public function useralert() {
        // Load specific theme config so useralerts always use uonbi.
        $theme = theme_config::load('uonbi');

        $alertinfo = '<span class="fa fa-2x fa-info-circle"></span>';
        $alertwarning = '<span class="fa fa-2x fa-exclamation-circle"></span>';
        $alertsuccess = '<span class="fa fa-2x fa-bullhorn"></span>';
        $context = context_system::instance();

        $enable1alert = (empty($theme->settings->enable1alert)) ? false : $theme->settings->enable1alert;
        $staffonly1alert = (empty($theme->settings->staffonly1alert)) ? false : $theme->settings->staffonly1alert;
        if ($staffonly1alert && !has_capability('moodle/course:viewhiddencourses', $context)) {
            $enable1alert = false;
        }
        $alert1type = (empty($theme->settings->alert1type)) ? false : $theme->settings->alert1type;
        $alert1icon = 'alert' . $alert1type;
        $alert1heading = (empty($theme->settings->alert1title)) ? false :
            $theme->settings->alert1title;
        $alert1text = (empty($theme->settings->alert1text)) ? false :
            theme_uonbi_get_setting('alert1text', 'format_html');
        $alert1content = '<div class="alertmessage">' . $$alert1icon . '<div class="alerttext"><span class="title"> '
            . $alert1heading . '</span>  ' . $alert1text .'</div></div>';

        $enable2alert = (empty($theme->settings->enable2alert)) ? false :
            $theme->settings->enable2alert;
        $staffonly2alert = (empty($theme->settings->staffonly2alert)) ? false :
            $theme->settings->staffonly2alert;
        if ($staffonly2alert && !has_capability('moodle/course:viewhiddencourses', $context)) {
            $enable2alert = false;
        }
        $alert2type = (empty($theme->settings->alert2type)) ? false :
            $theme->settings->alert2type;
        $alert2icon = 'alert' . $alert2type;
        $alert2heading = (empty($theme->settings->alert2title)) ? false :
            $theme->settings->alert2title;
        $alert2text = (empty($theme->settings->alert2text)) ? false :
            theme_uonbi_get_setting('alert2text', 'format_html');
        $alert2content = '<div class="alertmessage">' . $$alert2icon . '<div class="alerttext"><span class="title"> '
            . $alert2heading . '</span>  ' . $alert2text .'</div></div>';

        $enable3alert = (empty($theme->settings->enable3alert)) ? false :
            $theme->settings->enable3alert;
        $staffonly3alert = (empty($theme->settings->staffonly3alert)) ? false :
            $theme->settings->staffonly3alert;
        if ($staffonly3alert && !has_capability('moodle/course:viewhiddencourses', $context)) {
            $enable3alert = false;
        }
        $alert3type = (empty($theme->settings->alert3type)) ? false :
            $theme->settings->alert3type;
        $alert3icon = 'alert' . $alert3type;
        $alert3heading = (empty($theme->settings->alert3title)) ? false :
            $theme->settings->alert3title;
        $alert3text = (empty($theme->settings->alert3text)) ? false :
            theme_uonbi_get_setting('alert3text', 'format_html');
        $alert3content = '<div class="alertmessage">' . $$alert3icon . '<div class="alerttext"><span class="title"> '
            . $alert3heading . '</span>  ' . $alert3text .'</div></div>';

        $useralertcontext = [
            'hasuseralert' => true,
            'useralert' => array(
                array(
                    'enablealert' => $enable1alert,
                    'alerttype' => $alert1type,
                    'alertcontent' => $alert1content,
                ),
                array(
                    'enablealert' => $enable2alert,
                    'alerttype' => $alert2type,
                    'alertcontent' => $alert2content,
                ),
                array(
                    'enablealert' => $enable3alert,
                    'alerttype' => $alert3type,
                    'alertcontent' => $alert3content,
                ),
            )
        ];

        return $this->render_from_template('theme_uonbi/useralert', $useralertcontext);
    }

    /**
     * Adds custom my courses drop list to page header if enabled.
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function uonbi_mycourses() {
        $menu = new custom_menu();
        $hasdisplaymycourses = (empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
        if (isloggedin() && !isguestuser() && $hasdisplaymycourses) {
            $branchtitle = get_string('mycourses');

            $url = new moodle_url('/my/index.php');
            $branch = $menu->add($branchtitle, $url, $branchtitle, 10000);
            $label = get_string('mymoodle', 'my');
            $dashurl = new moodle_url("/my");
            $branch->add($label, $dashurl, $label);

            if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
                foreach ($courses as $course) {
                    if ($course->visible) {
                        $label = format_string($course->fullname);
                        $branch->add($label, new moodle_url('/course/view.php', ['id' => $course->id]), $label);
                    }
                }
            } else {
                return '';
            }
        }
        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }
        return $content;
    }
}
