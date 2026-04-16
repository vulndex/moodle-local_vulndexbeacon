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
namespace local_vulndexbeacon\util;

use Throwable;

/**
 * The payload class provides methods to gather and build comprehensive information
 * about the Moodle instance, including system and plugin details.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class payload {
    /**
     * Builds and returns an array containing system and site information.
     *
     * The method gathers information about the node ID, Moodle version, PHP version,
     * operating system, site URLs, and plugin inventory. It compiles this data into
     * a structured array for further use.
     *
     * @return array An associative array containing:
     *               - 'node_id': Node identifier from plugin settings.
     *               - 'moodle_version': Details about the Moodle version including release, branch, and build.
     *               - 'php_version': The current PHP version.
     *               - 'os': Information about the operating system, including name and version.
     *               - 'site_url': The main site URL.
     *               - 'home_url': The home URL of the site.
     *               - 'version': The version identifier of the returned data structure.
     *               - 'inventory': An array containing:
     *                   - 'plugins': Installed plugins information.
     *                   - 'themes': Installed themes information.
     * @throws \dml_exception
     */
    public static function build(): array {

        global $CFG;

        $node = (string)get_config('local_vulndexbeacon', 'nodeid');

        $moodleversion = [
            'release' => $CFG->release ?? '',
            'branch' => $CFG->branch ?? '',
            'build' => $CFG->version ?? 0,
        ];

        $phpversion = PHP_VERSION;

        $osinfo = [];
        if (function_exists('php_uname')) {
            try {
                $osinfo = [
                    'name' => php_uname(),
                    'version' => php_uname('r'),
                ];
            } catch (Throwable $e) {
                $osinfo = [
                    'name' => null,
                    'version' => null,

                ];
            }
        } else {
            $osinfo = [
                'name' => null,
                'version' => null,
            ];
        }

        $siteurl = $CFG->wwwroot;
        $homeurl = $CFG->wwwroot;

        $plugins = self::get_plugins();
        $themes = self::get_themes();

        return [
            'node_id' => $node,
            'moodle_version' => $moodleversion,
            'php_version' => $phpversion,
            'os' => $osinfo,
            'site_url' => $siteurl,
            'home_url' => $homeurl,
            'version' => 1,
            'inventory' => [
                'plugins' => $plugins,
                'themes' => $themes,
            ],
        ];
    }

    /**
     * Retrieves a list of non-standard plugins with their type, name, version, release, component,
     * and enabled status.
     *
     * This method filters out standard plugins and checks if each plugin is enabled by leveraging
     * the `is_plugin_enabled` function if it exists. If not, it falls back to the configuration settings
     * to determine the status.
     *
     * @return array An array of associative arrays representing plugins. Each associative array contains:
     *         - 'type' (string): The type of the plugin.
     *         - 'name' (string): The name of the plugin.
     *         - 'version' (string|null): The installed database version of the plugin.
     *         - 'release' (string|null): The release version of the plugin.
     *         - 'component' (string): The component name of the plugin.
     *         - 'is_enabled' (bool|null): Whether the plugin is enabled (true), disabled (false), or
     *           null if the status could not be determined.
     * @throws \dml_exception
     */
    private static function get_plugins(): array {
        $pluginman = \core_plugin_manager::instance();
        $all = $pluginman->get_plugins();

        $result = [];

        foreach ($all as $type => $typedata) {
            foreach ($typedata as $plugin) {
                if ($plugin->is_standard()) {
                    continue;
                }

                $pluginenabled = false;
                if (function_exists('is_plugin_enabled')) {
                    $pluginenabled = is_plugin_enabled($plugin->component);
                } else {
                    $plugindisabled = get_config('core', 'disabledplugins');
                    try {
                        $disabled = $plugindisabled ? json_decode($plugindisabled, true, 512, JSON_THROW_ON_ERROR) : [];
                        $pluginenabled = !isset($disabled[$plugin->component]);
                    } catch (\JsonException $e) {
                        $pluginenabled = null;
                    }
                }

                $result[] = [
                    'type' => $type,
                    'name' => $plugin->name,
                    'version' => $plugin->versiondb ?? null,
                    'release' => $plugin->release ?? null,
                    'component' => $plugin->component ?? ($type . '_' . $plugin->name),
                    'is_enabled' => $pluginenabled,
                ];
            }
        }

        return $result;
    }

    /**
     * Retrieves a list of non-standard themes with their name, version, release, component,
     * and enabled status.
     *
     * This method filters out standard themes and determines the enabled status of each theme
     * by using the `is_plugin_enabled` function if available. If the function is not present,
     * it falls back to checking the configuration settings.
     *
     * @return array An array of associative arrays representing themes. Each associative array contains:
     *         - 'name' (string): The name of the theme.
     *         - 'version' (string|null): The installed database version of the theme.
     *         - 'release' (string|null): The release version of the theme.
     *         - 'component' (string): The component name of the theme.
     *         - 'is_enabled' (bool): Whether the theme is enabled (true) or disabled (false).
     */
    private static function get_themes(): array {
        $pluginman = \core_plugin_manager::instance();
        $themes = $pluginman->get_plugins_of_type('theme');

        $result = [];

        foreach ($themes as $theme) {
            if ($theme->is_standard()) {
                continue;
            }

            $pluginenabled = false;
            if (function_exists('is_plugin_enabled')) {
                $pluginenabled = is_plugin_enabled($theme->component);
            } else {
                $plugindisabled = get_config('core', 'disabledplugins');
                $disabled = $plugindisabled ? json_decode($plugindisabled, true) : [];
                $pluginenabled = !isset($disabled[$theme->component]);
            }

            $result[] = [
                'name' => $theme->name,
                'version' => $theme->versiondb ?? null,
                'release' => $theme->release ?? null,
                'component' => $theme->component,
                'is_enabled' => $pluginenabled,
            ];
        }

        return $result;
    }
}
