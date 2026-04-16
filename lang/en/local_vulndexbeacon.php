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

/**
 * English strings.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);
$string['pluginname'] = 'VulnDex Beacon';
$string['apiendpoint'] = 'API endpoint';
$string['apiendpoint_desc'] = 'HTTPS URL to send version payload as JSON via POST.';
$string['apikey'] = 'API key';
$string['apikey_desc'] = 'Bearer token used to authenticate.';
$string['nodeid'] = 'Node identifier';
$string['nodeid_desc'] = 'Your internal node ID for this Moodle instance.';
$string['task_send_version'] = 'Send Moodle version to external endpoint';
$string['privacy:metadata'] =
    'When used with a valid API key, VulnDex Beacon transmits the following data, some of which may be personal: IP address, server hostname, plugin and theme names, along with their respective versions and status. In addition, the PHP version and OS version are transmitted.';
$string['manualsync'] = 'Manual sync';
$string['manualsync_desc'] = 'Trigger an immediate sync to the external endpoint using the current configuration.';
$string['manualsync_button'] = 'Run sync now';
$string['sync_triggered'] = 'Manual sync has been triggered. Check the logs for details.';
$string['laststatus'] = 'Last sync status';
$string['laststatus_desc'] = 'Summary of the last sync attempt to the VulnDex API.';
$string['laststatus_never'] = 'No sync has been executed yet.';
$string['sync_exception'] = 'An error occurred during the manual synchronisation.';
