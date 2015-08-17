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

use \OCA\User_Files_Restore\Db\RequestMapper;
use \OCA\User_Files_Restore\Db\Request;

class PageController extends Controller {

    protected $requestMapper;
    protected $l;
    protected $userId;

    public function __construct($appName, IRequest $request, IL10N $l, RequestMapper $requestMapper, $userId)
    {
        parent::__construct($appName, $request, 'GET, POST');
        $this->l = $l;
        $this->requestMapper = $requestMapper;
        $this->userId = $userId;
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        // get all todo, running and done requests
        $todos = $runnings = $dones = array();

        // tests datas
        $todos = array(
            array(
                'mime' => 'file',
                'file' => '/boo/ba.sh',
            ),
            array(
                'mime' => 'dir',
                'file' => '/boo/projects',
            ),
            array(
                'mime' => 'file',
                'file' => '/boo/secret.pwd',
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
            ),
            array(
                'mime' => 'file',
                'file' => '/boo/bla.ck',
            ),
            array(
                'mime' => 'file',
                'file' => '/boo/whi.te',
            ),
            array(
                'mime' => 'dir',
                'file' => '/boo/colors',
            ),
            array(
                'mime' => 'dir',
                'file' => '/own.cloud',
            ),
            array(
                'mime' => 'file',
                'file' => '/path/to/the/megafile.long_extension',
            ),
        );

        return $this->render('main', array(
            'todos' => $todos,
            'runnings' => $runnings,
            'dones' => $dones,
        ));
    }
}
