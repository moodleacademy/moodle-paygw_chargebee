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
 * Privacy Subsystem implementation for paygw_stripe.
 *
 * @package    paygw_chargebee
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_chargebee\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy Subsystem implementation for paygw_chargebee.
 *
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\data_provider {

    /**
     * Returns metadata about this plugin.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored in this plugin.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'paygw_chargebee',
            [
                'userid' => 'privacy:metadata:paygw_chargebee:userid',
                'customerid' => 'privacy:metadata:paygw_chargebee:customerid',
            ],
            'privacy:metadata:paygw_chargebee'
        );
        return $collection;
    }
}