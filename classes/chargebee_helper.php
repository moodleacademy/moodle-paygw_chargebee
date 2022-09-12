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
 * Various helper methods for interacting with the Chargebee API
 *
 * @package    paygw_chargebee
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_chargebee;

use ChargeBee\ChargeBee\Environment;
use ChargeBee\ChargeBee\Models\HostedPage;
use ChargeBee\ChargeBee\Models\Invoice;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/payment/gateway/chargebee/.extlib/autoload.php');

/**
 * The helper class for Chargebee payment gateway.
 *
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chargebee_helper {
    /**
     * @var string Transaction status - Success
     */
    public const STATUS_SUCCEEDED = 'succeeded';

    /**
     * @var string Transaction status - Failed
     */
    public const STATUS_FAILED = 'failed';

    /**
     * @var string Transaction status - Cancelled
     */
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @var  string Chargebee Site name.
     */
    private $sitename;
    /**
     * @var string Chargebee API key.
     */
    private $apikey;

    /**
     * @var string Prefix to use when creating customers in Chargebee.
     */
    private $customeridprefix;

    /**
     * Initialise the Chargebee API client.
     *
     * @param string $sitename
     * @param string $apikey
     * @param string $customeridprefix
     */
    public function __construct(string $sitename, string $apikey, string $customeridprefix) {
        $this->apikey = $apikey;
        $this->sitename = $sitename;
        $this->customeridprefix = $customeridprefix;

        Environment::configure($this->sitename, $this->apikey);
    }

    /**
     * Fetch Hosted page for one-time payment checkout and return url.
     *
     * @param \stdClass $user
     * @param float $cost
     * @param string $currency
     * @param string $description
     * @param string $redirecturl
     * @return string Hosted page url
     */
    public function get_checkout_url($user, float $cost, string $currency, string $description, string $redirecturl): string {
        // Get the Chargebee HostedPage.
        $result = HostedPage::checkoutOneTime(array(
            "currency_code" => $currency,
            "redirectUrl" => $redirecturl,
            "customer" => array(
                "id" => $this->customeridprefix . $user->id,
                "email" => $user->email,
                "firstName" => $user->firstname,
                "lastName" => $user->lastname,
            ),
            "charges" => array(array(
                "amount" => $this->get_unit_amount($cost, $currency),
                "description" => $description
            ))
        ));

        // Return the url of the HostedPage.
        return $result->hostedPage()->url;
    }

    /**
     * Verify this transaction is authentic, and corresponds to current transaction.
     *
     * @param string $identifier unique identifier of the hosted page resource
     * @param  \stdClass $user
     * @return bool
     */
    public function verify_transaction(string $identifier, $user): bool {
        global $DB;

        // Retrieve hosted page.
        $hostedpage = $this->get_hosted_page($identifier);

        if (
            $hostedpage->content['invoice']['status'] === 'paid' &&
            $hostedpage->content['invoice']['customer_id'] == $this->customeridprefix . $user->id
        ) {
            // Check if invoice transaction id exists in db already.
            if (!$record = $DB->get_record(
                'paygw_chargebee',
                array(
                    'transactionid' => $hostedpage->content['invoice']['linked_payments'][0]['txn_id'],
                    'userid' => $user->id
                )
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a Chargebee Hosted Page from a given unique identifier
     *
     * @param string $identifier unique identifier of the hosted page resource
     * @return
     */
    public function get_hosted_page(string $identifier) {
        // Retrieve hosted page.
        $result = HostedPage::retrieve($identifier);

        return $result->hostedPage();
    }

    /**
     * Void an unpaid invoice
     *
     * @param string $identifier unique identifier of the hosted page resource
     * @param  int $userid id of the user
     * @return boolean true of success, false otherwise
     */
    public function void_unpaid_invoice(string $identifier, int $userid): bool {
        // Retrieve hosted page.
        $hostedpage = $this->get_hosted_page($identifier);

        try {
            if (
                $hostedpage->content['invoice']['status'] === 'payment_due' &&
                $hostedpage->content['invoice']['customer_id'] == $this->customeridprefix . $userid &&
                $hostedpage->content['invoice']['amount_paid'] == '0'
            ) {
                // We have an unpaid invoice, and nothing has been paid yet.
                Invoice::voidInvoice($hostedpage->content['invoice']['id']);
                return true;
            }

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Record Chargebee transaction details
     *
     * @param string $identifier unique identifier of the hosted page resource
     * @param integer $userid id of the user
     * @param integer $paymentid id from payments table
     * @return void
     */
    public function save_transaction_details(string $identifier, int $userid, int $paymentid): void {
        global $DB;

        $hostedpage = $this->get_hosted_page($identifier);

        $record = new \stdClass();
        $record->paymentid = $paymentid;
        $record->userid = $userid;
        $record->customerid = $hostedpage->content['invoice']['customer_id'];
        $record->transactionid = $hostedpage->content['invoice']['linked_payments'][0]['txn_id'];
        $record->invoicenumber = $hostedpage->content['invoice']['id'];
        $record->amountpaid = $this->get_paid_amount($hostedpage->content['invoice']['amount_paid'],
            $hostedpage->content['invoice']['currency_code']);

        $DB->insert_record('paygw_chargebee', $record);
    }

    /**
     * Convert the cost into the unit amount accounting for zero-decimal currencies.
     *
     * @param float $cost
     * @param string $currency
     * @return float
     */
    public function get_unit_amount(float $cost, string $currency): float {
        if (in_array($currency, gateway::get_zero_decimal_currencies())) {
            return $cost;
        }
        return $cost * 100;
    }

    /**
     * Convert the amount paid into the decimal amount accounting for zero-decimal currencies.
     *
     * @param float $amount
     * @param string $currency
     * @return float
     */
    public function get_paid_amount(float $amount, string $currency): float {
        if (in_array($currency, gateway::get_zero_decimal_currencies())) {
            return $amount;
        }
        return $amount / 100;
    }
}
