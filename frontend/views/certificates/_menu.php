<?php

use yii\helpers\Url;

$action = Yii::$app->controller->action->id;

?>

<div class="container-fluid">
    <div class="container">
        <div class="third-nav align-nav--mobile clearfix <?= in_array($action, ['history', 'documents']) ? 'hidden-xs' : ''; ?>">
            <div class="col-xs-12 <?= in_array($action, ['history', 'documents']) ? 'col-sm-12' : ''; ?>">
                <div class="flex-it align-items--center small-title--group">
                    <?php if ($action != 'create' && !empty($certificate)) { ?>
                        <div class="small-title certificate-number">Certificate <?= $certificate->primaryKey; ?></div>
                        <ul class="small-menu--certificates clearfix">
                            <li <?= $action == 'history' ? 'class="active"' : ''; ?>>
                                <a href="<?= Url::to("/certificates/history?id={$certificate->primaryKey}"); ?>">history</a>
                            </li>
                            <li <?= $action == 'documents' ? 'class="active"' : ''; ?>>
                                <a href="<?= Url::to("/certificates/documents?id={$certificate->primaryKey}"); ?>">documents</a>
                            </li>
                        </ul>
                    <?php } else { ?>
                        <div class="small-title certificate-number">New Certificate</div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-xs-12 <?= in_array($action, ['history', 'documents']) ? 'col-sm-12' : ''; ?>">
                <a class="back-to-list--btn" href="<?= Url::to("/site/index"); ?>">back to list<i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</div>