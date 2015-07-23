<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore;

\OC::$CLASSPATH['\OCA\User_Files_Restore\Requests'] = 'user_files_restore/lib/requests.php';

// use \OCA\User_Files_Restore\App\User_Files_Restore;
// $app = new User_Files_Restore;
// $c = $app->getContainer();

\OCP\Util::addscript('user_files_restore', 'restore');
\OCP\Util::addStyle('user_files_restore', 'restore');
