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
 * Process payment
 *
 * @package    paygw_chargebee
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/payment/gateway/chargebee/.extlib/autoload.php');

use core_payment\helper as payment_helper;
use paygw_chargebee\chargebee_helper;


require_login();

$id = required_param('id', PARAM_ALPHANUMEXT); // Unique identifier of the hosted page resource.
$state = required_param('state', PARAM_TEXT); // State: succeeded, failed or cancelled.
$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_INT);

$config = (object) payment_helper::get_gateway_configuration($component, $paymentarea, $itemid, 'chargebee');
$chargebeehelper = new chargebee_helper($config->sitename, $config->apikey, $config->customeridprefix);

if ($state === $chargebeehelper::STATUS_SUCCEEDED) {
    $payable = payment_helper::get_payable($component, $paymentarea, $itemid);
    $surcharge = payment_helper::get_gateway_surcharge('chargebee');
    $cost = payment_helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

    // Verify transaction.
    if ($chargebeehelper->verify_transaction($id, $USER, $cost, $payable->get_currency())) {
        // Now proceed to save payment details in Moodle.
        $paymentid = payment_helper::save_payment(
            $payable->get_account_id(),
            $component,
            $paymentarea,
            $itemid,
            $USER->id,
            $cost,
            $payable->get_currency(),
            'chargebee'
        );

        // Record Chargebee transaction details.
        $chargebeehelper->save_transaction_details($id, $USER->id, $paymentid);

        payment_helper::deliver_order($component, $paymentarea, $itemid, $paymentid, $USER->id);

        // Find redirection.
        $url = new moodle_url('/');
        // Method only exists in 3.11+.
        if (method_exists('\core_payment\helper', 'get_success_url')) {
            $url = payment_helper::get_success_url($component, $paymentarea, $itemid);
        } else if ($component == 'enrol_fee' && $paymentarea == 'fee') {
            $courseid = $DB->get_field('enrol', 'courseid', ['enrol' => 'fee', 'id' => $itemid]);
            if (!empty($courseid)) {
                $url = course_get_url($courseid);
            }
        }
        redirect($url, get_string('paymentsuccessful', 'paygw_chargebee'), 3, 'success');
    }
    redirect(new moodle_url('/'), get_string('paymentmismatch', 'paygw_chargebee'), 3, 'error');
}

redirect(new moodle_url('/'), get_string('paymentcancelled', 'paygw_chargebee'));
