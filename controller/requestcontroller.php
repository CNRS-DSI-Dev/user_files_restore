<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\Controller;

use \OCP\AppFramework\APIController;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\IRequest;
use \OCP\IL10N;
use \OCA\User_Files_Restore\Db\RequestMapper;
use \OCA\User_Files_Restore\Db\Request;

class RequestController extends APIController
{

    protected $requestMapper;
    // protected $requestService;
    protected $userId;

    public function __construct($appName, IRequest $request, IL10N $l, RequestMapper $requestMapper, $userId)
    {
        parent::__construct($appName, $request, 'GET, POST');
        $this->l = $l;
        $this->requestMapper = $requestMapper;
        // $this->requestService = $requestService;
        $this->userId = $userId;
    }

    /**
     * Create a request
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param string $file File path
     * @param int $version Allowed values are stored in appconfig "versions"
     * @param string $filetype
     */
    public function create($file, $version, $filetype)
    {
        $collision = false;

        // test if there is(are) collision(s) between requests (one is contained into another)
        $existingRequests = $this->requestMapper->getRequests($this->userId, RequestMapper::STATUS_TODO);

        $toCancelRequests = array();
        $toKeepRequest = '';
        foreach($existingRequests as $existingRequest) {
            if ($existingRequest->getPath() == $file) {
                continue;
            }

            // a precedent request contains the new one ("version" does not count, here)
            if (strpos($file, $existingRequest->getPath()) === 0) {
                $collision = true;
                $toKeepRequest = $existingRequest->getPath();
                array_push($toCancelRequests, $file);

                // there can't be any "higher level" path, so we stop
                break;
            }
            // the current request contains a precedent request, so we plan the cancellation of this last one
            elseif (strpos($existingRequest->getPath(), $file) === 0) {
                $collision = true;
                $toKeepRequest = $file;
                array_push($toCancelRequests, $existingRequest->getPath());
            }
        }

        if ($collision) {
            $response = new JSONResponse();
            return array(
                'status' => 'collision_error',
                'data' => array(
                    'toKeep' => $toKeepRequest,
                    'toCancel' => json_encode($toCancelRequests),
                ),
            );
        }
        else {
            try {
                // $request = $this->requestMapper->saveRequest($this->userId, $file, (int)$version, $filetype);
            }
            catch(\Exception $e) {
                $response = new JSONResponse();
                return array(
                    'status' => 'error',
                    'data' => array(
                        'msg' => $e->getMessage(),
                    ),
                );
            }

            return array(
                'status' => 'success',
                'data' => array(
                    'msg' => 'Request saved',
                    'file' => $file,
                    'version' => (int)$version,
                ),
            );
        }
    }

    /**
     * Cancel a request
     * @NoAdminRequired
     * @NoCSRFRequired
     * @param int $id Request identifier
     */
    public function delete($id)
    {

       try {
            $request = $this->requestMapper->cancelRequest($this->userId, $id);
        }
        catch(\Exception $e) {
            $response = new JSONResponse();
            return array(
                'status' => 'error',
                'data' => array(
                    'msg' => $e->getMessage(),
                ),
            );
        }

        return array(
            'status' => 'success',
            'data' => array(
                'msg' => 'Request cancelled',
                'id' => $id,
            ),
        );
    }
}
