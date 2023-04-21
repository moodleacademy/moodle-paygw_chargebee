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
$string['apikey'] = 'API key';
$string['apikey_help'] = 'The API key that we use to identify ourselves with Chargebee';
$string['sitename'] = 'Site name';
$string['sitename_help'] = 'Chargebee site name';
$string['customeridprefix'] = 'Customer id prefix';
$string['customeridprefix_help'] = 'Prefix to use when creating new customer accounts in Chargebee';
$string['lineitemprefix'] = 'Invoice item prefix';
$string['lineitemprefix_help'] = 'Prefix to use for items in the invoice';
$string['autovoidinvoice'] = 'Void unpaid invoice';
$string['autovoidinvoice_help'] = 'Automatically void unpaid invoices, if payment has failed';
$string['paymentmethods'] = 'Payment methods';
$string['gatewayname'] = 'Chargebee';
$string['gatewaydescription'] = 'Chargebee payment gateway allows processing of credit card and PayPal transactions.';
$string['paymentsuccessful'] = 'Payment was successful.';
$string['paymentcancelled'] = 'Payment was cancelled.';
$string['paymentalreadyexists'] = 'Error. This transaction was already recorded.';
$string['transactionfailed'] = 'Error. This transaction could not be completed.';
$string['privacy:metadata:paygw_chargebee'] = 'Stores the relation from Moodle users to Chargebee customer objects';
$string['privacy:metadata:paygw_chargebee:userid'] = 'Moodle user ID';
$string['privacy:metadata:paygw_chargebee:customerid'] = 'Customer ID used in Chargebee';
$string['privacy:metadata:paygw_chargebee:transactionid'] = 'Transaction identifier in Chargebee';
$string['privacy:metadata:paygw_chargebee:invoicenumber'] = 'Invoice number of this transaction in Chargebee';
$string['privacy:metadata:paygw_chargebee:amountpaid'] = 'Amount paid for this transaction';
$string['privacy:metadata:paygw_chargebee_com'] = 'Shares the required user data with Chargebee for processing payments';
$string['privacy:metadata:paygw_chargebee_com:userid'] = 'Moodle user ID of the user requesting a transaction';
$string['privacy:metadata:paygw_chargebee_com:firstname'] = 'First name of the user requesting a transaction';
$string['privacy:metadata:paygw_chargebee_com:lastname'] = 'Last name of the user requesting a transaction';
$string['privacy:metadata:paygw_chargebee_com:email'] = 'Email of the user requesting a transaction';
$string['commentvoidinvoice'] = 'Automatically voided because payment declined.';
$string['eventtransactionstarted'] = 'Payment transaction started';
$string['eventtransactioncompleted'] = 'Payment transaction completed';
$string['eventtransactionsuccessful'] = 'Payment transaction successful';
$string['eventtransactionfailed'] = 'Payment transaction failed';
$string['eventtransactioncancelled'] = 'Payment transaction cancelled';
$string['eventvoidinvoicesuccessful'] = 'Void unpaid invoice successful';
$string['eventvoidinvoicefailed'] = 'Void unpaid invoice failed';
$string['errtransactionverificationfailed'] = 'Transaction verification failed';
$string['errchargebeeerrorstatus'] = 'Chargebee error status: {$a}';
