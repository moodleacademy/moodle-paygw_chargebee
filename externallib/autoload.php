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
 * Class loaders and includes required for chargebee.
 *
 * @package    paygw_chargebee
 * @copyright  2022 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/// TODO: Is there a better way to do this?
// Manually include Guzzle libs for now.
require_once(__DIR__ . '/GuzzleHttp/functions_include.php');
require_once(__DIR__ . '/GuzzleHttp/Psr7/functions_include.php');
require_once(__DIR__ . '/GuzzleHttp/Promise/functions_include.php');

require_once($CFG->dirroot . '/externallib/chargebee-php/lib/init.php');

spl_autoload_register(
    function ($classname) {
        $map = [
            'GuzzleHttp' => 'GuzzleHttp',
        ];
        foreach ($map as $namespace => $subpath) {
            $classpath = explode('_', $classname);
            if ($classpath[0] != $namespace) {
                $classpath = explode('\\', $classname);
                if ($classpath[0] != $namespace) {
                    continue;
                }
            }
            $subpath = __DIR__ . '/';
            $filepath = $subpath . implode('/', $classpath) . '.php';
            if (file_exists($filepath)) {
                require_once($filepath);
            }
        }
    }
);
