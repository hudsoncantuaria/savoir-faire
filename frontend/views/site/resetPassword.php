<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use common\helpers\CustomHelper;
use lajax\translatemanager\helpers\Language as Lx;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('titles', 'Reset Password | Angdocs');

?>

<section class="register bg pr">
    <div class="overlay-wrapper">
        <div class="overlay-left"></div>
        <div class="overlay-right"></div>
    </div>
    <div class="password-modal--wrapper register-modal--wrapper visible-xs visible-sm">
        <div class="col-xs-12 col-sm-6 col-md-6 nopadding">
            <div class="login-box green">
                <div class="svg-wrapper logo-wrapper border-bottom hidden-xs">
                    <svg class="logo-login center-block" width="196" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 195.31 224.25">
                        <defs>
                            <style></style>
                        </defs>
                        <title></title>
                        <g>
                            <path class="a" d="M25.39,211.83l-2.16,2a6.83,6.83,0,0,0-5-2.23,5.47,5.47,0,0,0-3.92,1.52,5,5,0,0,0-1.59,3.74,5.34,5.34,0,0,0,.7,2.75,4.93,4.93,0,0,0,2,1.89,6,6,0,0,0,2.87.68,6.16,6.16,0,0,0,2.46-.48,8.74,8.74,0,0,0,2.45-1.74l2.1,2.09a11.57,11.57,0,0,1-3.4,2.33,9.7,9.7,0,0,1-3.66.65,8.63,8.63,0,0,1-6.2-2.29,8.33,8.33,0,0,1-1.31-10,8,8,0,0,1,3.15-2.9,9.2,9.2,0,0,1,4.41-1.1,9.62,9.62,0,0,1,3.87.81,9.22,9.22,0,0,1,3.21,2.3m8.13,1.41-2.09,5.49H35.6ZM32,209h3l6,16H37.92l-1.22-3.29H30.35L29.08,225H26Zm12.64,0h2.83L51,220.16,54.6,209h2.83L60,225H57.19l-1.64-10.1L52.3,225H49.74l-3.22-10.1L44.84,225H42Zm21.45,2.94V222h1.45a7.37,7.37,0,0,0,3.11-.48,3.63,3.63,0,0,0,1.58-1.61,5.78,5.78,0,0,0,.61-2.79,5.22,5.22,0,0,0-1.46-4,6,6,0,0,0-4.22-1.26H66.1ZM63,209h3.7a12.44,12.44,0,0,1,5.31.87,6.86,6.86,0,0,1,2.86,2.81,9.63,9.63,0,0,1,.49,7.95,7.06,7.06,0,0,1-1.74,2.57,6.18,6.18,0,0,1-2.4,1.42,17.48,17.48,0,0,1-4.48.39H63Zm23.23,2.61a4.86,4.86,0,0,0-3.62,1.5,5.21,5.21,0,0,0-1.47,3.8A5,5,0,0,0,83,221a5.05,5.05,0,0,0,3.29,1.17,4.81,4.81,0,0,0,3.58-1.52,5.47,5.47,0,0,0,0-7.49,4.85,4.85,0,0,0-3.62-1.53m0-2.89a7.69,7.69,0,0,1,5.66,2.38,7.93,7.93,0,0,1,2.37,5.8A7.85,7.85,0,0,1,92,222.65a8.1,8.1,0,0,1-11.47-.06,8.15,8.15,0,0,1,5.78-13.87m26,3.11-2.16,2a6.82,6.82,0,0,0-5-2.23,5.47,5.47,0,0,0-3.92,1.52,5,5,0,0,0-1.59,3.74,5.36,5.36,0,0,0,.71,2.75,4.93,4.93,0,0,0,2,1.89,6,6,0,0,0,2.87.68,6.16,6.16,0,0,0,2.46-.48,8.76,8.76,0,0,0,2.45-1.74l2.1,2.09a11.57,11.57,0,0,1-3.4,2.33,9.67,9.67,0,0,1-3.65.65,8.63,8.63,0,0,1-6.2-2.29,8.33,8.33,0,0,1-1.31-10,8,8,0,0,1,3.15-2.9,9.18,9.18,0,0,1,4.41-1.1,9.63,9.63,0,0,1,3.87.81,9.22,9.22,0,0,1,3.21,2.3m9.85-.62L120,213.15a3.07,3.07,0,0,0-2.32-1.6,1.4,1.4,0,0,0-.94.31.91.91,0,0,0-.37.7,1.2,1.2,0,0,0,.26.74,20.51,20.51,0,0,0,2.14,2q1.67,1.41,2,1.78A6.27,6.27,0,0,1,122,218.8a4.37,4.37,0,0,1,.37,1.81,4.16,4.16,0,0,1-1.31,3.15,4.73,4.73,0,0,1-3.41,1.24,5,5,0,0,1-2.85-.81,6.35,6.35,0,0,1-2.09-2.55l2.46-1.49q1.11,2.05,2.55,2.05a1.85,1.85,0,0,0,1.26-.45,1.32,1.32,0,0,0,.52-1,1.8,1.8,0,0,0-.39-1.05,11.53,11.53,0,0,0-1.71-1.61,17,17,0,0,1-3.24-3.19,4.06,4.06,0,0,1-.73-2.24,3.68,3.68,0,0,1,1.22-2.77,4.19,4.19,0,0,1,3-1.15,4.69,4.69,0,0,1,2.19.54,8,8,0,0,1,2.25,2M135,218v4.1h.76a4.72,4.72,0,0,0,2.56-.48,1.61,1.61,0,0,0,.66-1.39,2,2,0,0,0-.77-1.63,4.26,4.26,0,0,0-2.57-.6H135Zm0-6.09v3.37h.66a2.47,2.47,0,0,0,1.65-.47,1.59,1.59,0,0,0,.54-1.27,1.49,1.49,0,0,0-.51-1.19,2.35,2.35,0,0,0-1.56-.44ZM132,225V209h2.51a13,13,0,0,1,3.2.29A4.28,4.28,0,0,1,140,210.7a3.74,3.74,0,0,1,.85,2.44,3.53,3.53,0,0,1-.39,1.66,4.19,4.19,0,0,1-1.25,1.4,5,5,0,0,1,2.13,1.71,4.34,4.34,0,0,1,.67,2.43,4.53,4.53,0,0,1-.69,2.46,4.47,4.47,0,0,1-1.79,1.66,6.86,6.86,0,0,1-3,.55Zm12-16h3L151,220.37,155,209h3l-5.62,16h-2.86Zm19,9v4.1h.76a4.7,4.7,0,0,0,2.55-.48,1.61,1.61,0,0,0,.66-1.39,2,2,0,0,0-.77-1.63,4.25,4.25,0,0,0-2.57-.6H163Zm0-6.09v3.37h.66a2.47,2.47,0,0,0,1.65-.47,1.6,1.6,0,0,0,.54-1.27,1.5,1.5,0,0,0-.51-1.19,2.34,2.34,0,0,0-1.56-.44ZM160,225V209h2.51a13,13,0,0,1,3.2.29A4.26,4.26,0,0,1,168,210.7a3.74,3.74,0,0,1,.85,2.44,3.57,3.57,0,0,1-.39,1.66,4.19,4.19,0,0,1-1.25,1.4,5,5,0,0,1,2.12,1.71,4.32,4.32,0,0,1,.68,2.43,4.56,4.56,0,0,1-.69,2.46,4.47,4.47,0,0,1-1.79,1.66,6.88,6.88,0,0,1-3,.55Zm18.52-11.76-2.09,5.49h4.16ZM177,209h3l6,16h-3.08l-1.22-3.29h-6.35L174.08,225H171Z" transform="translate(0 -0.75)"/>
                            <path class="a" d="M149,62l-15.25,14.5Q118.18,60,98.72,60,82.3,60,71.06,71.25T59.81,98.85a40.72,40.72,0,0,0,5,20.29,35.63,35.63,0,0,0,14.09,13.91,41,41,0,0,0,20.24,5.05,41.8,41.8,0,0,0,17.35-3.54q7.86-3.54,17.28-12.87l14.79,15.38q-12.7,12.36-24,17.13A65.64,65.64,0,0,1,98.76,159Q72,159,55,142.11T38,98.81q0-17.09,7.75-30.37a57.57,57.57,0,0,1,22.2-21.36A62.76,62.76,0,0,1,99.07,39,66.15,66.15,0,0,1,149,62" transform="translate(0 -0.75)"/>
                            <path class="a" d="M92.82,85.63v29.65h4.12q6.09,0,8.84-1.4a10.66,10.66,0,0,0,4.49-4.74,17.46,17.46,0,0,0,1.75-8.19q0-7.48-4.16-11.61-3.75-3.71-12-3.71h-3ZM84,77H94.53q10.18,0,15.12,2.54a19.77,19.77,0,0,1,8.15,8.26,29.12,29.12,0,0,1,1.41,23.34,20.76,20.76,0,0,1-4.94,7.56,17.5,17.5,0,0,1-6.83,4.15Q103.75,124,94.69,124H84Z" transform="translate(0 -0.75)"/>
                            <path class="b" d="M98,21.81c-42.56,0-77.18,34.4-77.18,76.69S55.44,175.19,98,175.19s77.18-34.4,77.18-76.69S140.56,21.81,98,21.81M98,177c-43.56,0-79-35.21-79-78.5S54.44,20,98,20s79,35.22,79,78.5S141.56,177,98,177" transform="translate(0 -0.75)"/>
                            <path class="c" d="M98.5,11.81A86.69,86.69,0,1,0,185.19,98.5,86.78,86.78,0,0,0,98.5,11.81M98.5,187A88.5,88.5,0,1,1,187,98.5,88.6,88.6,0,0,1,98.5,187" transform="translate(0 -0.75)"/>
                            <path class="a" d="M97.66,2.56A95.74,95.74,0,1,0,193.5,98.3,95.9,95.9,0,0,0,97.66,2.56m0,193.3A97.55,97.55,0,1,1,195.31,98.3a97.72,97.72,0,0,1-97.65,97.55" transform="translate(0 -0.75)"/>
                        </g>
                    </svg>
                </div>
                <div class="logo-wrapper visible-xs">
                    <img class="img-responsive" src="<?= Yii::getAlias('@theme/images/logo-register.svg'); ?>" title="" alt="">
                </div>
                <div class="login title">
                    <?= Lx::t('frontend', 'Agents for National'); ?><br>
                    <b><?= Lx::t('frontend', 'Shippers Council of Cameroon'); ?></b>
                </div>
                <div class="flex-center">
                    <a class="email-login hidden-xs" href="mailto:angdocs@@angola.scc.com.pt"><?= Lx::t('frontend', 'angdocs@@angola.scc.com.pt'); ?></a>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 nopadding">
            <div class="login-box white">
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

                echo $form->field($model, 'password')->passwordInput(['autofocus' => true]);

                echo Html::submitButton('<span>' . Lx::t('frontend', 'Send') . '</span>
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>', [
                    'class' => 'login-btn'
                ]);

                ActiveForm::end();
                ?>
                <div class="register-request">
                    <a href="<?= Url::to("site/login"); ?>" class="request-access"><?= Lx::t('frontend', 'login access'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="register-modal--wrapper password-modal--wrapper hidden-xs hidden-sm">
        <div class="col-xs-12">
            <figure class="logo-wrapper--register">
                <img src="<?= Yii::getAlias('@theme/images/logo-register.jpg'); ?>" title="Angdocs" alt="Angdocs">
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

            echo $form->field($model, 'password')->passwordInput(['autofocus' => true]);

            echo Html::submitButton('<span>' . Lx::t('frontend', 'Save') . '</span>
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>', [
                'class' => 'login-btn'
            ]);

            ActiveForm::end();
            ?>
        </div>
    </div>
</section>