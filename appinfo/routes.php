<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore;

use \OCA\User_Files_Restore\App\User_Files_Restore;

$application = new User_Files_Restore();
$application->registerRoutes($this, array(
    'routes' => array(
        // REQUEST API
        array(
            'name' => 'request#get',
            'url' => '/api/1.0/request/{uid}',
            'verb' => 'GET',
        ),
        array(
            'name' => 'request#confirm',
            'url' => '/api/1.0/confirm/{request_id}',
            'verb' => 'GET',
        ),
        array(
            'name' => 'request#cancel',
            'url' => '/api/1.0/cancel/{request_id}',
            'verb' => 'GET',
        ),
        array(
            'name' => 'request#ask',
            'url' => '/api/1.0/request',
            'verb' => 'POST',
        ),
    ),
));
