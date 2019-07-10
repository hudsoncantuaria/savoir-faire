<?php

use yii\helpers\Url;

$action = Yii::$app->controller->action->id;

?>


<div class="container-fluid bg-gray visible-xs">
    <div class="container nopadding">
        <div class="third-nav clearfix">
            <div class="col-xs-12 nopadding">
                <div class="flex-between align-items--center small-title--group">
                    <div class="small-title certificate-number">Certificate <?= $certificate->primaryKey; ?></div>
                    <a class="back-to-list--btn" href="<?= Url::to("/site/index"); ?>">back<i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid nopadding sub-wrapper visible-xs">
    <div class="col-xs-12 nopadding">
        <ul class="small-menu--certificates clearfix">
            <li <?= $action == 'history' ? 'class="active"' : ''; ?>>
                <a href="<?= Url::to("/certificates/history?id={$certificate->primaryKey}"); ?>">history</a>
            </li>
            <li <?= $action == 'documents' ? 'class="active"' : ''; ?>>
                <a href="<?= Url::to("/certificates/documents?id={$certificate->primaryKey}"); ?>">documents</a>
            </li>
        </ul>
    </div>
</div>
