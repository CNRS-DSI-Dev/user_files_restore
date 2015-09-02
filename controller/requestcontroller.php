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

use \OCA\User_Files_Restore\lib\Helper;

class RequestController extends APIController
{

    protected $requestMapper;
    protected $userId;

    public function __construct($appName, IRequest $request, IL10N $l, RequestMapper $requestMapper, $userId)
    {
        parent::__construct($appName, $request, 'GET, POST');
        $this->l = $l;
        $this->requestMapper = $requestMapper;
        $this->userId = $userId;
    }

    /**
     * Create a request
     * @NoAdminRequired
     * @param string $file File path
     * @param int $version Allowed values are stored in appconfig "versions"
     * @param string $filetype
     */
    public function create($file, $version, $filetype)
    {
        $currentRequest = new Request();
        $currentRequest->setUid($this->userId);
        $currentRequest->setPath($file);
        $currentRequest->setVersion($version);
        $currentRequest->setFiletype($filetype);

        $collision = false;

        // test if there is(are) collision(s) between requests (one is contained into another)
        $existingRequests = $this->requestMapper->getRequests($this->userId, RequestMapper::STATUS_TODO);
        $toSortRequests = array();
        $toCancelRequests = array();

        foreach($existingRequests as $existingRequest) {
            // if versions are identical
            if ($existingRequest->getVersion() === $currentRequest->getVersion()) {
                // if current request contains existing request
                if (strpos($existingRequest->getPath(), $currentRequest->getPath()) === 0) {
                    array_push($toCancelRequests,  $existingRequest);
                    array_push($toSortRequests, $currentRequest);
                    $collision = true;
                }
                // if current request same as existing request
                elseif ($currentRequest->getPath() == $existingRequest->getPath()) {
                    // nothing to do, exiting foreach
                    $collision = true;
                    break;
                }
                // if existing request contains current request
                elseif (strpos($currentRequest->getPath(), $existingRequest->getPath()) === 0) {
                    array_push($toSortRequests, $existingRequest);
                }
            }
            // versions are different
            else {
                // if current request contains existing request
                if (strpos($existingRequest->getPath(), $currentRequest->getPath()) === 0) {
                    array_push($toSortRequests, $currentRequest);
                    array_push($toSortRequests, $existingRequest);
                    $collision = true;
                }
                // if current request same as existing request
                elseif ($currentRequest->getPath() == $existingRequest->getPath()) {
                    array_push($toCancelRequests, $existingRequest);
                    array_push($toSortRequests, $currentRequest);
                    $collision = true;
                }
                // if existing request contains current request
                elseif (strpos($currentRequest->getPath(), $existingRequest->getPath()) === 0) {
                    array_push($toSortRequests, $existingRequest);
                    array_push($toSortRequests, $currentRequest);
                    $collision = true;
                }
            }
        }

        // collision
        // process toCancelRequest and toSortRequests
        if ($collision) {
            if (!empty($toSortRequests)) {
                $response = new JSONResponse();

                // sort on path length, shortest first
                usort($toSortRequests, function($a, $b) {
                    if (strlen($a->getPath()) == strlen($b->getPath())) {
                        return 0;
                    }

                    if (strlen($a->getPath()) < strlen($b->getPath())) {
                        return -1;
                    }

                    return 1;
                });

                // update the dateRequest field (creation date) accordingly
                // Obviously, current request will be created, not updated
                $currentDate = time();
                $inc = 1;
                foreach($toSortRequests as $request) {
                    $requestId = $request->getId();
                    if (!empty($requestId)) {
                        $request->setDateRequest(date('Y-m-d H:i:s', $currentDate + $inc));
                        try {
                            $this->requestMapper->update($request);
                        }
                        catch (\Exception $e) {
                            $response = new JSONResponse();
                            return array(
                                'status' => 'error',
                                'data' => array(
                                    'msg' => $e->getMessage(),
                                ),
                            );
                        }
                    }
                    else {
                        try {
                            $this->requestMapper->saveRequest(
                                $this->userId,
                                $request->getPath(),
                                $request->getVersion(),
                                $request->getFiletype(),
                                $currentDate + $inc
                            );
                        }
                        catch (\Exception $e) {
                            $response = new JSONResponse();
                            return array(
                                'status' => 'error',
                                'data' => array(
                                    'msg' => $e->getMessage(),
                                ),
                            );
                        }
                    }

                    $inc++;
                }

                // get only paths (to display to user)
                $toSortRequestsPath = array_map(function($elt) {
                    return $elt->getPath();
                }, $toSortRequests);
                $toCancelRequestsPath = array_map(function($elt) {
                    return $elt->getPath();
                }, $toCancelRequests);

                // cancel useless requests
                foreach($toCancelRequests as $request) {
                    $this->requestMapper->delete($request);
                }

                // Probably do not want to display what we keep...
                if (count($toCancelRequestsPath) > 0) {
                    return array(
                        'status' => 'collision_error',
                        'data' => array(
                            'toKeep' => json_encode($toSortRequestsPath),
                            'toCancel' => json_encode($toCancelRequestsPath),
                        ),
                    );
                }
                else {
                    return array(
                        'status' => 'success',
                        'data' => array(
                            'msg' => $this->l->t('Request successfully created'),
                            'file' => $currentRequest->getPath(),
                            'version' => (int)$currentRequest->getVersion(),
                        ),
                    );
                }
            }

            return array(
                'status' => 'error',
                'data' => array(
                    'msg' => $this->l->t('Your request is already taken in account by an older one.'),
                ),
            );
        }

        // no collision
        // TODO: optimization: extract chars before first / on ER and CR, then compare
        // insert current request
        if (!$collision) {
            try {
                $request = $this->requestMapper->saveRequest($this->userId, $file, (int)$version, $filetype);
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
                    'msg' => $this->l->t('Request successfully created'),
                    'file' => $currentRequest->getPath(),
                    'version' => (int)$currentRequest->getVersion(),
                ),
            );
        }
    }

    /**
     * Cancel a request
     * @NoAdminRequired
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
                'msg' => $this->l->t('Request cancelled'),
                'id' => $id,
            ),
        );
    }

    /**
     * Returns allowed "versions"
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function versions()
    {
       try {
            $versions = Helper::getVersions();
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
                'msg' => $this->l->t('Allowed versions'),
                'versions' => json_encode($versions),
            ),
        );
    }
}
