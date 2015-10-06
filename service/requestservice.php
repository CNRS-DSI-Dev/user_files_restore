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
    const MAX_LENGTH = 30;

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
            $todo['complete_filename'] = $request->getPath();
            $todo['file'] = $this->shortenPath($request->getPath());
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
            $running['complete_filename'] = $request->getPath();
            $running['file'] = $this->shortenPath($request->getPath());
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
            $done['complete_filename'] = $request->getPath();
            $done['file'] = $this->shortenPath($request->getPath());
            $done['dateEnd'] = $request->getDateEnd();
            $done['error'] = $request->getErrorCode();
            array_push($dones, $done);
        }

        return $dones;
    }

    public function getPrecedingNb()
    {
        $precedingNb = 0;

        try {
            $precedingNb = $this->requestMapper->getPrecedingRequests($this->userId);
        }
        catch (\Exception $e) {
            // nothing for now
        }

        return $precedingNb;
    }

    protected function shortenPath($path)
    {
        if (isset($path[self::MAX_LENGTH])) {
            $len = self::MAX_LENGTH - 3;
            $path = "..." . mb_substr($path, -$len);
        }

        return $path;
    }
}
