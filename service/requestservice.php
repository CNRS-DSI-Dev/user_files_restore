<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\Service;

use \OCA\User_Files_Restore\Db\RequestMapper;
use \OCA\User_Files_Restore\Db\Request;
use \OCP\IL10N;

class RequestService
{
    protected $requestMapper;
    protected $userId;
    protected $l;

    public function __construct(RequestMapper $requestMapper, $userId, IL10N $l)
    {
        $this->requestMapper = $requestMapper;
        $this->userId = $userId;
        $this->l = $l;
    }

    public function getTodos()
    {
        $todos = array();

        $requests = $this->requestMapper->getRequests($this->userId, RequestMapper::STATUS_TODO);

        foreach($requests as $request) {
            $todo = array();
            $todo['id'] = $request->getid();
            $todo['mime'] = $this->l->t($request->getFiletype());
            $todo['file'] = $request->getPath();
            $todo['version'] = $request->getVersion();
            array_push($todos, $todo);
        }

        return $todos;
    }

    public function getRunnings()
    {
        $runnings = array();

        $requests = $this->requestMapper->getRequests($this->userId, RequestMapper::STATUS_RUNNING);

        foreach($requests as $request) {
            $running = array();
            $running['mime'] = $this->l->t($request->getFiletype());
            $running['file'] = $request->getPath();
            array_push($runnings, $running);
        }

        return $runnings;
    }

    public function getDones()
    {
        $dones = array();

        $requests = $this->requestMapper->getRequests($this->userId, RequestMapper::STATUS_DONE);

        foreach($requests as $request) {
            $done = array();
            $done['mime'] = $this->l->t($request->getFiletype());
            $done['file'] = $request->getPath();
            $done['dateEnd'] = $request->getDateEnd();
            array_push($dones, $done);
        }

        return $dones;
    }
}
