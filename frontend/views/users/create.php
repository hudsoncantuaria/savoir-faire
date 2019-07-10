<?php

use lajax\translatemanager\helpers\Language as Lx;

$this->title = Yii::t('titles', 'Create User | Angdocs');

?>

<section class="edit-profile">
    <?= $this->render('/layouts/_menu'); ?>

    <div class="container-fluid">
        <div class="container">
            <div class="third-nav clearfix">
                <div class="col-xs-12">
                    <div class="small-title"><?= Lx::t('frontend', 'Create User'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="container">
            <?= $this->render('/users/_form', [
                'userForm' => $userForm,
                'formId' => 'new-user--form',
                'formClass' => 'edit-profile--form',
                'buttonTitle' => 'Create User',
                'buttonClass' => 'login-btn pull-right'
            ]); ?>
        </div>
    </div>
</section>
