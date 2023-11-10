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
 * Contains the logic to improve the course-index behaviour.
 *
 * @package   theme_uonbi
 * @author    Sumaiya Javed <sumaiya.javed@catalyst.net.nz>
 * @copyright Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'theme_boost/drawers', 'theme_boost/drawer'],
function($, Drawers, Drawer) {

return {
   'init': function() {

           // Create event object to target boostbar.
           var e = $.Event("click");
           e.originalEvent = 'click';
           e.target = 'button.navbar-toggler.aabtn.d-block';
           var trigger = $(e.target).closest('[data-action=toggle-drawer]');

           // When boost bar is expanded course index closes.
           trigger.on("click", function() {
               const courseindexDrawer = Drawers.getDrawerInstanceForNode(document.querySelector(
                   '#theme_boost-drawers-courseindex'));
               var openBoost = trigger.attr('aria-expanded');
               var openCI = courseindexDrawer.drawerNode.className.includes("show");
               if (!openBoost && !openCI) {
                   // Manually open drawer.
                   courseindexDrawer.openDrawer();
               }
               if (openBoost && openCI) {
                   // Manually close drawer.
                   courseindexDrawer.closeDrawer();
               }
           });

           // When course index is expanded boost bar is closed.
           // Listen to the before show event.
           document.addEventListener(Drawers.eventTypes.drawerShow, event => {
               var openBoost = trigger.attr('aria-expanded') == 'true';
               if (!openBoost) {
                   // Do nothing.
               } else {
                   trigger.trigger("click");
               }
           });

           // On entering a page where course index is already expanded,
           // prevent boost bar being expanded also.
           if($('.pagelayout-course.drawer-open-index').length) {
            $( '#nav-drawer' ).addClass( "closed" ).attr( "aria-hidden","true" );
            $( '.navbar-toggler' ).attr( "aria-expanded","false" );
            $( 'body' ).removeClass( "drawer-open-left" ); 
        };
       }
   };
});
