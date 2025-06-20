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
 * Hooks on top of the myoverview block, waits for its principle rendering
 * to be done (e.g. after the user selects something), and fetches
 * additional data from a webservice in the University of Nairobi theme to add extra
 * UI elements, such as more navigation.
 *
 * @module     theme/uonbi
 * @class      block_myoverview_metadata
 * @package    theme/uonbi
 * @copyright  2019 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Peter Spicer <peter.spicer@catalyst-eu.net>
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, Ajax, Templates, Notification) {

    return {
        observer: null,
        registerObserver: function() {
            // Any changes to the block_myoverview DOM area will be passed to this function.
            this.observer = new MutationObserver(function(mutations, observer) {
                observer.newcourses = [];
                observer.hascourses = false;

                /* We receive a list of changes from the MutationObserver here, though this will
                 * likely take the form of (collections of nodes removed) as existing content is
                 * removed, and then (collections of nodes added) as the template rendering is
                 * transplanted into the DOM.
                 */
                for (var i = 0; i < mutations.length; i++) {
                    // If it's not adding nodes, this can't be relevant to us.
                    if (!mutations[i].addedNodes) {
                        continue;
                    }
                    for (var j = 0; j < mutations[i].addedNodes.length; j++) {
                        // If it's not adding nodes we can target with selectors, this can't be relevant.
                        if (!mutations[i].addedNodes[j].querySelectorAll) {
                            continue;
                        }

                        // Find all the nodes added that have our data attribute; this should be one per course item.
                        var changes = mutations[i].addedNodes[j].querySelectorAll('[data-overview-course-id]');
                        if (changes && changes.length) {
                            for (var k = 0; k < changes.length; k++) {
                                var courseid = changes[k].dataset.overviewCourseId;
                                observer.hascourses = true;
                                // If we haven't seen this course at some point in this run, we need to get its data.
                                if (!observer.cache.hasOwnProperty(courseid)) {
                                    observer.newcourses.push(courseid);
                                }
                            }
                        }
                    }
                }

                if (observer.newcourses.length > 0) {
                    observer.fetchCourses(observer.newcourses);
                } else if (observer.hascourses) {
                    observer.updateCourses();
                }
            });
            this.observer.cache = {};
            /* This is where the MutationObserver is actually activated - since we will have DOM changes inside
             * this area later on, we want to be able to track the object, so we've already made it, and then
             * we turn it on, rather than just running it as soon as we initialise it.
             */
            this.observer.selector = document.querySelector('#block-region-content .block_myoverview');
            if (this.observer.selector) {
                this.observer.fetchCourses = this.fetchCourses.bind(this.observer);
                this.observer.updateCourses = this.updateCourses.bind(this.observer);
                this.observer.observe(this.observer.selector, {
                    childList: true,
                    subtree: true
                });
            }
        },
        init: function() {
            this.registerObserver();
        },
        fetchCourses: function(newcourses) {
            // If there are courses we don't know about, call this theme's course_metadata webservice to populate.
            var addToCache = function(data) {
                data.forEach(function(course) {
                    this.cache[course.id] = course;
                }, this);
                this.updateCourses();
            };
            Ajax.call([{
                methodname: 'theme_uonbi_course_metadata',
                args: { courses: newcourses.join(',') },
                done: addToCache.bind(this),
                fail: function(data) { throw data.error; }
            }]);
        },
        updateCourses: function() {
            // Stop the observer so the changes we want to make aren't captured.
            this.disconnect();

            /* Moodle's template rendering process is Promise-based (both to render and to transplant rendered
             * content into the DOM), so we need to stack up all these changes into a collection of Promises
             * so we can re-enable listening on the DOM for the block_myoverview content after we've added our
             * content. This way, if a user selects a new option, changing order or format, we can still
             * render our changes.
             */

            var promises = [];
            // This is our template promise that encapsulates the rendering; bound to the correct scope.
            var promise = function(courseid, sel) {
                Templates.render('block_myoverview/cards-buttons', this.cache[courseid]).then(function(html, js) {
                    return Templates.replaceNodeContents(sel, html, js);
                });
            }.bind(this);

            // This replaces the summary in the view-summary template with the one returned by the fetchCourses Ajax call.
            var promise_summary = function(courseid, summary) {
                summary.children().remove();
                summary.append(this.cache[courseid]['summary']);
            }.bind(this);

            for (var courseid in this.cache) {
                if (!this.cache.hasOwnProperty(courseid)) {
                    continue;
                }
                // This tries to find the course card with summary icons container. If not found (e.g. on list view), skip.
                var sel = $('#block-region-content .block_myoverview [data-overview-course-id=' + courseid + '] .summary-icons');
                if (sel.length != 0) {
                    promises.push(
                        promise(courseid, sel)
                    );
                }

                // This tries to find the course summary. If not found (e.g. on list view), skip.
                var summary = $('#course-summary-'+courseid);
                if (summary.children().length != 0) {
                    promises.push(
                        promise_summary(courseid, summary)
                    );
                }
            }
            var obs = function() {
                // Restart the observer now we have done the various changes we care
                this.observe(this.selector, {
                    childList: true,
                    subtree: true
                });
            }.bind(this);
            Promise.all(promises).then(function() {
                // Now we're all done, restart the observer using the above function, just bound to the right scope.
                obs();
            }).catch(Notification.exception);
        }
    };
});