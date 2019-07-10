<?php

use lajax\translatemanager\helpers\Language as Lx;

$this->title = Yii::t('titles', 'Update User | Angdocs');

?>

<section class="edit-profile">
    <?= $this->render('/layouts/_menu'); ?>

    <div class="container-fluid">
        <div class="container">
            <div class="third-nav clearfix">
                <div class="col-xs-12">
                    <div class="small-title"><?= Lx::t('frontend', 'Update User'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-home">
        <div class="container">
            <?= $this->render('/users/_form', [
                'userForm' => $userForm,
                'formId' => 'update-user--form',
                'formClass' => 'edit-profile--form',
                'buttonTitle' => 'Update User',
                'buttonClass' => 'login-btn pull-right margin-top-20'
            ]); ?>
        </div>
    </div>
</section>