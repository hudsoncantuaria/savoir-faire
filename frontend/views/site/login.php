<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $loginForm \frontend\models\LoginForm */

use common\helpers\CustomHelper;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('titles', 'Login | Angdocs');

?>

<section class="login bg pr">
    <div class="overlay-wrapper">
        <div class="overlay-left"></div>
        <div class="overlay-right"></div>
    </div>
    <div class="login-modal--wrapper">
        <div>
            <div class="login-box green">
                <div class="svg-wrapper logo-wrapper align-center">
                    <img class="img-responsive" src="<?= Yii::getAlias('@theme'); ?>/images/logo-login.png" title="" alt="">
                </div>
                <div class="login title">
                    <?= Lx::t('frontend', 'Agents for National'); ?><br>
                    <b><?= Lx::t('frontend', 'Shippers Council of Angola'); ?></b>
                </div>
                <div class="flex-center">
                    <a class="email-login hidden-xs" href="mailto:angdocs@angdocs.be"><?= Lx::t('frontend', 'angdocs@angdocs.be'); ?></a>
                </div>
            </div>
        </div>
        <div>
            <div class="login-box white">
                <h1 class="login-sentence"><?= Lx::t('frontend', 'Please enter your username and password to login'); ?></h1>
                <?php if (count(Yii::$app->session->getAllFlashes()) > 0) { ?>
                    <div class="generate-alerts padding-bottom-20">
                        <?= CustomHelper::generateAlerts(); ?>
                    </div>
                <?php
                }
                $form = ActiveForm::begin(['id' => 'login-form']);
                ?>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                        <?php
                            echo $form->field($loginForm, 'username', [
                                'options' => [
                                    'class' => 'input-wrapper',
                                    'tag' => 'div',
                                ],
                                'template' => '{label}{input}{error}',
                            ])->textInput(['autofocus' => true, 'class' => '', 'required' => '']);
                        ?>
                    </div>
                    <div class="col-xs-12 col-sm-6 certificate-box--column">
                        <?php
                            echo $form->field($loginForm, 'password', [
                                'options' => [
                                    'class' => 'input-wrapper',
                                    'tag' => 'div',
                                ],
                                'template' => '{label}{input}{error}',
                            ])->passwordInput(['autofocus' => true, 'class' => '', 'required' => '']);
                        ?>
                    </div>
                    <div class="col-xs-12 certificate-box--column clearfix">
                        <?php
                            echo Html::submitButton('<span>login</span>
                                <i class="fa fa-arrow-right" aria-hidden="true"></i>', [
                                'class' => 'login-btn',
                                'name' => 'login-button'
                            ]);
                        ?>
                    </div>
                <?php
                    ActiveForm::end();
                ?>
                </div>
                <a href="<?= Url::to("/site/request-password-reset"); ?>" class="forgot-pass"><?= Lx::t('frontend', 'forgot your password'); ?></a>
                <div class="register-request">
                    <div class="new-user"><?= Lx::t('frontend', 'new user?'); ?></div>
                    <a href="<?= Url::to("/site/signup"); ?>" class="request-access"><?= Lx::t('frontend', 'request access'); ?></a>
                </div>
        </div>
    </div>
</section>
