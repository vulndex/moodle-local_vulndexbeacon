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
 * German strings.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);
$string['apiendpoint'] = 'API-Endpunkt';
$string['pluginname'] = 'VulnDex Beacon';
$string['apiendpoint_desc'] = 'HTTPS-URL, an die die Version als JSON per POST gesendet wird.';
$string['apikey'] = 'API-Schlüssel';
$string['apikey_desc'] = 'Bearer-Token zur Authentifizierung.';
$string['nodeid'] = 'Node-Kennung';
$string['nodeid_desc'] = 'Interne Kennung für diese Moodle-Instanz.';
$string['vulndexbeacon:sendnow'] = 'Datentransfer zu VulnDex';
$string['manualsync'] = 'Manueller Sync';
$string['privacy:metadata'] =
    'VulnDex Beacon übermittelt mit gültigem API-Schlüssel folgende möglicherweise personenbezogene Daten: IP-Adresse, Hostname des Servers, Name des Plugins und des Themes sowie die zugehörigen Versionen und der Status. Darüber hinaus werden die PHP-Version und die OS-Version übermittelt.';
$string['task_send_version'] = 'Moodle-Version an externen Endpunkt senden';
$string['manualsync_button'] = 'Sync jetzt ausführen';
$string['manualsync_desc'] = 'Löst sofort einen Sync zum externen Endpunkt mit der aktuellen Konfiguration aus.';
$string['laststatus'] = 'Letzter Sync-Status';
$string['sync_triggered'] = 'Manueller Sync wurde gestartet. Details sind in den Logs ersichtlich.';
$string['laststatus_desc'] = 'Zusammenfassung des letzten Sync-Versuchs zur VulnDex API.';
$string['laststatus_never'] = 'Bisher wurde noch kein Sync ausgeführt.';
$string['sync_exception'] = 'Während dem manuellen Sync ist ein Fehler aufgetreten.';
