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
 * blocks/myoverview/renderer.php
 *
 * @package    theme_uonbi
 * @copyright  2022 onwards Catalyst IT Europe (http://www.catalyst-eu.net)
 * @author     Catalyst IT Europe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_uonbi\output;
defined('MOODLE_INTERNAL') || die();

use core_course_category;
use block_myoverview\output\main as main;

class block_myoverview_renderer extends \block_myoverview\output\renderer {

    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_main(main $main) {
        global $DB, $PAGE;
        $data = $main->export_for_template($this);

        // Identify the course IDs.
        if (!empty($data['coursesview'])) {
            $coursethemes = enrol_get_my_courses('id,theme,category');
            $catthemes    = $DB->get_records_select('course_categories', "theme != ''", []);
            foreach (['inprogress', 'future', 'past'] as $timeline) {
                if (!empty($data['coursesview'][$timeline])) {
                    foreach ($data['coursesview'][$timeline]['pages'] as $pageid => $page) {
                        foreach ($page['courses'] as $index => $course) {
                            if (!empty($coursethemes[$course->id])) {
                                $category = $coursethemes[$course->id]->category;
                                $theme    = $coursethemes[$course->id]->theme;
                            }

                            $courserenderer = $PAGE->get_renderer('core', 'course');
                            $courseicons = $courserenderer->uonbi_course_detail_icons($course);
                            $data['coursesview'][$timeline]['pages'][$pageid]['courses'][$index]->courseicons = $courseicons;

                            $viewurl = $data['coursesview'][$timeline]['pages'][$pageid]['courses'][$index]->viewurl;
                            $infourl = str_replace('view.php', 'info.php', $viewurl);
                            $data['coursesview'][$timeline]['pages'][$pageid]['courses'][$index]->infourl = $infourl;

                            if (!empty($theme)) {
                                $data['coursesview'][$timeline]['pages'][$pageid]['courses'][$index]->themename = $theme;
                            } else if (!empty($category) && !empty($catthemes[$category])) {
                                $cattheme = $catthemes[$category]->theme;
                                $data['coursesview'][$timeline]['pages'][$pageid]['courses'][$index]->themename = $cattheme;
                            } else {
                                if (!empty($category)) {
                                    $coursecat = core_course_category::get($category, IGNORE_MISSING);
                                    if (!empty($coursecat)) {
                                        $parentids = $coursecat->get_parents();
                                        $parents   = core_course_category::get_many($parentids);
                                        foreach ($parents as $parent) {
                                            if (!empty($catthemes[$parent->id]->theme) && $parent->is_uservisible()) {
                                                $cattheme = $catthemes[$parent->id]->theme;
                                                $data['coursesview'][$timeline]['pages'][$pageid]['courses'][$index]->themename = $cattheme;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $PAGE->requires->js_call_amd('theme_uonbi/block_myoverview_metadata', 'init');

        return $this->render_from_template('block_myoverview/main', $data);
    }
}
