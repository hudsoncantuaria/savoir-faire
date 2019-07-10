<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */


?>

<div>
    <p><?= Yii::t("email", "Hello"). ' ' . Html::encode($user->name); ?>,</p>
    <p><?= Yii::t("email/signup", "Account successfully registered. We will validate your account. After validation, an email will be sent to your email inbox with the credentials."); ?></p>
</div>
