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
 * renderers/core_course_renderer.php
 *
 * @package   theme_uonbi
 * @copyright 2018 Catalyst IT Europe, www.catalyst-eu.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_uonbi\output\core;

use moodle_url;
use lang_string;
use coursecat_helper;
use core_course_category;
use stdClass;
use cm_info;
use core_text;
use core_course_list_element;
use context_course;
use context_system;
use pix_url;
use html_writer;
use heading;
use pix_icon;
use image_url;
use single_select;

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot . '/course/renderer.php');
require_once($CFG->libdir . '/filelib.php');
global $PAGE;

/**
 * UONBI theme course renderer class
 */

class course_renderer extends \core_course_renderer {

    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
            global $CFG;
            if ($totalcount === null) {
                $totalcount = count($courses);
            }
            if (!$totalcount) {
                // Courses count is cached during courses retrieval.
                return '';
            }
            if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
                if ($totalcount <= $CFG->courseswithsummarieslimit) {
                    $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
                }
                else {
                    $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
                }
            }
            $paginationurl = $chelper->get_courses_display_option('paginationurl');
            $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
            if ($totalcount > count($courses)) {
                if ($paginationurl) {
                    $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                    $page = $chelper->get_courses_display_option('offset') / $perpage;
                    $pagingbar = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, array(
                        'perpage' => $perpage
                    )));
                    if ($paginationallowall) {
                        $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                            'perpage' => 'all'
                        )) , get_string('showall', '', $totalcount)) , array(
                            'class' => 'paging paging-showall'
                        ));
                    }
                }
                else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                    $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                    $morelink = html_writer::tag('div', html_writer::tag('a', html_writer::start_tag('i', array(
                        'class' => 'fa-graduation-cap' . ' fa fa-fw'
                    )) . html_writer::end_tag('i') . $viewmoretext, array(
                        'href' => $viewmoreurl,
                        'class' => 'btn btn-primary coursesmorelink'
                    )) , array(
                        'class' => 'paging paging-morelink'
                    ));
                }
            }
            else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
                $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                    'perpage' => $CFG->coursesperpage
                )) , get_string('showperpage', '', $CFG->coursesperpage)) , array(
                    'class' => 'paging paging-showperpage'
                ));
            }
            $attributes = $chelper->get_and_erase_attributes('courses');
            $content = html_writer::start_tag('div', $attributes);
            if (!empty($pagingbar)) {
                $content .= $pagingbar;
            }
            $categoryid = optional_param('categoryid', 0, PARAM_INT);
            $coursecount = 0;
            $content .= $this->view_available_courses($categoryid, $courses, $totalcount);
            if (!empty($pagingbar)) {
                $content .= $pagingbar;
            }
            if (!empty($morelink)) {
                $content .= $morelink;
            }
            $content .= html_writer::end_tag('div');
            $content .= '<div class="clearfix"></div>';
            return $content;
        }

        public function view_available_courses($id = 0, $courses = null, $totalcount = null) {
            /* available courses */
            global $CFG, $OUTPUT, $PAGE;

            $rcourseids = array_keys($courses);
            $acourseids = array_chunk($rcourseids, 4);


            if ($id != 0) {
                //$newcourse = get_string('availablecourses');
                $newcourse = null;
            }
            else {
                $newcourse = null;
            }
            $header = '
                <div id="category-course-list">
                    <div class="courses category-course-list-all">
                        <h5>' . $newcourse . '</h5>
                    ';
            $content = '';
            $footer = '</div>
                </div>';
            if (count($rcourseids) > 0) {
                foreach ($acourseids as $courseids) {
                    $content .= '<div class="card-grid mx-0 row row-cols-1 row-cols-sm-2 row-cols-lg-4">';
                    $rowcontent = '';
                    foreach ($courseids as $courseid) {
                        $course = get_course($courseid);
                        $courseicons = $this->uonbi_course_detail_icons($course);

                        $trimtitlevalue = '200';
                        $trimsummaryvalue = '100';
                        $summary = theme_uonbi_strip_html_tags($course->summary);
                        $summary = format_text(theme_uonbi_course_trim_char($summary, $trimsummaryvalue));
                        $trimtitle = format_string(theme_uonbi_course_trim_char($course->fullname, $trimtitlevalue));
                        $noimgurl = $OUTPUT->image_url('default', 'theme');
                        if (!empty($PAGE->theme->settings->courseinfo)) {
                            $courseurl = new moodle_url('/course/info.php', array('id' => $courseid));
                        } else{
                            $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
                        }

                        $systemcontext = $PAGE->bodyid;

                        // Course completion Progress Radial
                        if (\core_completion\progress::get_course_progress_percentage($course)) {
                            $comppc = \core_completion\progress::get_course_progress_percentage($course);
                            $comppercent = number_format($comppc, 0);
                            $hasprogress = true;
                        } else {
                            $comppercent = 0;
                            $hasprogress = false;
                        }
                        $progresschartvisible = $PAGE->theme->settings->progresschart;
                        $progresschartcontext = ['progresschartvisible' => $progresschartvisible, 'hasprogress' => $hasprogress, 'progress' => $comppercent];
                        $progresschart = $this->render_from_template('block_myoverview/progress-chart', $progresschartcontext);

                        // Course completion Progress bar
                        if ($course->enablecompletion == 1) {
                            $completiontext = get_string('coursecompletion', 'completion');
                            $compbar = "<div class='progress'>";
                            $compbar .= "<div class='progress-bar progress-bar-info barfill' role='progressbar' aria-valuenow='{$comppercent}' ";
                            $compbar .= " aria-valuemin='0' aria-valuemax='100' style='width: {$comppercent}%;'>";
                            $compbar .= "{$comppercent}%";
                            $compbar .= "</div>";
                            $compbar .= "</div>";
                            $progressbar = $compbar;
                        } else {
                            $progressbar = '';
                            $completiontext = '';
                        }

                        if ($course instanceof stdClass) {
                            $course = new core_course_list_element($course);
                        }

                        // print enrolmenticons
                        $pixcontent = '';
                        if ($icons = enrol_get_course_info_icons($course)) {
                            $pixcontent .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
                            foreach ($icons as $pix_icon) {
                                $pixcontent .= $this->render($pix_icon);
                            }
                            $pixcontent .= html_writer::end_tag('div'); // .enrolmenticons
                        }

                        // display course category if necessary (for example in search results)
                        if ($cat = core_course_category::get($course->category, IGNORE_MISSING)) {
                            $catcontent = html_writer::start_tag('div', array('class' => 'coursecat'));
                            $catcontent .= get_string('category').': '.
                                    html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                            $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                            $catcontent .= $pixcontent;
                            $catcontent .= html_writer::end_tag('div'); // .coursecat
                        }

                        // Load from config if using an img from course summary file otherwise serve a default one.
                        $imgurl = '';
                        $context = context_course::instance($course->id);
                        foreach ($course->get_course_overviewfiles() as $file) {
                            $isimage = $file->is_valid_image();
                            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename() , !$isimage);
                            if (!$isimage) {
                                $imgurl = $noimgurl;
                            }
                        }

                        if (empty($imgurl)) {
                            $imgurl = $OUTPUT->get_generated_image_for_id($course->id);
                        }

                        $rowcontent .= html_writer::start_tag('div', array(
                            'class' => $course->visible ? 'card courses-card coursevisible' : 'card courses-card coursedimmed'
                        ));

                        $tooltiptext = 'data-tooltip="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';

                        $theme = \theme_config::load('uonbi');
                        $coursecontacts = (!empty($theme->settings->coursecontacts)) ? $this->course_contacts($course) : '';

                        $rowcontent .= '
                        <div class="card-inner">
                        <a ' . $tooltiptext . ' href="' . $courseurl . '" class="img-container" >
                        <div class="card-img-top myoverviewimg" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                            <div class="media">
                                 <div class="mr-2">
                                    ' . $progresschart . '
                                </div>
                            </div>
                        </div>
                        </a>
                        <div class="card-body">
                            <h3 class="card-title">
                                <a href="' . $courseurl . '">
                                    ' . $trimtitle . '
                                </a>
                            </h3>
                            <div class="card-contacts">
                            ' . $coursecontacts . '
                            </div>
                        </div>
                        <div class="card-footer">
                            ' . $courseicons . '
                        </div>
                        </div>
                        </div>';
                    }
                    $content .= $rowcontent;
                    $content .= '</div>';
                }
            }
            $coursehtml = $header . $content . $footer;
            return $coursehtml;
        }

    /**
     * Renders course summary box.
     *
     * @param stdClass|core_course_list_element $course
     * @param array $buttonconfig
     * @return string
     */
    public function course_summary_box($course, $buttonconfig = null, $showexpanded = false) {
        global $CFG, $DB, $PAGE;

        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }

        if ($buttonconfig === null) {
            $buttonconfig = $this->course_info_button($course);
        }

        $output = '';

        $output .= html_writer::start_tag('div', array('class' => 'course-summary-wrapper row'));
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-image-wrapper col-sm-5'));
        // display course overview files
        $contentimages = $contentfiles = '';
        if (count($course->get_course_overviewfiles())) {
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if ($isimage) {
                    $contentimages .= html_writer::tag('div',
                        html_writer::empty_tag('img', array('src' => $url, 'class' => 'card-img-top')),
                        array('class' => 'courseimage'));
                }
            }
        } else { // display default course image if no course overview image is set
                $url = $this->output->image_url('default', 'theme_uonbi');
                $contentimages .= html_writer::empty_tag('img', array('src' => $url, 'class' => 'card-img-top'));
        }
        $output .= $contentimages. $contentfiles;
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'course-summary-summary col-sm-7'));
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-summary-text row '));
        if ($buttonconfig['label'] == get_string('viewcourse', 'theme_uonbi')) {
            $output .= html_writer::link($buttonconfig['target'], $course->fullname, array('class' => 'coursename'));
        } else {
            $output .= html_writer::tag('h1', $course->fullname, array('class' => 'coursename'));
        }

        if ($course->has_summary()) {
            $output .= html_writer::tag('div', html_writer::tag('p', ''.$course->summary),
                  array('class' => 'course-summary-shortintro col-md-7'));
        } else {
            $output .= html_writer::tag('p', get_string('nosummary', 'theme_uonbi'), array('class' => 'course-summary-shortintro col-md-7 nosummary'));
        }

        $output .= html_writer::start_tag('div', array('class' => 'course-summary-summary-button col-md-5'));
        $output .= $this->course_info_button($course);

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        return $output;
    }

        /**
     * Renders course summary box.
     *
     * @param stdClass|core_course_list_element $course
     * @param array $buttonconfig
     * @return string
     */
    public function uonbi_course_summary_box($course) {
        global $CFG;

        if ($course instanceof \stdClass) {
            $course = new core_course_list_element($course);
        }

        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-wrapper row'));
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-image-wrapper col-sm-4 col-lg-3'));
        $output .= $this->uonbi_course_image($course, 'course-info-image');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-summary col-sm-8 col-lg-8'));
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-title '));
        $output .= html_writer::tag('h1', $course->fullname, array('class' => 'coursename'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'course-summary-details-wrapper'));
        $output .= html_writer::start_tag('div', array('class' => 'course-summary-details row'));

        $output .= html_writer::start_tag('div', array('class' => 'course-summary-icons col-sm-4 col-lg-3'));
        $output .= $this->uonbi_course_detail_icons($course);
        $output .= html_writer::end_tag('div');

        // Display course summary.
        $theme = \theme_config::load('uonbi');
        $themecustomfield = strval($theme->settings->coursecustomfieldsummary);
        if (!empty($themecustomfield)) {
            $customfields = get_course_custom_fields_value($course);
            if (!empty($customfields)) {
                $truncsum = $customfields[$themecustomfield];
            }
        }
        if ($course->has_summary() && empty($truncsum)) {
            $chelper = new coursecat_helper();
            $summs = $chelper->get_course_formatted_summary($course, array('overflowdiv' => false, 'noclean' => true,
                    'para' => false));
            $summs = strip_tags($summs);
            $truncsum = mb_strimwidth($summs, 0, 200, "...", 'utf-8');
        }
        if (!empty($truncsum)) {
            $output .= html_writer::tag('div', $truncsum, array('class' => 'course-summary-short-summary col-sm-8 col-lg-6'));
        } else {
            $classes = 'course-summary-short-summary col-sm-8 col-lg-6 nosummary';
            $output .= html_writer::tag('p', get_string('nosummary', 'theme_uonbi'), array('class' => $classes));
        }

        $output .= html_writer::start_tag('div', array('class' => 'course-summary-summary-button col-sm-12 col-lg-3'));
        $output .= $this->uonbi_course_view_button($course);
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        if (!empty($theme->settings->coursecontacts)) {
            $output .= html_writer::start_tag('div', array('class' => 'teachers-list'));
            $output .= $this->course_contacts($course);
            $output .= html_writer::end_tag('div');
        }
        
        return $output;
    }

    public function uonbi_course_image($course, $classes) {
        global $CFG, $OUTPUT;

        if ($course instanceof \stdClass) {
            $course = new core_course_list_element($course);
        }

        // Display default course image if no course overview image is set.
        $url = $OUTPUT->get_generated_image_for_id($course->id);
        if (count($course->get_course_overviewfiles())) {
            // Use the first image saved in the course settings.
            foreach ($course->get_course_overviewfiles() as $file) {
                if ($file->is_valid_image()) {
                    $path = '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename();
                    $imgurl = moodle_url::make_file_url('/pluginfile.php', $path);
                    $url = $imgurl->out();
                    break;
                }
            }
        }

        $output = html_writer::start_tag('div', array('class' => 'courseimage'));
        $output .= html_writer::empty_tag('img', array('src' => $url, 'class' => $classes));
        $output .= html_writer::end_tag('div');

        return $output;
    }

    public function uonbi_course_view_button($course) {
        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'enrolnow'));
        $url = new moodle_url('/course/view.php', array('id' => $course->id));
        $output .= html_writer::link($url, get_string('entercourse', 'theme_uonbi'), array('class' => 'btn btn-secondary btn-lg'));
        $output .= html_writer::end_tag('div');

        return $output;
    }

    public function uonbi_course_detail_icons($course) {
        global $CFG;

        $coursecontext = context_course::instance($course->id);

        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'summary-icons'));

        $pluginconfig = get_config('theme_uonbi');
        $urlparam = ['id' => $course->id];

        if ($pluginconfig->participants_button) {
            if (isloggedin()) {
                $output .= html_writer::link(
                    new moodle_url('/user/index.php', $urlparam),
                    $this->output->pix_icon('i/cohort', get_string('participants')),
                    ['class' => 'btn btn-secondary btn-sm link-participants']);
            }
        }

        if ($pluginconfig->grades_button) {
            if (has_capability('moodle/course:update', $coursecontext)) {
                $urlgrades = new moodle_url('/grade/report/index.php', $urlparam);
            } else {
                $urlgrades = new moodle_url('/grade/report/user/index.php', $urlparam);
            }
            if (isloggedin()) {
                $output .= html_writer::link(
                    $urlgrades,
                    $this->output->pix_icon('i/grades', get_string('grades')),
                    ['class' => 'btn btn-secondary btn-sm link-grades']);
            }
        }

        if ($pluginconfig->badges_button && $CFG->enablebadges) {
            if (isloggedin()) {
                $output .= html_writer::link(
                    new moodle_url('/badges/index.php?type=2', $urlparam),
                    $this->output->pix_icon('i/badge', get_string('badges')),
                    ['class' => 'btn btn-secondary btn-sm link-badges']);
            }
        }

        if ($pluginconfig->forums_button) {
            if (isloggedin()) {
                $output .= html_writer::link(
                    new moodle_url('/mod/forum/index.php', $urlparam),
                    $this->output->pix_icon('t/messages', get_string('forum', 'forum')),
                    ['class' => 'btn btn-secondary btn-sm link-forums']);
            }
        }

        if ($pluginconfig->settings_button) {
            if (has_capability('moodle/course:update', $coursecontext)) {

                if ($pluginconfig->alt_behaviour_settings_button) {
                    $params = [
                        'id' => $course->id,
                        'sesskey' => sesskey(),
                        'edit' => 'on',
                    ];
                    $urlsettings = new moodle_url('/course/view.php', $params);
                    $iconhtml = $this->output->pix_icon('t/editinline', get_string('editon', 'theme_uonbi'));
                    $classhtml = ['class' => 'btn btn-secondary btn-sm link-settings-alt'];
                } else {
                    $urlsettings = new moodle_url('/course/edit.php', $urlparam);
                    $iconhtml = $this->output->pix_icon('a/setting', get_string('settings'));
                    $classhtml = ['class' => 'btn btn-secondary btn-sm link-settings'];
                }
                $output .= html_writer::link(
                    $urlsettings,
                    $iconhtml,
                    $classhtml);

            } else {
                $output .= html_writer::link(
                    new moodle_url('/course/view.php', $urlparam),
                    $this->output->pix_icon('t/right', get_string('entercourse')),
                    ['class' => 'btn btn-secondary btn-sm link-course']);
            }
        }

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Returns HTML to display course contacts.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_contacts(core_course_list_element $course) {
        $content = '';
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', ['class' => 'teachers']);
            
            foreach ($course->get_course_contacts() as $coursecontact) {
                $rolenames = array_map(function ($role) {
                    return $role->displayname;
                }, $coursecontact['roles']);
                $name = implode(", ", $rolenames).': '.
                    html_writer::link(new moodle_url('/user/view.php',
                        ['id' => $coursecontact['user']->id, 'course' => SITEID]),
                        $coursecontact['username']);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul');
        }
        return $content;
    }

    public function uonbi_course_summary_box_long_summary($course, $options = array()) {
        $summarytext = file_rewrite_pluginfile_urls(
              $course->summary, 'pluginfile.php', \context_course::instance($course->id)->id, 'course', 'summary', null
        );
        $options['trusted'] = true;
        $options['noclean'] = true;
        $options['context'] = \context_course::instance($course->id);
        $summarytext = format_text($summarytext, $course->summaryformat, $options);
        if (!empty($this->searchcriteria['search'])) {
            $summarytext = highlight($this->searchcriteria['search'], $summarytext);
        }
        $output = '';
        $output .= html_writer::tag('div', ''.$summarytext, array('class' => 'course-summary-long-summary'));

        return $output;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG, $PAGE;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        if (!empty($PAGE->theme->settings->courseinfo)) {
            $infourl = new moodle_url('/course/info.php', array('id' => $course->id));
        } else{
            $infourl = new moodle_url('/course/view.php', array('id' => $course->id));
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'info'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $image = $this->output->pix_icon('i/info', $this->strings->summary);
                $content .= html_writer::link($infourl, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div'); // .moreinfo

        // print enrolmenticons
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pix_icon) {
                $content .= $this->render($pix_icon);
            }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }

        $content .= html_writer::end_tag('div'); // .info

        $content .= html_writer::start_tag('div', array('class' => 'content'));

        if ($course->has_summary()) {
            $content .= $this->uonbi_course_summary_box_long_summary($course, $options = array());
        }

        $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }

/**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        $output = '';
        $modname = $mod->modname;
        // We return empty string (because course module will not be displayed at all)
        // if:
        // 1) The activity is not visible to users
        // and
        // 2) The 'availableinfo' is empty, i.e. the activity was
        //     hidden in a way that leaves no info, such as using the
        //     eye icon.
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }

        $output .= html_writer::start_tag('div', array('class' => 'activity-inner'));

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent
        $output .= html_writer::start_tag('div', array('class' => 'activity-content'));

        // Display the link to the module (or do nothing if module has no url)
        $cmname = $this->course_section_cm_name($mod, $displayoptions);

        if (!empty($cmname)) {
            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityname'));
            $output .= $modname;
            $output .= html_writer::end_tag('div');
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;


            // Module can put text after the link (e.g. forum unread)
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance
        }

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case cons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->url;
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $output .= html_writer::span($modicons, 'actions');
        }

        // Show availability info (if module is not available).
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        // If there is content AND a link, then display the content here
        // (AFTER any icons). Otherwise it was displayed before
        if (!empty($url)) {
            $output .= $contentpart;
        }

        $output .= html_writer::end_tag('div'); // $indentclasses

        // End of indentation div.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name_title(cm_info $mod, $displayoptions = array()) {
        $output = '';
        $url = $mod->url;
        if (!$mod->is_visible_on_course_page() || !$url) {
            // Nothing to be displayed to the user.
            return $output;
        }

        //Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        // Avoid unnecessary duplication: if e.g. a forum name already
        // includes the word forum (or Forum, etc) then it is unhelpful
        // to include that in the accessible description that is added.
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
            $altname = '';
        }
        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        list($linkclasses, $textclasses) = $this->course_section_cm_classes($mod);

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        // Display link itself.
        $activitylink = html_writer::empty_tag('img', array('src' => $mod->get_icon_url(),
                'class' => 'iconlarge activityicon', 'alt' => ' ', 'role' => 'presentation')) .
                html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));
        if ($mod->uservisible) {
            $output .= html_writer::link($url, $activitylink, array('class' => $linkclasses, 'onclick' => $onclick));
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->is_visible_on_course_page()).
            $output .= html_writer::tag('div', $activitylink, array('class' => $textclasses));
        }
        return $output;
    }
}