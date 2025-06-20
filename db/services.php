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
 * @author     Peter Spicer <peter.spicer@catalyst.net.nz>
 */

$services = [
    'uonbi' => [
        'functions' => [
            'theme_uonbi_course_metadata',
        ],
        'requiredcapability' => '',
        'restrictedusers' => false,
        'enabled' => 1,
        'shortname' => 'uonbi',
    ]
];

$functions = [
    'theme_uonbi_course_metadata' => [
        'classname' => 'theme_uonbi\\external\\course',
        'methodname' => 'course_metadata',
        'description' => 'Fetch additional metadata about courses',
        'type' => 'read',
        'ajax' => true,
    ],
];
