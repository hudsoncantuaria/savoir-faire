<?php

use lajax\translatemanager\helpers\Language as Lx;

?>

<div class="coming-soon--mask">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <div class="coming-soon--block">
                    <i class="fa fa-close close-coming--btn"></i>
                    <img src="<?= Yii::getAlias('@theme'); ?>/images/logo.png" class="img-responsive center-block" alt="Camdocs">
                    <p><?= Lx::t('frontend', 'Coming Soon'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>