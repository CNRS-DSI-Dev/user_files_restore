<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\Db;

use \OCP\AppFramework\Db\Entity;

class Request extends Entity {
    protected $uid;
    protected $dateRequest;
    protected $path;
    protected $version;
    protected $status;
    protected $dateEnd;
    protected $errorCode;

    public function __construct() {
        $this->addType('version', 'integer');
        $this->addType('status', 'integer');
    }
}
