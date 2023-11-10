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
 * Web service definition for the uonbi theme
 *
 * @package    theme_uonbi
 * @copyright  2019 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Peter Spicer <peter.spicer@catalyst-eu.net>
 */

namespace theme_uonbi\external;
use external_api;
use external_value;
use external_multiple_structure;
use external_single_structure;
use external_function_parameters;
use moodle_url;
use context_course;

defined('MOODLE_INTERNAL') || die;

class course extends external_api {

    /**
     * Button options for course_overview block when in card view.
     */
    public const CARD_OPTIONS = [
        'participants',
        'grades',
        'badges',
        'forums',
        'settings',
    ];

    public static function course_metadata_parameters() {
        return new external_function_parameters(
            [
                'courses' => new external_value(PARAM_SEQUENCE, 'CSV list of courses, e.g. 1,2,3'),
            ]
        );
    }

    public static function course_metadata($courses) {
        global $DB, $CFG;

        $params = self::validate_parameters(self::course_metadata_parameters(), ['courses' => $courses]);

        $courses = explode(',', $params['courses']);
        $courses = array_map('intval', $courses);
        $courses = array_diff($courses, [0]);

        // Get the courses the current user is enrolled in (array).
        $enroledcourses = enrol_get_my_courses();

        $return = [];
        foreach ($courses as $courseid) {
            $course = [
                'id' => $courseid,
                'participants_url' => (new moodle_url('/user/index.php', ['id' => $courseid]))->out(),
            ];

            $coursecontext = context_course::instance($courseid);
            if (has_capability('moodle/course:update', $coursecontext)) {
                $course['grades_url'] = (new moodle_url('/grade/report/index.php', ['id' => $courseid]))->out();
            } else {
                $course['grades_url'] = (new moodle_url('/grade/report/user/index.php', ['id' => $courseid]))->out();
            }

            $course['badges_url'] = '';
            if ($CFG->enablebadges) {
                $course['badges_url'] = (new moodle_url('/badges/view.php', ['type' => 2, 'id' => $courseid]))->out();
            }

            $course['forums_url'] = (new moodle_url('/mod/forum/index.php', ['id' => $courseid]))->out();

            $course['course_url'] = '';
            $course['settings_url'] = '';
            if (has_capability('moodle/course:update', $coursecontext)) {
                if (get_config('theme_uonbi', 'alt_behaviour_settings_button')) {
                    $params = [
                        'id' => $courseid,
                        'sesskey' => sesskey(),
                        'edit' => 'on',
                    ];
                    $course['settings_alt_url'] = (new moodle_url('/course/view.php', $params))->out();
                } else {
                    $course['settings_url'] = (new moodle_url('/course/edit.php', array('id' => $courseid)))->out();
                }
            } else {
                $course['course_url'] = (new moodle_url('/course/view.php', ['id' => $courseid]))->out();
            }
            // Check that the user is enroled in the course before adding the course summary to the data to be displayed.
            $course['summary'] = '';
            if (array_key_exists($courseid, $enroledcourses)) {
                /* The course summary will not be filtered (by using functions like: s() or format_text()) as this will
                * remove the elements required by the bootstrap functionality.
                * By not filtering the course summary we ensure it will display and work the same as on the course's info page.
                */
                $course['summary'] = $DB->get_field('course', 'summary', ['id' => $courseid], IGNORE_MISSING);
            }

            $return[] = $course;
        }
        return $return;
    }

    public static function course_metadata_returns() {
        $keys = [
            'id' => new external_value(PARAM_INT, 'Course id'),
            'course_url' => new external_value(PARAM_URL, 'Course URL'),
            'summary' => new external_value(PARAM_RAW, 'Course summary'),
        ];

        $cardoptions = static::CARD_OPTIONS;
        $pluginconfig = get_config('theme_uonbi');

        foreach ($cardoptions as $cardoption) {
            if ($pluginconfig->{$cardoption.'_button'}) {
                $desc = ucfirst("{$cardoption} URL");

                if ($cardoption == 'settings') {
                    $desc .= ' if appropriate';
                    if ($pluginconfig->alt_behaviour_settings_button) {
                        $cardoption .= '_alt';
                    }
                }
                $keys["{$cardoption}_url"] = new external_value(PARAM_URL, $desc);
            }
        }

        return new external_multiple_structure(
            new external_single_structure($keys)
        );
    }
}
