<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\App;

use \OCP\AppFramework\App;
use \OCA\User_Files_Restore\Controller\PageController;
use \OCA\User_Files_Restore\Controller\RequestController;
use \OCA\User_Files_Restore\Service\RequestService;
use \OCA\User_Files_Restore\Db\RequestMapper;

class User_Files_Restore extends App {

    /**
     * Define your dependencies in here
     */
    public function __construct(array $urlParams=array()){
        parent::__construct('user_files_restore', $urlParams);

        $container = $this->getContainer();

        $container->registerService('L10N', function($c) {
            return $c->query('ServerContainer')->getL10N($c->query('AppName'));
        });
    }
}
