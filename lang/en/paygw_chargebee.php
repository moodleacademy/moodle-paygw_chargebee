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
 * Plugin strings are defined here.
 *
 * @package     paygw_chargebee
 * @category    string
 * @copyright   2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Chargebee';
$string['pluginname_desc'] = 'The Chargebee plugin allows you to receive payments via Chargebee with Braintree.';
$string['apikey'] = 'API Key';
$string['apikey_help'] = 'The API key that we use to identify ourselves with Chargebee';
$string['sitename'] = 'Site name';
$string['sitename_help'] = 'Chargebee site name';
$string['customeridprefix'] = 'Customer id prefix';
$string['customeridprefix_help'] = 'Prefix to use when creating new customer accounts in Chargebee';
$string['lineitemprefix'] = 'Invoice item prefix';
$string['lineitemprefix_help'] = 'Prefix to use for items in the invoice';
$string['paymentmethods'] = 'Payment Methods';
$string['gatewayname'] = 'Chargebee';
$string['gatewaydescription'] = 'Braintree is an authorised payment gateway provider for processing credit card transactions.';
$string['paymentsuccessful'] = 'Payment was successful.';
$string['paymentcancelled'] = 'Payment was cancelled.';
$string['paymentalreadyexists'] = 'Error. This transaction was already recorded.';
$string['privacy:metadata:paygw_chargebee'] = 'Stores the relation from Moodle users to Chargebee customer objects';
$string['privacy:metadata:paygw_chargebee:userid'] = 'Moodle user ID';
$string['privacy:metadata:paygw_chargebee:customerid'] = 'Customer ID returned from Chargebee';
