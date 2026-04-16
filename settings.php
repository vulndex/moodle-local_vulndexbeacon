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
 * Admin settings for the local_vulndexbeacon plugin.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);
defined('MOODLE_INTERNAL') || die();

global $ADMIN;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_vulndexbeacon', get_string('pluginname', 'local_vulndexbeacon'));

    $nodeid = (string)get_config('local_vulndexbeacon', 'nodeid');
    if ($nodeid === '') {
        try {
            $nodeid = bin2hex(random_bytes(16));
        } catch (Throwable $e) {
            $nodeid = uniqid('vulndex_', true);
        }

        set_config('nodeid', $nodeid, 'local_vulndexbeacon');
    }

    $settings->add(new admin_setting_encryptedpassword(
        'local_vulndexbeacon/apikey',
        get_string('apikey', 'local_vulndexbeacon'),
        get_string('apikey_desc', 'local_vulndexbeacon')
    ));

    $settings->add(new admin_setting_heading(
        'local_vulndexbeacon/nodeid',
        get_string('nodeid', 'local_vulndexbeacon'),
        html_writer::tag('p', get_string('nodeid_desc', 'local_vulndexbeacon')) .
        html_writer::tag('code', s($nodeid))
    ));

    $lastcode = get_config('local_vulndexbeacon', 'laststatuscode');
    $lastmsg = get_config('local_vulndexbeacon', 'laststatusmessage');
    $lasttime = (int)get_config('local_vulndexbeacon', 'laststatusupdated');

    if ($lasttime > 0) {
        $summary = 'HTTP ' . (int)$lastcode . ' @ ' . userdate($lasttime);
        if (!empty($lastmsg)) {
            $summary .= html_writer::empty_tag('br') . html_writer::tag('code', s($lastmsg));
        }
    } else {
        $summary = html_writer::tag('code', get_string('laststatus_never', 'local_vulndexbeacon'));
    }

    $settings->add(new admin_setting_heading(
        'local_vulndexbeacon/laststatus',
        get_string('laststatus', 'local_vulndexbeacon'),
        html_writer::tag('p', get_string('laststatus_desc', 'local_vulndexbeacon')) .
        html_writer::tag('p', $summary)
    ));

    $syncurl = new moodle_url('/local/vulndexbeacon/sync.php', ['sesskey' => sesskey()]);
    $buttonhtml = html_writer::link(
        $syncurl,
        get_string('manualsync_button', 'local_vulndexbeacon'),
        ['class' => 'btn btn-secondary']
    );

    $settings->add(new admin_setting_heading(
        'local_vulndexbeacon/manualsync',
        get_string('manualsync', 'local_vulndexbeacon'),
        html_writer::tag('p', get_string('manualsync_desc', 'local_vulndexbeacon')) . $buttonhtml
    ));

    $ADMIN->add('localplugins', $settings);
}
