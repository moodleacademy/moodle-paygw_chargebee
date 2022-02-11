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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/externallib/autoload.php');

/**
 * The helper class for Chargebee payment gateway.
 *
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chargebee_helper {
    /**
     * @var  string Charge Site name.
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
    public function __construct(string $sitename, string $apikey)
    {
        $this->apikey = $apikey;
        $this->sitename = $sitename;

        Environment::configure($this->sitename, $this->apikey);
    }
}
