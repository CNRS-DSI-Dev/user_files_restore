<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore;

use \OCA\User_Files_Restore\App\User_Files_Restore;
$app = new User_Files_Restore;
$c = $app->getContainer();

/**
 * add navigation
 */
\OCP\App::addNavigationEntry(array(
    'id' => 'user_files_restore',
    'order' => 10,
    'href' => \OCP\Util::linkToRoute('user_files_restore.page.index'),
    'icon' => \OCP\Util::imagePath($c->query('AppName'), 'restoreApp.svg'),
    'name' => $c->query('L10N')->t('Restore')
));

/**
 * Load js and overlay icon
 */
$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() use ($c){
    \OCP\Util::addscript($c->query('AppName'), 'app');
    \OCP\Util::addscript($c->query('AppName'), 'userfilesrestoretabview');
    \OCP\Util::addscript($c->query('AppName'), 'filesplugin');
    \OCP\Util::addStyle($c->query('AppName'), 'restore');
});

