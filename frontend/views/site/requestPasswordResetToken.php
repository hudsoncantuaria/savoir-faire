<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use common\helpers\CustomHelper;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('titles', 'Request Password Reset | Angdocs');

?>


<section class="register bg pr">
    <div class="overlay-wrapper">
        <div class="overlay-left"></div>
        <div class="overlay-right"></div>
    </div>
    <div class="register-modal--wrapper password-modal--wrapper">
        <div>
            <figure class="logo-wrapper--register">
                <!--<img src="<?= Yii::getAlias('@theme/images/logo.jpg'); ?>" title="Angdocs" alt="Angdocs">-->
                <img class="img-responsive" src="<?= Yii::getAlias('@theme'); ?>/images/logo-login.png" title="" alt="">
            </figure>
            <h1 class="login-sentence"><?= Lx::t('frontend', 'Request Password Reset'); ?></h1>
            <?php
            if (count(Yii::$app->session->getAllFlashes()) > 0) {
                ?>
                <div class="generate-alerts margin-top-20">
                    <?= CustomHelper::generateAlerts(); ?>
                </div>
                <?php
            }

            $form = ActiveForm::begin();
            ?>
            <div class="certificate-box--column">
            <?php
             echo $form->field($model, 'email', [
                'options' => [
                    'class' => 'input-wrapper',
                    'tag' => 'div',
                ],
                'template' => '{label}{input}{error}',
            ])->textInput(['autofocus' => true]);
            ?>
            </div>
            <div class="clearfix">
                <?php
                echo Html::submitButton('<span>' . Lx::t('frontend', 'Send') . '</span>
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>', [
                    'class' => 'login-btn'
                ]);
                ?>
            </div>
            <?php
            

            ActiveForm::end();
            ?>
            <div class="register-request on-page">
                <a href="<?= Url::to("/site/login"); ?>" class="request-access"><?= Lx::t('frontend', 'login access'); ?></a>
            </div>
        </div>
    </div>
</section>
