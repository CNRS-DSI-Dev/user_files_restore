<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\Controller;

use \OCP\AppFramework\Controller;
use \OCP\IRequest;
use \OCP\IL10N;

use \OCA\User_Files_Restore\Service\RequestService;
use \OCA\User_Files_Restore\Db\Request;

class PageController extends Controller {

    protected $requestService;
    protected $l;
    protected $userId;

    public function __construct($appName, IRequest $request, IL10N $l, RequestService $requestService, $userId)
    {
        parent::__construct($appName, $request, 'GET, POST');
        $this->l = $l;
        $this->requestService = $requestService;
        $this->userId = $userId;
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        // get all todo, running and done requests
        $todos = $runnings = $dones = array();

        // TEST DATAS
        // list($todos, $runnings, $dones) = $this->getTestDatas();

        $todos = $this->requestService->getTodos();
        $runnings = $this->requestService->getRunnings();
        $dones = $this->requestService->getDones();

        return $this->render('main', array(
            'todos' => $todos,
            'runnings' => $runnings,
            'dones' => $dones,
        ));
    }

    /**
     * Returns fake requests of each status
     * @return [type] [description]
     */
    private function getTestDatas() {
        // tests datas
        $todos = array(
            array(
                'id' => 1,
                'mime' => 'file',
                'file' => '/boo/ba.sh',
                'version' => 1,
            ),
            array(
                'id' => 2,
                'mime' => 'dir',
                'file' => '/boo/projects',
                'version' => 15,
            ),
            array(
                'id' => 3,
                'mime' => 'file',
                'file' => '/boo/secret.pwd',
                'version' => 30,
            ),
        );

        $runnings = array(
            array(
                'mime' => 'dir',
                'file' => '/gan_ainm',
            ),
        );

        $dones = array(
            array(
                'mime' => 'file',
                'file' => '/boo/foo.ls',
                'dateEnd' => '2015-08-18 15:00:00',
            ),
            array(
                'mime' => 'file',
                'file' => '/boo/bla.ck',
                'dateEnd' => '2015-08-18 15:00:00',
            ),
            array(
                'mime' => 'file',
                'file' => '/boo/whi.te',
                'dateEnd' => '2015-08-18 15:00:00',
            ),
            array(
                'mime' => 'dir',
                'file' => '/boo/colors',
                'dateEnd' => '2015-08-18 15:00:00',
            ),
            array(
                'mime' => 'dir',
                'file' => '/own.cloud',
                'dateEnd' => '2015-08-18 15:00:00',
            ),
            array(
                'mime' => 'file',
                'file' => '/path/to/the/megafile.long_extension',
                'dateEnd' => '2015-08-18 15:00:00',
                'dateEnd' => '2015-08-18 15:00:00',
            ),
        );

        return array($todos, $runnings, $dones);
    }
}
