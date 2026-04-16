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
namespace local_vulndexbeacon\privacy;

use core_privacy\local\metadata\collection;

/**
 * Defines the privacy metadata and data request handling for the provider.
 *
 * This class implements the data privacy metadata provider and data request handling
 * to define and manage any external data shared or processed.
 *
 * Implements methods to:
 * - Provide metadata about external data processing.
 * - Handle requests for data from the provider.
 *
 * @package    local_vulndexbeacon
 * @copyright  2026 Oliver Kerzinger <oliver@kerzinger.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\data_provider {
    /**
     * Adds external location link details to the provided metadata collection.
     *
     * @param collection $collection The metadata collection to which the link details will be added.
     * @return collection The updated metadata collection containing the new external location link.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link(
            'vulndexbeacon',
            [],
            'privacy:metadata'
        );

        return $collection;
    }
}
