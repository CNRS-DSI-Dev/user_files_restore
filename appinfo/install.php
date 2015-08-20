<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

$versions = OCP\Config::getAppValue('user_files_restore', 'versions', 'doSet');
if($versions === 'doSet') {
    OCP\Config::setAppValue('user_files_restore', 'versions', json_encode(array(1, 6, 15)));
}
