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
 * Page loader waiting javascript.
 *
 * @package    format_twocol
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    var Waiting = {};

    /**
     * Initialise the class.
     *
     * @public
     */
    Waiting.init = function() {
        // Check if waiting spinner is enabled.
        var modaldata = document.querySelector('#loadingmodal');

        // Only enable spinner if set in data.
        if (modaldata.dataset.waiting == "1") {
            window.addEventListener("beforeunload", function () {
                // If page takes longer than 1 second to load,
                // display loading spinner.
                setTimeout(function(){
                    $('#loadingmodal').modal();
                }, 1000);
           });
        }
    };

    return Waiting;

});
