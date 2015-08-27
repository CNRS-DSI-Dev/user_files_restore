<?php

/**
 * ownCloud - User Files restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

use \OCA\User_Files_restore\App\User_Files_restore;
use \OCA\User_Files_restore\Command\RequestList;

$app = new User_Files_restore;
$c = $app->getContainer();
$requestMapper = $c->query('RequestMapper');

$application->add(new RequestList($requestMapper));
