<?php

use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Url;

if (empty($mobile)) {
    ?>
    <div class="cell data-cell id"><?= $item->primaryKey; ?></div>
    <div class="cell data-cell"><?= !empty($item->username) ? $item->username : '-'; ?></div>
    <div class="cell data-cell"><?= $isManager ? $item->company->name : $item->name; ?></div>
    <div class="cell data-cell"><?= $item->email; ?></div>
    <div class="cell data-cell"><?= $item->created_at; ?></div>
    <?php if ($isManager) { ?>
        <div class="cell data-cell"><?= $userTypeOptions[$item->type]; ?></div>
    <?php } ?>
    <div class="cell data-cell status <?= $userStatusCssOptions[$item->status]; ?>"><?= $userStatusOptions[$item->status]; ?></div>
    <div class="cell data-cell">
        <ul class="icons-certificates clearfix">
            <li>
                <a href="<?= Url::to("/users/update?id={$item->primaryKey}"); ?>">
                    <i class="fa fa-pencil-square-o edit-icon" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div>
<?php } else { ?>
    <a href="javascript: void(0);" class="coming-soon">
        <div class="col-xs-8">
            <div class="flex-it">
                <div class="certificate-id--title"><?= Lx::t('frontend', 'ID'); ?></div>
                <div class="certificate-id"><?= $item->primaryKey; ?></div>
            </div>
            <div class="certificate-name"><?= $item->username; ?></div>
            <div class="certificate-company"><?= $item->name; ?></div>
            <div class="certificate-company"><?= $item->email; ?></div>
            <div class="certificate-date"><?= $item->created_at; ?></div>
        </div>
        <div class="col-xs-4">
            <?php if ($isManager) { ?>
                <div class="certificate-status pull-right"><?= $userTypeOptions[$item->type]; ?></div>
                <br>
            <?php } ?>
            <div class="certificate-status pull-right <?= $userStatusCssOptions[$item->status]; ?>"><?= $userStatusOptions[$item->status]; ?></div>
        </div>
    </a>
    <?php
}