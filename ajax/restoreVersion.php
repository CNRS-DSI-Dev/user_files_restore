<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

OCP\JSON::checkAppEnabled('user_files_restore');
OCP\JSON::callCheck();

$file = $_GET['file'];
$revision=(int)$_GET['revision'];

// TODO: verify all parameters are here
// TODO: verify parameters are correct

// if(OCA\Files_Versions\Storage::rollback( $file, $revision )) {
	OCP\JSON::success(array("data" => array( "revision" => $revision, "file" => $file )));
    OCA\User_Files_Restore\Requests::createRequest(\OCP\User::getUser(), $file, $revision);
// }else{
// 	$l = OC_L10N::get('files_versions');
// 	OCP\JSON::error(array("data" => array( "message" => $l->t("Could not revert: %s", array($file) ))));
// }
