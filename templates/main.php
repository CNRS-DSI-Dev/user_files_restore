<?php

/**
 * ownCloud - Dashboard
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2014 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

\OCP\Util::addStyle('user_files_restore', 'restore');

?>

<div id="user_files_restore">
    <?php p($l->t('Restoration requests')); ?>
</div>

<div id="container">

<div id="freeCreate" class="dataBlock">
    <p class="header"><?php p($l->t('Request a restoration')); ?> <img src="<?php print_unescaped(image_path('user_files_restore', 'help.png')) ?>" title="<?php print_unescaped($l->t("You also can request a restoration on the application default page.")); ?>" /></p>
    <div>
        <input type="text" name="filename" placeholder="<?php p($l->t('Enter a directory name or a file name you want to restore')) ?>" />
        <select name="version">
            <?php foreach ($_['versions'] as $version): ?>
            <option value="<?php p($version['version']); ?>"><?php p($version['label']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="button" value="<?php p($l->t("Validate")); ?>">
    </div>
</div>

<div id="todo" class="dataBlock">
    <p class="header"><?php p($l->t('To be processed')); ?></p>
    <?php foreach($_['todos'] as $todo): ?>
    <p id="<?php p($todo['id']); ?>">
        <span title="<?php p($todo['complete_filename']); ?>"><?php p($todo['file']); ?><span>
        <span class="bonus">(<?php p($todo['mime']); ?> ; </span>
        <span class="bonus"><?php p($l->t('d - ') . $todo['version']); ?>)</span>
        <span class="cancel bonus" data-id="<?php p($todo['id']); ?>" data-version="<?php p($todo['version']); ?>"><?php p($l->t('Cancel')); ?></span>
    </p>
    <?php endforeach; ?>
</div>

<div id="running" class="dataBlock">
    <p class="header">
        <?php p($l->t('Running')); ?>
        <img src="<?php print_unescaped(image_path('user_files_restore', 'help.png')) ?>" title='<?php print_unescaped($l->t('Requests are not processed in real time. Please refer to the documentation.')); ?>' />
    </p>
    <?php foreach($_['runnings'] as $running): ?>
    <p>
        <span class="bonus">(<?php p($running['mime']); ?>)</span>
        <span title="<?php p($running['complete_filename']); ?>"><?php p($running['file']); ?></span>
    </p>
    <?php endforeach; ?>
</div>

<div id="done" class="dataBlock">
    <p class="header"><?php p($l->t('Done')); ?></span></p>
    <?php foreach($_['dones'] as $done): ?>
    <p>
        <span class="bonus">(<?php p($done['mime']); ?>)</span>
        <span title="<?php p($done['complete_filename']); ?>"><?php p($done['file']); ?></span>
        <span class="errorback">
            <?php if (!empty($done['error'])): ?>
            <img src="<?php print_unescaped(image_path('user_files_restore', 'exclamation.png')) ?>" title="<?php print_unescaped($done['error']); ?>" /></span>
            <?php else: ?>
            &nbsp;</span>
            <?php endif; ?>
        <span class="date bonus"><?php p($done['dateEnd']); ?></span>
    </p>
    <?php endforeach; ?>
</div>

</div>

<div id="footer">
    <p>Icons provided by <a href="http://glyphicons.com/">GLYPHICONS.com</a>, released under <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported (CC BY 3.0)</a></p>
</div>
