<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore;

class Requests {

    const STATUS_TODO = 1;
    const STATUS_RUNNING = 2;
    const STATUS_DONE = 3;

    /**
     * Create a restore request in database
     * @param string  $uid     User identifiant
     * @param string  $path    relative path to the file or dir to restore (relative to user's home dir)
     * @param integer $version 1, 15 or 30
     */
    public static function createRequest($uid, $path, $version)
    {
        // TODO: normalize path
        // TODO: verify allowed version

        $sql = "INSERT INTO *PREFIX*user_files_restore VALUES ('', :uid, NOW(), :path, :version, :status, NULL, NULL)";
        $st = \OCP\DB::prepare($sql);
        $st->execute(array(
            ':uid' => $uid,
            ':path' => $path,
            ':version' => $version,
            ':status' => self::STATUS_TODO,
        ));
    }
}
