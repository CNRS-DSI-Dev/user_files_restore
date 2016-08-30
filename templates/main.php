<?php

/**
 * ownCloud - Dashboard
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

\OCP\Util::addScript('user_files_restore', 'app');
\OCP\Util::addStyle('user_files_restore', 'restore');

?>

<div id="user_files_restore">
    <?php p($l->t('Restoration requests')); ?>
</div>

<div id="container" class="user_files_restore">

<div id="infos" class="dataBlock">
    <h2><?php print_unescaped($l->t('Prior information')); ?></h2>
    <div id="infos__detail">
        <ul>
            <li><?php print_unescaped($l->t("If you want to restore a mistakenly deleted files, have a look on trashbin, it's simpler and quicker.")); ?></li>
            <li class="freeCreate"><?php print_unescaped($l->t("You also can request a restoration on the application default page.")); ?></li>
            <li class="running"><?php print_unescaped($l->t("<span class=\"to_highlight\">Restoration requests are processed as background jobs. You can find on this page the state and results of your requests.</span><br /><b>Important</b>: restored files will overide (ie delete and replace) existing files with same path and name.")); ?></li>
            <li id="crypto"><?php print_unescaped($l->t("If you have been targeted by a crypto-virus, please ask your CSSI / RSSI before creating any restoration request.")); ?></li>
        </ul>
    </div>
</div>

<div id="freeCreate" class="dataBlock clearfix">
    <p class="header"><?php p($l->t('Request a complete restoration')); ?> <img src="<?php print_unescaped(image_path('user_files_restore', 'help.png')) ?>" /></p>
    <div>
        <select name="version">
            <?php foreach ($_['versions'] as $version): ?>
            <option value="<?php p($version['version']); ?>"><?php p($version['label']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="button" value="<?php p($l->t("Validate")); ?>">
    </div>
</div>

<div id="todo" class="dataBlock clearfix">
    <p class="header"><?php p($l->t('To be processed')); ?></p>
    <?php foreach($_['todos'] as $todo): ?>
    <p id="<?php p($todo['id']); ?>">
        <span title="<?php p($todo['complete_filename']); ?>"><?php p($todo['file']); ?></span>
        <span class="bonus">(<?php p($todo['mime']); ?> ; </span>
        <span class="bonus"><?php p($l->t('d - ') . $todo['version']); ?>)</span>
        <span class="cancel bonus" data-id="<?php p($todo['id']); ?>" data-version="<?php p($todo['version']); ?>"><?php p($l->t('Cancel')); ?></span>
    </p>
    <?php endforeach; ?>
</div>

<div id="running" class="dataBlock">
    <p class="header">
        <?php p($l->t('Running')); ?>
        <img src="<?php print_unescaped(image_path('user_files_restore', 'help.png')) ?>" />
        <?php if ($_['precedingRequests'] > 0): ?>
        <span><?php p($l->t("You have %d request(s) before yours to be processed.", $_['precedingRequests'])) ;?></span>
        <?php endif ?>
    </p>
    <?php foreach($_['runnings'] as $running): ?>
    <p>
        <span class="bonus">(<?php p($running['mime']); ?>)</span>
        <span title="<?php p($running['complete_filename']); ?>"><?php p($running['file']); ?></span>
    </p>
    <?php endforeach; ?>
</div>

<div id="done" class="dataBlock">
    <p class="header"><?php p($l->t('Done')); ?></p>
    <?php
        foreach($_['dones'] as $done):
            $classDone = "";
            if (!empty($done['error'])) {
                $classDone = "error";
            }
    ?>
    <p class="<?php  echo $classDone ?>">
        <span class="bonus">(<?php p($done['mime']); ?>)</span>
        <span title="<?php p($done['complete_filename']); ?>"><?php p($done['file']); ?></span>
        <span class="errorback">
            <?php if (!empty($done['error'])): ?>
            <img src="<?php print_unescaped(image_path('user_files_restore', 'exclamation.png')) ?>" />
            <span  title="<?php print_unescaped($done['error']); ?>">Voir plus</span>
            <?php else: ?>
            &nbsp;
            <?php endif; ?>
            </span>
        <span class="date bonus"><?php p($done['dateEnd']); ?></span>
    </p>
    <?php endforeach; ?>
</div>

</div>

<div id="footer">
    <p>Icons provided by <a href="http://glyphicons.com/">GLYPHICONS.com</a>, released under <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported (CC BY 3.0)</a></p>
</div>
