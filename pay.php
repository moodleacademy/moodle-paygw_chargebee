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
require_once($CFG->dirroot . '/payment/gateway/chargebee/.extlib/autoload.php');

use core_payment\helper as payment_helper;
use paygw_chargebee\chargebee_helper;

require_login();

$component = required_param('component', PARAM_ALPHANUMEXT);
$paymentarea = required_param('paymentarea', PARAM_ALPHANUMEXT);
$itemid = required_param('itemid', PARAM_INT);
$description = required_param('description', PARAM_TEXT);

$config = (object) payment_helper::get_gateway_configuration($component, $paymentarea, $itemid, 'chargebee');
$payable = payment_helper::get_payable($component, $paymentarea, $itemid);
$surcharge = payment_helper::get_gateway_surcharge('chargebee');
$cost = payment_helper::get_rounded_cost($payable->get_amount(), $payable->get_currency(), $surcharge);

$chargebeehelper = new chargebee_helper($config->sitename, $config->apikey, $config->customeridprefix);

// Moodle sets this to &amp; when '&' is expected. see: MDL-71368.
// $redirecturl = new moodle_url(
//   '/payment/gateway/chargebee/process.php',
//   array('component' => $component, 'paymentarea' => $paymentarea, 'itemid' => $itemid)
// );

$redirecturl = $CFG->wwwroot . '/payment/gateway/chargebee/process.php?component=' . $component . '&paymentarea=' .
  $paymentarea . '&itemid=' . $itemid;
/// TODO: Move this into helper
// $result = HostedPage::checkoutOneTime(array(
//   "currency_code" => $payable->get_currency(),
//   "redirectUrl" => $redirecturl,
//   // "billingAddress" => array(
//   //   "firstName" => $USER->firstname,
//   //   "lastName" => $USER->lastname,
//   //   "line1" => "PO Box 9999",
//   //   "city" => "Perth",
//   //   "state" => "Western Australia",
//   //   "zip" => "6872",
//   //   "country" => "AU"
//   // ),
//   "customer" => array(
//     "id" => "AC-" . $USER->id,
//     "email" => $USER->email,
//     "firstName" => $USER->firstname,
//     "lastName" => $USER->lastname,
//   ),
//   "charges" => array(array(
//     "amount" => $cost * 100,
//     "description" => $description
//   ))
// ));

// $hostedPage = $result->hostedPage();

$checkouturl = $chargebeehelper->get_checkout_url($USER, $cost, $payable->get_currency(), $description, $redirecturl);

redirect($checkouturl);
