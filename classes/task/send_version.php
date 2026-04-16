<?php
// This file is part of VulnDex Beacon for Moodle - http://moodle.org/
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

declare(strict_types=1);
namespace local_vulndexbeacon\task;

use core\task\scheduled_task;
use local_vulndexbeacon\util\payload;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Class representing a scheduled task to send version data to a remote API.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_version extends scheduled_task {
    /**
     * The API endpoint for connecting to the Vulndex beacon service in Moodle.
     */
    private const API_ENDPOINT = 'https://api.vulndex.at/beacon/moodle';

    /**
     * Retrieves the name of the task.
     *
     * @return string The localized name of the task.
     */
    public function get_name(): string {
        return get_string('task_send_version', 'local_vulndexbeacon');
    }

    /**
     * Executes the task to send data to a configured API endpoint.
     *
     * This method retrieves configuration values such as the API key and node ID,
     * decrypts the API key, builds the request payload, and performs an HTTP POST
     * request to the API endpoint. It also handles exceptions, logs output messages,
     * and updates configuration settings based on the response status and content.
     *
     * @return void
     */
    public function execute(): void {
        $endpoint = self::API_ENDPOINT;
        $apikeyencrytped = trim((string)get_config('local_vulndexbeacon', 'apikey'));
        $nodeid = trim((string)get_config('local_vulndexbeacon', 'nodeid'));
        try {
            if (empty($apikeyencrytped) || empty($nodeid)) {
                mtrace('local_vulndexbeacon: missing config (apikey/nodeid).');
                return;
            }
            $apikey = \core\encryption::decrypt($apikeyencrytped);
            if ($apikey === '') {
                mtrace('local_vulndexbeacon: missing apikey (unable to decrypt).');
            }

            $data = payload::build();
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $curl = new \curl();
            $curl->setHeader('Content-Type: application/json');
            $curl->setHeader('Authorization: Bearer ' . $apikey);

            // Timeout konservativ.
            $options = [
                'timeout' => 10,
                'ssl_verifypeer' => true,
                'ssl_verifyhost' => 2,
            ];

            $response = $curl->post($endpoint, $json, $options);
            $info = $curl->get_info();
            $errno = $curl->get_errno();

            $httpcode = $info['http_code'] ?? 0;
            $body = (string)$response;

            if ($errno) {
                $msg = 'cURL error ' . $errno . ' - ' . $curl->error;
                mtrace('local_vulndexbeacon: ' . $msg);

                \set_config('laststatuscode', 0, 'local_vulndexbeacon');
                \set_config('laststatusmessage', $msg, 'local_vulndexbeacon');
                \set_config('laststatusupdated', time(), 'local_vulndexbeacon');
                return;
            }

            if ($httpcode < 200 || $httpcode >= 300) {
                $msg = 'HTTP ' . $httpcode . ' response: ' . $body;
                mtrace('local_vulndexbeacon: ' . $msg);
            } else {
                $msg = 'sent ok (HTTP ' . $httpcode . ').';
                mtrace('local_vulndexbeacon: ' . $msg);
            }

            \set_config('laststatuscode', $httpcode, 'local_vulndexbeacon');
            \set_config('laststatusmessage', $msg, 'local_vulndexbeacon');
            \set_config('laststatusupdated', time(), 'local_vulndexbeacon');
        } catch (\JsonException $e) {
            mtrace('local_vulndexbeacon: invalid json response');
            \set_config('laststatusupdated', time(), 'local_vulndexbeacon');
            \set_config('laststatusmessage', 'invalid json response', 'local_vulndexbeacon');
        } catch (\coding_exception $e) {
            mtrace('local_vulndexbeacon: api key decryption error');
            \set_config('laststatusupdated', time(), 'local_vulndexbeacon');
            \set_config('laststatusmessage', 'api key decryption error', 'local_vulndexbeacon');
        }
    }
}
