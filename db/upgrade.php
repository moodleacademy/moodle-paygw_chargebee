<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     paygw_chargebee
 * @category    upgrade
 * @copyright   2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute paygw_chargebee upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_paygw_chargebee_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022030100) {
        // Define field amountpaid to be added to paygw_chargebee.
        $table = new xmldb_table('paygw_chargebee');
        $field = new xmldb_field('amountpaid', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'invoicenumber');

        // Conditionally launch add field amountpaid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Chargebee savepoint reached.
        upgrade_plugin_savepoint(true, 2022030100, 'paygw', 'chargebee');
    }

    return true;
}
