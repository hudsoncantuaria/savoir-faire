<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<?= Yii::t("email", "Hello") . ' ' . $user->username ?>,

<?= Yii::t("email/signup", "Account successfully registered. We will validate your account. After validation, an email will be sent to your email inbox with the credentials."); ?>
