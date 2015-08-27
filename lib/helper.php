<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\lib;

class Helper
{
    /**
     * Returns the list of allowed "versions"
     * @return array
     */
    public static function getVersions()
    {
        $versions = array();

        $result = json_decode(\OCP\Config::getAppValue('user_files_restore', 'versions', ''));

        if (is_array($result)) {
            foreach($result as $item) {
                array_push($versions, $item);
            }
        }

        return $versions;
    }
}
