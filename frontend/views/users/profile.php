<?php

use lajax\translatemanager\helpers\Language as Lx;

$this->title = Yii::t('titles', 'Edit Profile | Angdocs');

?>
<section class="edit-profile">
    <?= $this->render('/layouts/_menu'); ?>

    <div class="container-fluid">
        <div class="container">
            <div class="third-nav clearfix">
                <div class="col-xs-12">
                    <div class="small-title"><?= Lx::t('frontend', 'Edit Profile'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="container">
            <?= $this->render('/users/_form', [
                'userForm' => $userForm,
                'formId' => 'user-profile--form',
                'formClass' => 'edit-profile--form',
                'buttonTitle' => 'Update Profile',
                'buttonClass' => 'login-btn pull-right',
                'disabledFields' => $disabledFields
            ]); ?>
        </div>
    </div>
</section>

<?php

$profileJs = <<<JS
    $('input[type=text]').each(function() {
        $(this).addClass('trigger-label'); 
    });
JS;

$this->registerJS($profileJs, $this::POS_READY);