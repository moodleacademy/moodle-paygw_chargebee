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

defined('MOODLE_INTERNAL') || die();

define('CHARGEBEE_USER_PREFIX', "AC-"); /// TODO: Move this to settings.

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
     * Initialise the Chargebee API client.
     *
     * @param string $apikey
     * @param string $sitename
     */
    public function __construct(string $sitename, string $apikey) {
        $this->apikey = $apikey;
        $this->sitename = $sitename;

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
        // Get the Chargebee HostedPage
        $result = HostedPage::checkoutOneTime(array(
            "currency_code" => $currency,
            "redirectUrl" => $redirecturl,
            "customer" => array(
                "id" => CHARGEBEE_USER_PREFIX . $user->id, /// Get this values from settings/config.
                "email" => $user->email,
                "firstName" => $user->firstname,
                "lastName" => $user->lastname,
            ),
            "charges" => array(array(
                "amount" => $cost * 100,
                "description" => $description
            ))
        ));

        // Return the url of the HostedPage
        return $result->hostedPage()->url;
    }

    /**
     * Verify this transaction is authentic, and corresponds to current transaction.
     *
     * @param string $identifier
     * @param  \stdClass $user
     * @param float $cost
     * @param string $currency
     * @return bool
     */
    public function verify_transaction(string $identifier, $user, float $cost, string $currency): bool {
        // Retrieve hosted page
        $result = HostedPage::retrieve($identifier);
        $hostedPage = $result->hostedPage();

        $timediff = time() - $hostedPage->content['invoice']['paid_at'];

        // Verify against the following details: status, customer_id, amount, currency, date
        if (
            $timediff < 60 &&
            $hostedPage->content['invoice']['status'] === 'paid' &&
            $hostedPage->content['invoice']['customer_id'] == CHARGEBEE_USER_PREFIX . $user->id &&
            $hostedPage->content['invoice']['currency_code'] === $currency &&
            $hostedPage->content['invoice']['amount_paid'] == ($cost * 100)
        ) {
            return true;
        }

        return false;
    }
}
