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
 * Payment processor
 *
 * @package    paygw_chargebee
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/externallib/autoload.php');

use core_payment\helper as payment_helper;
use paygw_chargebee\chargebee_helper;
use ChargeBee\ChargeBee\Models\HostedPage;

require_login();

$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);

// Get fee details
$enrolfee = $DB->get_record('enrol', ['enrol' => 'fee', 'id' => $itemid]);

if (!empty($enrolfee)) {
  $course = $DB->get_record('course', ['id' => $enrolfee->courseid]);
  //$url = course_get_url($courseid);
  // Chargebee also returns an id param after successful payment
  $url = $CFG->wwwroot . '/course/view.php?name=' . $course->shortname;
} else {
  $url = $CFG->wwwroot;
}

$config = (object) payment_helper::get_gateway_configuration($component, $paymentarea, $itemid, 'chargebee');
$payable = payment_helper::get_payable($component, $paymentarea, $itemid);
$surcharge = payment_helper::get_gateway_surcharge('chargebee');
$cost = payment_helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

$chargebeehelper = new chargebee_helper($config->sitename, $config->apikey);

/// TODO: Move this into helper
$result = HostedPage::checkoutOneTime(array(
  "currency_code" => $payable->get_currency(),
  "redirectUrl" => $url,
  "billingAddress" => array(
    "firstName" => $USER->firstname,
    "lastName" => $USER->lastname,
    "line1" => "PO Box 9999",
    "city" => "Perth",
    "state" => "Western Australia",
    "zip" => "6872",
    "country" => "AU"
  ),
  "customer" => array(
    "id" => "__ma_test__moodleid-" . $USER->id,
    "email" => $USER->email,
    "firstName" => $USER->firstname,
    "lastName" => $USER->lastname,
  ),
  "charges" => array(array(
    "amount" => $cost * 100,
    "description" => "Course fee"
  ))
));

$hostedPage = $result->hostedPage();

redirect($hostedPage->url);
