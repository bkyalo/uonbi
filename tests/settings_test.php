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
 * Unit tests for settings.
 *
 * @package     theme_uonbi
 * @author      Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class theme_uonbi_settings_testcase extends basic_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot.'/lib/adminlib.php');
        require_once(__DIR__ . '/../settings.php');
    }

    /**
     * Test for input validation 'theme_uonbi/courseicon'.
     * @dataProvider iconname_provider
     * @param string $iconname icon name for validation
     * @param string|bool $expected expected result
     */
    public function test_input_text_validation_course_icon(string $iconname, $expected) {

        $setting = new admin_setting_configtext('name', 'title', 'description', 'icon-graduation');
        // Allow only string from 'fa-' (Font Awesome) or 'icon-' (Simple Line Icons).
        $setting->paramtype = THEME_UONBI_SETTINGS_ICON_REGEX;

        // This should pass.
        $result = $setting->validate($iconname);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider for icon name.
     * @return array[]
     * @throws coding_exception
     */
    public function iconname_provider(): array {
        $errormsg = get_string('validateerror', 'admin');
        return [
            ['icon-abc', true],
            ['supericon-abc', $errormsg],
            ['icon-', $errormsg],
            ['fa-zzz', true],
            ['fazzz', $errormsg],
            ['superfa-zzz', $errormsg],
            ['fa-', $errormsg],
        ];
    }
}
