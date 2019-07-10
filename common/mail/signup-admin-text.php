<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<?= Yii::t("email/signup", "New User Signup"); ?>

<?= Yii::t("email/signup", "Name:"). ' ' . $user->name; ?>

<?= Yii::t("email/signup", "Email:"). ' ' . $user->email; ?>
