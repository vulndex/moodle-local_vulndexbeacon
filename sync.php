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
 * Perform sync manual by user action.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);
require_once(__DIR__ . '/../config.php');

global $PAGE;
defined('MOODLE_INTERNAL') || die();

try {
    require_login();
    require_sesskey();

    $context = context_system::instance();
    require_capability('local/vulndexbeacon:sendnow', $context);

    $PAGE->set_url(new moodle_url('/local/vulndexbeacon/sync.php'));
    $PAGE->set_context($context);

    $task = new \local_vulndexbeacon\task\send_version();
    $task->execute();

    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'local_vulndexbeacon']),
        get_string('sync_triggered', 'local_vulndexbeacon'),
        3
    );
} catch (require_login_exception $e) {
    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'local_vulndexbeacon']),
        get_string('sync_exception', 'local_vulndexbeacon'),
        10
    );
}
