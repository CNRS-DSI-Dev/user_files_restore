<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore;

use \OCA\User_Files_Restore\App\User_Files_Restore;
$app = new User_Files_Restore;
$c = $app->getContainer();

\OCP\Util::addscript($c->query('AppName'), 'restore');
\OCP\Util::addStyle($c->query('AppName'), 'restore');
